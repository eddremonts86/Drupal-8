# Steve API - Drupal Evenets API

## Export Proccess
### Endpoint URLs
  - [http://system]/steveAPI/events/{date}/{page} (Get Events by date and page)
  - [http://system]/steveAPI/events/{date}  (Get Events by date)
  - [http://system]/steveAPI/event/{eventid} (Get Events by event Steve API ID)
  - [http://system]/steveAPI/eventreviews/{eventid} (Get Events reviews by event Steve API ID)
  - [http://system]/steveAPI/eventsTranslaion/{date}/{page}/{lang} (Get Events Translation by date ,page and lang)
  - [http://system]/steveAPI/eventsTranslaion/{eventid}/{lang} (Get Events Translation by event Steve API ID and lang)
  ####Variables
   - date (2018/08/12) 
   - eventid (5616431.sport_1)
   - page (0,1,2)
   - lang (en,da)
  
## Import Proccess
### Drush Commands
  - steve_importAllEvents  (Import Events by date and page)
  - steve_importEventsByID (Import Events by event id)
  - steve_importEventsRevisions (Import  Events by event Steve API ID)
  - steve_importAllEventsTranslaion (Import  Events Translation by date ,page and lang)
  - steve_importEventsTranslaionByID (Import Events Translation by event Steve API ID and lang)
  
####To more Info rum "drush help" in you terminal
