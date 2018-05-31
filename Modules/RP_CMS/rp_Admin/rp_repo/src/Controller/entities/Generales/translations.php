<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:13 PM
 */

namespace Drupal\rp_repo\Controller\entities\Generales;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\Core\Language\LanguageInterface;

class translations extends ControllerBase
{
    public function createTaxonomyTranslation()
    {
        $language = \Drupal::languageManager()->getLanguages();
        $defaultLanguage = \Drupal::languageManager()->getDefaultLanguage()->getId();
        $query = \Drupal::entityQuery('taxonomy_term');
        $ids = $query->execute();
        foreach ($language as $land) {
            foreach ($ids as $id) {
                $l = $land->getId();
                if ($l != $defaultLanguage) {
                    $term = Term::load($id);
                    $translated_fields = $term;
                    $translated_fields = $translated_fields->toArray();
                    $include = ['sport', 'participant', 'stream_provider'];
                    $vid = $translated_fields['vid'][0]['target_id'];
                    if (in_array($vid, $include)) {
                        if (!$term->hasTranslation($l)) {
                            $translated_entity_array = array_merge($term->toArray(), ['name' => $term->name->value . ' - ' . $l]);
                            $term->addTranslation($l, $translated_entity_array)->save();
                            print "Making translation to " . $l . " lang";
                        }
                    }
                }
            }
        }

        return true;
    }

    public function createNodeTranslation($node, $region, $properties = [], $alias)
    {
        $defLand = \Drupal::languageManager()->getDefaultLanguage()->getId();
        if (!$node->hasTranslation($region) and $region != $defLand) {
            var_dump("Making translation to " . $region . "lang");
            $node_translate = $node;
            if (!empty($properties)) {
                $newTranslation = [
                    'title' => $node->getTitle() . ' -' . $region,
                    'field_events_properties' => $properties
                ];
                $translated_entity_array = array_merge($node->toArray(), $newTranslation);
                $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
                return true;
            } else {
                $newTranslation = [
                    'title' => $node->getTitle() . ' -' . $region
                ];
                $translated_entity_array = array_merge($node->toArray(), $newTranslation);
                $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
                return true;
            }
        }
    }

    public function createTranslation($node, $region, $properties = [], $alias)
    {
        $defLand = \Drupal::languageManager()->getDefaultLanguage()->getId();
        if (!$node->hasTranslation($region) and $region != $defLand) {
            var_dump("Making translation to " . $region . "lang");
            $node_translate = $node;
            if (!empty($properties)) {
                $newTranslation = [
                    'title' => $node->getTitle() . ' -' . $region,
                    'field_events_properties' => $properties
                ];
                $translated_entity_array = array_merge($node->toArray(), $newTranslation);
                $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
                return true;
            } else {
                $newTranslation = [
                    'title' => $node->getTitle() . ' -' . $region
                ];
                $translated_entity_array = array_merge($node->toArray(), $newTranslation);
                $node_translate->addTranslation(strval($region), $translated_entity_array)->save();
                return true;
            }
        }
        return true;
    }

    public function generateAliasbyTrasnlations()
    {
        $language = \Drupal::languageManager()->getLanguages();
        $defaultLanguage = \Drupal::languageManager()->getDefaultLanguage()->getId();
        $query = \Drupal::entityQuery('taxonomy_term');
        $ids = $query->execute();
        foreach ($language as $land) {
            foreach ($ids as $id) {
                $l = $land->getId();
                if ($l != $defaultLanguage) {
                    $term = Term::load($id);
                    $translated_fields = $term;
                    $translated_fields = $translated_fields->toArray();
                    $include = ['sport', 'participant', 'stream_provider'];
                    $vid = $translated_fields['vid'][0]['target_id'];
                    if (in_array($vid, $include)) {
                        if (!$term->hasTranslation($l)) {
                            var_dump("Making translation to " . $l . " lang");
                            $translated_entity_array = array_merge($term->toArray(), ['name' => $term->name->value . ' - ' . $l]);
                            $term->addTranslation($l, $translated_entity_array)->save();
                        }
                    }
                }
            }
        }

        return true;
    }

}
