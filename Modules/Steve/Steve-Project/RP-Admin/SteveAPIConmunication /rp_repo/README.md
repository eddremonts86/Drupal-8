RP CMS - Repo
-
This module have all the programming logic for connect to the API services 
between the client (sespor.dk) and  the API Server, also between the client 
and the Content Repo.

    This Module needs: 
    
    - Drupal Modules 	
        - RP API	
        - RP CMS - Events 
        - rp_base
        - rp_repoapi
        - rp_site
        - rp_site_info
        - rp_cms_site_info
         
    - External Library 
        - Guzzle HTTP - Guzzle HTTP Services. See doc here https://github.com/guzzle/guzzle
        - Slugify - Clear text. See doc here https://github.com/cocur/slugify 
          
    - One time is enable.
        - Drush Commands (examples): 
          > Most Important Commands <;)   
            - drush help  
            - drush cr all 
             
          > API Import Commands   
            - drush rp_importfromapi (Import data from STEVE-API) 
            - drush rp_importApiDataByDate 7 (Import data from STEVE-API as many day you want)
            - drush rp_importApiBYDays 2015-12-25 (Import data from STEVE-API by specify day)
            - drush rp_deleteAll (Delete all content and taxonomies)
            
          > Content Repo Import Commands
            - drush rp_importfromrepo (Import and unify data from Content Repo)
            
          > Steve CMS interation Commands
            - drush rp_getschedule 1  ( A help command to see if we have any data for specify sport)           
            - drush rp_updateMainMenu ( desable menu items(sport) if don't have any event )
            - drush rp_updatePaht (Recreate url using PathAuto module)
            
           