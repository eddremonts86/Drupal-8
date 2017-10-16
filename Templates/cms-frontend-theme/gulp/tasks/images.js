const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
// const gzip = require('gulp-gzip');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').images;

// Images
gulp.task('images', function(){
    return gulp.src(config.src)
        .pipe(imagemin())
        // .pipe(gzip({}))
        .pipe(gulp.dest(config.dest));
});

// Cleans images to make sure src and dest are sync`d
gulp.task('images-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('images-watcher', function(cb){
    runSequence('images-clean', 'images', 'post-clean', cb);
});
