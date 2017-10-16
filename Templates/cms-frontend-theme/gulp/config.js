/**
 * THEME_NAME
 * set your theme name here an forget about the other settings! ;)
 */
const THEME_NAME = 'steve';

const path = require('path');
const dest = 'dist/themes/' + THEME_NAME;
const src = 'src/themes/' + THEME_NAME;

module.exports = {
    // clean tasks var
    dest: dest,
    // template files
    templates: {
        src: src + '/templates/**/*',
        dest: dest + '/templates'
    },
    // theme files
    theme: {
        // root level files
        root: {
            src: src + '/*',
            dest: dest
        },
        // config folder
        config: {
            src: src + '/config/**/*',
            dest: dest + '/config'
        },
        // extensions
        extensions: {
            src: src + '/src/**/*',
            dest: dest + '/src'
        }
    },
    // styles
    styles: {
        // css reset
        reset: {
            src: [
                'node_modules/normalize.css/normalize.css'
            ]
        },
        // sass files
        sass: {
            // libraries to be included
            dependencies: {
                src: [
                    'node_modules/bootstrap-sass/assets/stylesheets/**/*',
                    'node_modules/flag-icon-css/sass/**/*'
                ],
                dest: [
                    src + '/scss/includes/bootstrap',
                    src + '/scss/includes/flag-icon-css',
                ]
            },
            // process any non partial
            src: src + '/scss/**/*.scss'
        },
        // non-sass external dependencies to be appended to final .css file
        other: {
            src: [
                'node_modules/font-awesome/css/font-awesome.min.css'
            ]
        },
        dest: dest + '/css'
    },
    // images
    images: {
        src: [
            src + '/images/**',
            'node_modules/flag-icon-css/flags/**/*'
        ],
        dest: dest + '/images'
    },
    // scripts
    js: {
        // own scripts
        src: [
            src + '/scripts/countdown.js',
            src + '/scripts/carousel.js'
        ],
        // libraries to be included
        libraries: {
            src: [
                'node_modules/bootstrap-sass/assets/javascripts/**/*',
                // 'node_modules/jquery/dist/jquery.min.js',
                'node_modules/moment/min/moment-with-locales.min.js'
            ],
            bundle: false
        },
        // source maps for included libraries
        srcmap: [],
        dest: dest + '/js'
    },
    // fonts
    fonts: {
        // deps fonts
        external: {
            src: [
                'node_modules/bootstrap-sass/assets/fonts/**/*',
                'node_modules/font-awesome/fonts/**/*'
            ]
        },
        src: [
            src + '/fonts/**/*'
        ],
        dest: dest + '/fonts'
    },
    // static mocks
    static: {
        src: [],
        dest: dest + '/static'
    },
    // lint: {
    //     src: modules
    // },
    // test
    test: {
        smoke: {
            configFile: path.resolve('src/test/unit/karma.conf.js')
        }
    }
};
