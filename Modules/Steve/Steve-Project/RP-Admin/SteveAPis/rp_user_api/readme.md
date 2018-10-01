#Steve API - Drupal User API

## Export Proccess
### Endpoint URLs
  - [http://system]/steveAPI/getUsers (Get All Users)
  - [http://system]/steveAPI/getUser/{SiteId}  (Get User by Steve SITE ID)
  - [http://system]/steveAPI/getUserContent/{SiteId} (Get User content by Steve SITE ID)
  - [http://system]/steveAPI/updateUser/{SiteId} (Get All User content by Steve SITE ID)
      
  ####User Login API - From User controllers site 
  - [http://system]/steveAPI/{userToken}/{siteToken} (Get User and Site Tokens)
  - [http://system]/steveAPI/getAPIUserbyTonkesandSite/{site}/{userToken}/{siteToken} (Get SiteID and User and Site Tokens)
   
 ####Variables    
  - SiteId (2,8)
  - userToken (4cb97bdb-6cb5-4a4b-be43-c1150dd03732)   
  - siteToken (6cb5-4a4b-4cb97bdb-be43-c1150dd03732)
  
## Import Proccess
### Drush Commands
  
  - steve_importUsers  (Get All Users)
  - steve_updateUsers  (Get All User content by Steve SITE ID)  
  - steve_importUserBySiteID (Get User by Steve SITE ID)
  - steve_importUserSiteDefault (Get User - where Steve SITE ID is taken from drupal configuration site id )  
  - steve_importUserContentBySiteID (Get User by Steve SITE ID)
  
  ####To more Info rum "drush help" in you terminal
