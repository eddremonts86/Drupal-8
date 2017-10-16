RP CMS - Repo
-
This module have all the programming logic for connect to the API services 
between the client (sespor.dk) and  the API Server, also between the client 
and the Content Repo.

This Module needs: 
- Drupal Modules 	
    - RP API	
    - RP CMS - Tax Sport
    - RP CMS - Channels	
    - RP CMS - Game Pages		
    - RP CMS - Tournament Page
    - RP CMS - Participants Content Page	
    - RP CMS - Sport Internal Pages	
    - RP CMS - Sport Pages	
    - RP CMS - Stream Provider
- External Library 
    - Guzzle HTTP - Guzzle Services    
- One time is enable.
    - Drush Commands: 
        - drush rp_importfromapi 
        - drush rp_importfromrepo
        - drush rp_getschedule [ sport id ]
    