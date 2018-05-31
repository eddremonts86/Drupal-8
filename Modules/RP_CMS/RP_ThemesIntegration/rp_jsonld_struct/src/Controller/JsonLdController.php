<?php

namespace Drupal\rp_jsonld_struct\Controller;

use \Datetime;
use Drupal\Core\Url;
use Drupal\rp_client_base\Controller\SteveFrontendControler;


class JsonLdController extends SteveFrontendControler
{

    public function getJsonLd()
    {        
        $entity = NULL;
        $route  = \Drupal::routeMatch();
        $routeName = $route->getRouteName();
        $front = \Drupal::service('path.matcher')->isFrontPage();
       
        if ($term = $this->getTaxonomyTermByUrl()) {
            $entity = $term;
        } else if ($node = $this->getNodeByUrl(1)) {
            $entity = $node;
        }

        if ($entity->field_jsonld_struct && $entity->field_jsonld_struct->target_id) {

            $termId = $entity->field_jsonld_struct->target_id;
            $taxonomyTerm = $this->getTaxonomyByCriterio(['tid' => $termId, 'vid' => 'jsonld_']);

            switch ($taxonomyTerm->name->value) {
                case 'Events':
                    // Related to node type Events
                    $jsonld = $this->getEventsJsonld($entity);
                    break;
                case 'Leages':
                    // Related to Sports and Tournaments taxonomy vocabulary
                    $jsonld = $this->getLeaguesJsonld($entity);
                    break;
                case 'Participants':
                    // Related to Participant taxonomy vocabulary
                    $jsonld = $this->getParticipantsJsonld($entity);
                    break;
                case 'Reviews':
                    // $jsonld = $this->getReviewsJsonld($entity);
                    break;
                case 'Blog':
                    // Related to node type Sport blog
                    $jsonld = $this->getSportJsonld($entity);
                    break;
                case 'Site':
                    // Default values
                    $jsonld = $this->getSiteJsonld($entity);
                    break;
                case 'Sport':
                    // Related to node type Sport
                    $jsonld = $this->getSportJsonld($entity);
                    break;
                case 'Streams':
                    // Related to Stream Provider taxonomy vocabulary
                    // Related to node type Sport Stream Reviews
                    $jsonld = $this->getStreamsJsonld($entity);
                    break;
            }

        } else if (@$entity->field_jsonld_struct) {
            //Default values if field is empty
            $jsonld = $this->getSiteJsonld($entity);
        } else if ($front) {
            // Values for the front page
            $jsonld = $this->getFrontJsonld($entity);
        }else if ($routeName == 'rp_cms_steve_integration.live_stream_reviews') {
            //Related to Live Stream Reviews page
            $jsonld = $this->getLiveStreamReviewsJsonld();
        } else if ($entity) {
            // Need to provide default values for Articles and Page
            $jsonld = $this->getDefaultJsonld($entity);
        } else {
            return NULL;
        }
        return json_encode($jsonld);
    }

    private function getEventsJsonld($entity)
    {
        $event = $this->getEventParticipants();
        $sport = $this->getSport();
        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "SportsEvent",
            "name" => $event['events']['eventName'],
            "description" => $sport['sportName'],
            "startDate" => date(DateTime::ISO8601, $event['events']['eventDate']),
            "endDate" => date(DateTime::ISO8601, $event['events']['eventDate']),
            "image" => file_create_url($this->getPageBackground()),
            // Replace with actual location (new field required)
            "location" => [
                "@type" => "Place",
                "name" => "No information",
                "address" => "No information"
            ],
            "url" => Url::fromRoute('entity.node.canonical', ['node' => $entity->id()], ['absolute' => TRUE])->toString()
        ];
        foreach ($event['events']['Participants'] as $participant) {
            $jsonld['competitor'][] = [
                "@type" => "SportsTeam",
                "name" => $participant['name'],
                "logo" => file_create_url($participant['logo'])
            ];
        }

        return $jsonld;
    }

    private function getLeaguesJsonld($entity)
    {
        $events = $this->getSchedulePerTournament(25,$entity->id());
        $description = substr(strip_tags($this->getShortcode($entity->description->value)), 0, 250);
        $description = $description ? $description . '...' : 'No information';
        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "SportsEvent",
            "name" => $entity->name->value,
            //Replace with the actual start date
            "startDate" => date(DateTime::ISO8601),
            "endDate" => date(DateTime::ISO8601),
            // Replace with actual location (new field required)
            "description" => $description,
            "location" => [
                "@type" => "Place",
                "name" => "No information",
                "address" => "No information"
            ],
            "url" => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $entity->id()], ['absolute' => TRUE])->toString()
        ];
        if ($entity->field_logo->target_id) {
            $uri = $this->getImgUrl($entity->field_logo->target_id);
            $jsonld['image'] = file_create_url($uri);
        }
        if (isset($events)) {
            $count = 0;
            foreach ($events as $index => $event) {
                if ($count < 15) {
                    $count++;
                    $nodeId = $event->nid->value;
                    $jsonld['subEvent'][] = [
                        "@type" => "SportsEvent",
                        "name" => $event->title->value,
                        "startDate" => date(DateTime::ISO8601, $event->field_event_date->value),
                        "endDate" => date(DateTime::ISO8601, $event->field_event_date->value),
                        "image" => file_create_url($this->getPageBackgroundByNodeID($nodeId)),
                        "description" => "No information",
                        "location" => [
                            "@type" => "Place",
                            "name" => "No information",
                            "address" => "No information",
                        ],
                        "url" => Url::fromRoute('entity.node.canonical', ['node' => $nodeId], ['absolute' => TRUE])->toString()
                    ];
                    $position = count($jsonld['subEvent']) - 1;
                    $participants = $this->getEventParticipantsByNodeId($nodeId);
                    foreach ($participants['events']['Participants'] as $participant) {
                        $jsonld['subEvent'][$position]['competitor'][] = [
                            "@type" => "SportsTeam",
                            "name" => $participant['name'],
                            "logo" => file_create_url($participant['logo'])
                        ];
                    }
                }
            }
        }
        return $jsonld;
    }

    private function getParticipantsJsonld($entity)
    {
        $params = ['tid' => $entity->field_participant_sport->target_id, 'vid' => 'sport'];
        $sportObj = $this->getTaxonomyByCriterio($params);

        $jsonld = [
            '@context' => 'http://schema.org',
            '@type' => 'SportsTeam',
            'name' => $entity->name->value,
            'sport' => $sportObj->name->value,
            "url" => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $entity->id()], ['absolute' => TRUE])->toString()
        ];

        return $jsonld;
    }

    private function getReviewsJsonld($entity)
    {
        $jsonld = [];

        return $jsonld;
    }

    private function getSiteJsonld($entity)
    {
        $entityType = $entity->getEntityTypeId();

        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "WebPage"
        ];

        if ($entityType == "taxonomy_term") {
            $taxonomy_description = substr(strip_tags($this->getShortcode($entity->description->value)), 0, 250);
            $taxonomy_description = $taxonomy_description ? $taxonomy_description . '...' : 'No information';
            $jsonld['name'] = $entity->name->value;
            $jsonld['description'] = $taxonomy_description;
        } else if ($entityType == "node") {
            $node_description = substr(strip_tags($this->getShortcode($entity->body->value)), 0, 250);
            $node_description = $node_description ? $node_description . '...' : 'No information';
            $jsonld['name'] = $entity->title->value;
            $jsonld['description'] = $node_description;
        }

        return $jsonld;
    }

    private function getSportJsonld($entity)
    {
        $sportObj = $this->getSport();
        $events = $this->getSchedule(0, 3);

        $node_description = substr(strip_tags($this->getShortcode($entity->body->value)), 0, 250);
        $description = $node_description ? $node_description . '...' : 'No information';

        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "SportsEvent",
            "name" => $sportObj['sportName'],
            "url" => Url::fromRoute('entity.node.canonical', ['node' => $entity->id()], ['absolute' => TRUE])->toString(),
            "startDate" => date(DateTime::ISO8601),
            "endDate" => date(DateTime::ISO8601),
            "location" => [
                "@type" => "Place",
                "name" => "No information",
                "address" => "No information",
            ],
            "description" => $description,
            "offers" => [
                "@type" => "Offer",
                "price" => "00.00",
                "priceCurrency" => "USD",
                "validFrom" => date(DateTime::ISO8601),
                "availability" => "No information",
                "url" => "https://google.com"
            ],
            "performer" => "No information"
        ];
        if ($sportObj['sportBackground']) {
            $jsonld['image'] = file_create_url($sportObj['sportBackground']);
        } else {
            $jsonld['image'] = \Drupal::request()->getSchemeAndHttpHost() . "/themes/custom/stevethemebase/src/images/fodbold-desktop-banner.jpg";
        }
        foreach ($events as $event) {
            $nodeId = $event["nodeId"];
            $jsonld['subEvent'][] = [
                "@type" => "SportsEvent",
                "name" => $event["title"],
                "startDate" => date(DateTime::ISO8601, $event["eventDate"]),
                "endDate" => date(DateTime::ISO8601, $event["eventDate"]),
                "image" => file_create_url($this->getPageBackgroundByNodeID($nodeId)),
                "description" => "No information",
                "location" => [
                    "@type" => "Place",
                    "name" => "No information",
                    "address" => "No information",
                ],
                "url" => Url::fromRoute('entity.node.canonical', ['node' => $nodeId], ['absolute' => TRUE])->toString()
            ];
            $position = count($jsonld['subEvent']) - 1;
            $participants = $this->getEventParticipantsByNodeId($nodeId);
            foreach ($participants['events']['Participants'] as $participant) {
                $jsonld['subEvent'][$position]['competitor'][] = [
                    "@type" => "SportsTeam",
                    "name" => $participant['name'],
                    "logo" => file_create_url($participant['logo'])
                ];
            }

        }

        return $jsonld;
    }

    private function getStreamsJsonld($entity)
    {

        if($entity->getEntityTypeId() == "node" && $entity->type->target_id == "sport_stream_reviews"){
            $events = $this->getSchedule(5);
            $jsonld = [
                "@context" => "http://schema.org",
            ];

            foreach ($events as $event) {
                $data = [
                    "@type" => "SportsEvent",
                    "name" => $event['title'],
                    "startDate" => date(DateTime::ISO8601, $event['eventDate']),
                    //Replace with the actual end date
                    "endDate" => date(DateTime::ISO8601, $event['eventDate']),
                    "description" => $event['sportname'],
                    // Replace with actual location (new field required)
                    "location" => [
                        "@type" => "Place",
                        "name" => "No information",
                        "address" => "No information"
                    ],
                    "offers" => [
                        "@type" => "Offer",
                        "price" => "00.00",
                        "priceCurrency" => "USD",
                        "validFrom" => date(DateTime::ISO8601, $event['eventDate']),
                        "availability" => "No information",
                        "url" => "https://google.com"
                    ],
                    "performer" => $event['eventTournamentName'],
                    "url" => Url::fromRoute('entity.node.canonical', ['node' => $event['nodeId']], ['absolute' => TRUE])->toString()
                ];

                if ($entity->field_events_bg->target_id) {
                    $uri = $this->getImgUrl($entity->field_events_bg->target_id);
                    $data['image'] = file_create_url($uri);
                } else {
                    $data['image'] = \Drupal::request()->getSchemeAndHttpHost() . "/themes/custom/stevethemebase/src/images/fodbold-desktop-banner.jpg";
                }
                foreach ($event['eventStreams'] as $stream) {
                    $data['subEvent'][] = [
                        "@type" => "BroadcastEvent",
                        "name" => $stream['streamName'],
                        "publishedOn" => [
                            "@type" => "BroadcastService",
                            "url" => $stream['endLink']
                        ],
                        "startDate" => date(DateTime::ISO8601, $event['eventDate']),
                    ];
                }

                $jsonld["@graph"][] = $data;
            }
        }else if($entity->getEntityTypeId() == "taxonomy_term" && $entity->getVocabularyId() == "stream_provider"){
            $StreamProvider = $this->getStreamProviderFormat();
            $jsonld = [
                "@context" => "http://schema.org",
                "@type" => "Review",
                "itemReviewed" => [
                    "@type" => "Thing",
                    "name" => $entity->name->value,
                    "url" => $StreamProvider['url']
                ],
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => $StreamProvider['Provider6Stars']
                ],
                "reviewBody" => $StreamProvider['Provider6Content'],
                "author" => \Drupal::config('system.site')->get('mail'),
                "url" => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $entity->id()], ['absolute' => TRUE])->toString(),
            ];
            if ($entity->field_streamprovider_logo->target_id) {
                $uri = $this->getImgUrl($entity->field_streamprovider_logo->target_id);
                $jsonld['image'] = file_create_url($uri);
            }


        }
        return $jsonld;
    }

    private function getFrontJsonld($entity)
    {
        $config = \Drupal::config('system.site');
        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => $config->get('name'),
            "description" => $config->get('slogan')
        ];

        return $jsonld;
    }

    private function getDefaultJsonld($entity)
    {
        $description = substr(strip_tags($this->getShortcode($entity->body->value)), 0, 250) . '...';
        $jsonld = [
            "@context" => "http://schema.org",
            "@type" => "NewsArticle",
            "description" => $description ? $description : 'No information',
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            ],
            "headline" => $entity->title->value,
            "image" => [
                $this->getImageUrlPlusDefault($entity->field_image->target_id),
            ],
            "datePublished" => date(DateTime::ISO8601, $entity->created->value),
            "dateModified" => date(DateTime::ISO8601, $entity->changed->value),
            "author" => [
                "@type" => "Person",
                "name" => $this->getAutor($entity->nid->value)
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => "Gaming Innovation Group - GIG",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "http://" . $_SERVER['HTTP_HOST'] . "/themes/custom/stevethemebase/src/images/gig.png"
                ]
            ]
        ];

        return $jsonld;
    }

    private function getLiveStreamReviewsJsonld(){
        $data = [];
        $streams = $this->getLiveStreamReviewsFormat();
        $jsonld = [
            "@context" => "http://schema.org",
        ];

        foreach ($streams as $stream) {
            $jsonld["@graph"][] = [
                "@type" => "Review",
                "itemReviewed" => [
                    "@type" => "Thing",
                    "name" => $stream['name']
                ],
                "reviewBody" => $stream['description'],
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => $stream['rating']
                ],
                "author" => \Drupal::config('system.site')->get('mail'),
                "url" => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $stream['streamId']], ['absolute' => TRUE])->toString()
            ];
        }

        return $jsonld;
    }
}