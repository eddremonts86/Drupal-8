const gulp = require('gulp');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').templates;

// Theme templates files process
gulp.task('templates', function(){
    return gulp.src(config.src)
        .pipe(gulp.dest(config.dest));
});

// Cleans templates to make sure src and dest are sync`d
gulp.task('templates-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('templates-watcher', function(cb){
    runSequence('templates-clean', 'templates', 'post-clean', cb);
});
