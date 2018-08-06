`Repository Structure` 

`RP_CMS`
 
        - rp_cms_events ( Provide all content types, taxonomies and configurations)
        - rp_repo ( Import fron STeve API module)
     
`RP_Configuration (All our script)`
        
        - Installation Process 
        - Modules - Auto Generate
        - Templete - Auto Generate
    
`RP_ContentRepo`
        
        -  rp_api
        -  rp_base
        -  rp_channel
        -  rp_cms_site_info
        -  rp_competition
        -  rp_game
        -  rp_game_info
        -  rp_language
        -  rp_participant
        -  rp_region
        -  rp_repoapi
        -  rp_repows
        -  rp_site
        -  rp_site_info
        -  rp_sport
        -  rp_sport_info
        -  rp_stream_provider
        
`RP_ThemesIntegration`

        -  rp_cms_steve_integration
        -  rp_shortcode
        
        
        
 `Steve - Drush commands`
            
            
            rp_importfromapi
            rp_importApiDataByDate [date]
            rp_importApiBYDays  [number of days]
            rp_importSites  
            rp_importSitesByID [siteId]
            rp_importTimeZones
            rp_importTimeZonesByID [Id]
            rp_importRegions
            rp_importRegionsByID [Id]
            rp_importLanguages
            rp_importLanguagesByID [Id]
            rp_importfromrepo
            rp_getschedule
            rp_getschedule
            rp_updateMainMenu
            rp_updatePaht
            rp_deleteAll           
            
            steve_createTournamentTree [Tournament Id]
            steve_createParticipantByID [participantId]
            steve_createStreamByID [streamId]
            steve_channels
            steve_jsonld
            steve_importSchedule  -  only Dev Version
            
 `REPOSITORY API`        
            
         User    
            steve_getAllUsers
            steve_getUserbyLocalSite  
            steve_getUserbySite [siteId]            
            steve_importUsers
            steve_importUserBySiteID [siteId]
            steve_importUserContentBySiteID [siteId]
            steve_importUserSiteDefault
            steve_updateUsers            
        
         Site 
            steve_importAllSites
            steve_importSiteByID [siteId]
         
         Generate new Drupal Sites 
           
            steve_generateSites
            steve_generateSite [siteId]
            
            
        
 `Other helper Drush Commands`
      
            language-import (*.po)
              Examples
                  drush language-import en [url/path]/en.po
