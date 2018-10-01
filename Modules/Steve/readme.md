`Project Repository Structure` 

  `Global dependencies` 
      
      Production + Developer ENV 
        - Drush 8.*
        - Composer 
            Rum :
                - composer require rollbar/rollbar:~1.5  
                - composer require drupal/console:~1.0    
                - composer require guzzlehttp/guzzle-services    
                - composer require cocur/slugify   
      Developer ENV
            Rum:
                - Drupal Console 
                
      Turn off/on drupal cache          
            drupal site:mode dev   (off)
            drupal site:mode prod  (on)  
          also see this:
            https://www.drupal.org/node/1903374  -- debug twig template page   
      
    `Steve - Modules List` 
    
         Steve-Config&Doc
         
              API URL - PostMan
              General_Project_Idea  
              Installation_Process  
              MarkupHTML  
              Modules -  Auto Generate  
              Templete - Auto Generate  
              Z-Wolves Documentation
         
         Steve-Project
         
              RP-Admin  
              
                SteveAPIConmunication 
                    - rp_api  
                    - rp_repo
                    
                SteveAPis  
                    - rebel_endpoints  
                    - rp_site_api  
                    - rp_user_api
                    
                SteveDataManager
                    - rp_admin_events  
                    - rp_block_data_configuration  
                    - rp_site_generator

                  
              RP-ContentRepo Entity 
                    base
                      - rp_base  
                      - rp_repoapi  
                      - rp_repows

                    content
                      - rp_channel  
                      - rp_competition  
                      - rp_game  
                      - rp_language  
                      - rp_metadata  
                      - rp_participant  
                      - rp_region  
                      - rp_site  
                      - rp_sport  
                      - rp_stream_provider

                    info
                      - rp_cms_site_info  
                      - rp_competition_info  
                      - rp_game_info  
                      - rp_participant_info  
                      - rp_site_info  
                      - rp_sport_info  
                      - rp_stream_provider_info

              RP-ExternalModules  
                      - drush_language  
                      - twig_blocks

              RP-Integration
              
                    SteveModulesIntegration   
                      - rp_ad_block  
                      - rp_cookie  
                      - rp_jsonld_struct  
                      - RP_Layout  
                      - rp_shortcode  
                      - rp_style

                    SteveThemesIntegration                       
                      - rp_client_base  
                      - rp_cms_steve_base_config  
                      - rp_cms_steve_integration_horseracing   
                      - rp_cms_steve_integration_live_fodbald_streams  
                      - rp_cms_steve_watchfooty_config
                      - rp_cms_events   
                      - rp_cms_steve_integration  
                      - rp_cms_steve_integration_live_fodbald  
                      - rp_cms_steve_integration_se_fodbald
         
            
            
  `Steve - Drush commands List`
     
             Import schedule from steve api (MARS Project)            
                rp_importfromapi
                rp_importApiDataByDate [date - 2018-08-15]
                rp_importApiBYDays  [number of days - 5 ]
             
             Import Sites from steve api (MARS Project)   
                rp_importSites  
                rp_importSitesByID [Id]
             
             Import Time zones from steve api (MARS Project)
                rp_importTimeZones
                rp_importTimeZonesByID [Id]
             
             Import Regions from steve api (MARS Project)
                rp_importRegions
                rp_importRegionsByID [Id]
             
             Import Lenguages from steve api (MARS Project)
                rp_importLanguages
                rp_importLanguagesByID [Id]
             
             Import Tournament and Parents from steve api (MARS Project)   
                steve_createTournamentTree [Id]
                
             Import Participants from steve api (MARS Project)   
                steve_createParticipantByID [Id]
             
             Import schedule from steve api (MARS Project)   
                steve_createStreamByID [Id]
                
             Import Channels from steve api (MARS Project)   
                steve_channels
                
                
             Helpper commands   
                steve_jsonld
                rp_updateMainMenu
                rp_updatePaht
                                
             Only DEV-Version   
                steve_importSchedule   
                rp_deleteAll  
                
                
    `REPOSITORY API`        
             
             Generate new Drupal Sites            
                steve_generateSites
                steve_generateSite [siteId - 1]   
                
             User    
                steve_getAllUsers
                steve_getUserbyLocalSite  
                steve_getUserbySite [siteId - 1]  
                          
                steve_importUsers
                steve_importUserBySiteID [siteId - 1]
                steve_importUserContentBySiteID [siteId - 1]
                steve_importUserSiteDefault
                
                steve_updateUsers            
            
             Site 
                steve_importAllSites
                steve_importSiteByID [siteId - 1]
             
             Events    
                steve_importAllEvents  [page - 1]
                steve_importEventsByID [eventid - 7464]
                steve_importEventsRevisions [eventid - 7464]
                steve_importAllEventsTranslaion [eventid - 7464]  [land - ie]
                steve_importEventsTranslaionByID [eventid - 7464]
                
             Sport & Tournaments
                steve_importAllSports   [page - 1]
                steve_importAllSportsbyPage [eventid - 19434]
                steve_importAllSportsTranslations [land- da] [page- 1]
                steve_importAllUpdateSportsTranslationByID  [land - ie] [eventid - 7464]
            
    `Other helper Drush Commands`
           
                drush language-import en [url/path]/en.po  - More info here https://www.drupal.org/project/drush_language
