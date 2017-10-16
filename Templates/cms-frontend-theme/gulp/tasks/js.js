const gulp = require('gulp');
const gulpif = require('gulp-if');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
// const gzip = require('gulp-gzip');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').js;

// JavaScript process
gulp.task('js', ['js-libraries', 'js-scripts'], function(){
    return;
});

// Own scripts
gulp.task('js-scripts', function(){
    return gulp.src(config.src)
        .pipe(gulpif(global.IS_DEV, sourcemaps.init()))
        .pipe(concat('scripts.min.js'))
        // .pipe(gzip({}))
        // .pipe(gulpif(!global.IS_DEV, global.cachebust.resources()))
        .pipe(gulpif(!global.IS_DEV, uglify({ mangle: false })))
        .pipe(gulpif(global.IS_DEV, sourcemaps.write('./')))
        .pipe(gulp.dest(config.dest));
});

// Libraries
gulp.task('js-libraries', ['js-maps'], function(){
    return gulp.src(config.libraries.src)
        .pipe(gulpif(config.libraries.bundle, concat('libraries.min.js')))
        // .pipe(gzip({}))
        // .pipe(gulpif(!global.IS_DEV, global.cachebust.resources()))
        .pipe(gulp.dest(config.dest));
});

// Libraries source maps
gulp.task('js-maps', function(){
    if( !config.srcmap )
        return; // since this is optional

    return gulp.src(config.srcmap)
        .pipe(gulp.dest(config.dest));
});

// Cleans js to make sure src and dest are sync`d
gulp.task('js-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('js-watcher', function(cb){
    runSequence('js-clean', 'js', 'post-clean', cb);
});
