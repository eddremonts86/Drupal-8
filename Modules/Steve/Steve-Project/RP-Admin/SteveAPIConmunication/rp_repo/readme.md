# Steve API - integration
## rp_base

This module is the system importer from Steve API. 
We generate here all the different entities (taxonomies, nodes, pages,...) 
In the same way, the module has a long list of helper classes used by other modules. 


List of Drush commands 
  - rp_importfromapi (Import schedule from Steve Api)
  - rp_importApiDataByDate (Import schedule from Steve Api by date - 2018/05/12 -  )
  - rp_importApiByDays(Import schedule from Steve Api by number of days)
  - rp_deleteAll (Delete all  system entities )
  - rp_updateMainMenu (Update clients Main Menu - used to hide a sport page with out events.)
  - rp_updatePaht (Recreate/update paht url for nodes and taxonomies)
  - rp_importSites (Import all Sites from Steve Api)
  - rp_importSitesByID (Import Site from Steve Api by id )
  - rp_importTimeZones  (Import Time Zones all from Steve Api)
  - rp_importTimeZonesByID (Import Time Zone from Steve Api by id )
  - rp_importRegions  (Import all Regions from Steve Api)
  - rp_importRegionsByID (Import Region from Steve Api by id )
  - rp_importLanguages  (Import all Languages from Steve Api)
  - rp_importLanguagesByID (Import Language from Steve Api by id )
  - rp_channels  (Import all from Steve API)
  - rp_createTournamentTree (Import all the dependencies tree for a tournament. Fron Tournament to Sport. )
  - rp_createParticipantByID (Import Participant from Steve API by id )
  - rp_createStreamByID (Import Streamer from Steve API by id )
  - rp_importSchedule (Delete and Re-import all the info of the system. All nodes and taxonomies and some of the basic configurations. This command is ONLY for developer env )
  - rp_jsonld (Create and import a JSON-LD config Taxonomy)
 
####To more Info rum "drush help" in you terminal
