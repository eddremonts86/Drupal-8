# Steve API - Sport and Tournaments API

## Export Proccess
### Endpoint URLs
  - [http://system]/steveApi/sports/getAll  (Get Sports and tournaments)
  - [http://system]/steveApi/sports/getAll/{page}  (Get Sports and tournaments by page)
  - [http://system]/steveAPI/sports/getSportByid/{eventid}  (Get Sports and tournaments by Sport/Tournament Steve Id )
  - [http://system]/steveAPI/sports/getSportTranslationByid/{eventid}/{lang}  (Get Sports and tournaments Translation by Sport/Tournament Steve Id and lang)
  - [http://system]/steveAPI/sports/getSportTranslations/{lang}/{page}  (Get all Sports and tournaments Translation by lang  and  page)
  
  ####Variables
   - date (2018/08/12) 
   - eventid (5616431.sport_1)
   - page (0,1,2)
   - lang (en,da)
  
## Import Proccess
### Drush Commands
  - steve_importAllSports  (Import Sports and tournaments)
  - steve_importAllSportsbyPage (Import Sports and tournaments by page)
  - steve_importSportbByID (Import  Sports and tournaments by Sport/Tournament Steve Id )
  - steve_importAllSportsTranslations (Import  Sports and tournaments Translation by Sport/Tournament Steve Id and lang)
  - steve_importAllUpdateSportsTranslationByID (Import all Sports and tournaments Translation by lang  and  page)
  
####To more Info rum "drush help" in you terminal
