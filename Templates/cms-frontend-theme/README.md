# Generic Theme Builder for Rebel Penguin

## Description
Generic Theme Builder (from now GTB) is a project that will allow any Rebel Penguin frontend developer to work fluently with a solid dev environment, so just creating a theme under src/themes and setting up gulp config to work with such theme will create a distributable version of the theme that can be moved or soft linked to any working drupal installation.

## Install

#### Prerequisites
Since dev must install any dependencies using [npm](https://www.npmjs.com/), nodejs must be installed. Download it from [here](https://nodejs.org/en/download/current/) or install it through your OS's package manager (recommended) following steps at [here](https://nodejs.org/en/download/package-manager).

#### Project installation
From the root of this project, you might notice that package.json file is present, running
```javascript
npm install
```
will fetch and install all the modules required. This might take a while depending on your system.

## Working with GTB

#### Themes
Under `src/themes` dev must work on its Drupal 8 theme just like it would be working on `{drupal-installation}/themes` folder. That folder will be processed and built into `{gtb-root}/dist/themes` so just setting up a soft link or shortcut pointing built theme(s) folder(s) into `{drupal-installation}/themes` folder will allow drupal to consume the theme.

#### Configuration
In order to configure GTB to create a distributable version of your theme, edit `{gtb-root}/gulp/config.js` and set your theme name in `THEME_NAME` constant. This migth be done likely once, and value must be replaced if moving to work in a different theme. Of course, `THEME_NAME` must be the same string as theme folder name.

#### Developing with GTB
GTB can set a list of watchers to update dist files conveniently while dev is working on a theme. To deploy a dev environment just use
```javascript
npm run dev
```
and development mode deployment will take place. In this mode, dist code will be updated on any change on the src files.
Dev deployment contains some really useful features like source maps and static HTML mocks enabled for the earlier phases of theme development.

#### Production deployment with GTB
In order to deploy for production, just use
```javascript
npm run build
```
and dist will contain a production distributable of your theme, where assets are minified and no source maps are included.

NOTE: gzip is disabled for now since I'm not aware about the guidelines you guys are following for it.

## Contact
Any issues arising while using GTB? No problem! Just find me at:
- email **arm4design@gmail.com**
- skype **arm4design**

## TODO
- implement smoke tests
- add support for Drupal clean cache from watchers
- implement live reload
- lint implementation
- CI implementation
- add support for bundling any drupal required file outside the theme itself
