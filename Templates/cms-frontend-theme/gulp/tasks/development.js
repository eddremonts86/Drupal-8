const gulp = require('gulp');
const runSequence = require('run-sequence');
// const CacheBuster = require('gulp-cachebust');
// global.cachebust = new CacheBuster();

// dev build
gulp.task('development', function(cb){
    global.IS_DEV = true;
    runSequence(['clean'], ['styles', 'images', 'js', 'templates', 'theme', 'fonts'], ['post-clean'], ['watch'], cb);
});
