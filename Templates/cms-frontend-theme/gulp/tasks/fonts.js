const gulp = require('gulp');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').fonts;

// Fonts
gulp.task('fonts', ['fonts-external'], function(){
    return gulp.src(config.src)
        .pipe(gulp.dest(config.dest));
});

// External fonts aka fonts from node_modules deps
gulp.task('fonts-external', function(){
    return gulp.src(config.external.src)
        .pipe(gulp.dest(config.dest));
});

// Cleans fonts to make sure src and dest are sync`d
gulp.task('fonts-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('fonts-watcher', function(cb){
    runSequence('fonts-clean', 'fonts', 'post-clean', cb);
});
