const gulp = require('gulp');
const del = require('del');
const runSequence = require('run-sequence');
const theme = require('../config').theme;

// Theme files
gulp.task('theme', ['theme-root', 'theme-config', 'theme-extensions'], function(){
    return;
});

// Theme root files
gulp.task('theme-root', function(){
    return gulp.src(theme.root.src)
        .pipe(gulp.dest(theme.root.dest));
});

// Theme root files clean
gulp.task('theme-root-clean', function(){
    return del(theme.root.dest + '/*.*');
});

// Theme root watcher function
gulp.task('theme-root-watcher', function(cb){
    runSequence('theme-root-clean', 'theme-root', 'post-clean', cb);
});

// Theme config files
gulp.task('theme-config', function(){
    return gulp.src(theme.config.src)
        .pipe(gulp.dest(theme.config.dest));
});

// Theme config files clean
gulp.task('theme-config-clean', function(){
    return del(theme.config.dest);
});

// Theme config watcher function
gulp.task('theme-config-watcher', function(cb){
    runSequence('theme-config-clean', 'theme-config', 'post-clean', cb);
});

// Theme extensions files
gulp.task('theme-extensions', function(){
    return gulp.src(theme.extensions.src)
        .pipe(gulp.dest(theme.extensions.dest));
});

// Theme extensions files clean
gulp.task('theme-extensions-clean', function(){
    return del(theme.extensions.dest);
});

// Theme extensions watcher function
gulp.task('theme-extensions-watcher', function(cb){
    runSequence('theme-extensions-clean', 'theme-extensions', 'post-clean', cb);
});
