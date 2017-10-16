const gulp = require('gulp');
const runSequence = require('run-sequence');
// const CacheBuster = require('gulp-cachebust');
// global.cachebust = new CacheBuster();

// Build for production
gulp.task('production', function(cb){
    runSequence(['clean'], ['styles', 'images', 'js', 'templates', 'theme', 'fonts'], ['post-clean'], cb);
});
