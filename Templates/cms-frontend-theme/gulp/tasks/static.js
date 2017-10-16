const gulp = require('gulp');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').static;

// Static files process
gulp.task('static', function(){
    return gulp.src(config.src)
        .pipe(gulp.dest(config.dest));
});

// Cleans static folder to make sure src and dest are sync`d
gulp.task('static-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('static-watcher', function(cb){
    runSequence('static-clean', 'static', 'post-clean', cb);
});
