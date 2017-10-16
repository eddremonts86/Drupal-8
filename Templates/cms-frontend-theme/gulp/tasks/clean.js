const gulp = require('gulp');
const del = require('del');
const config = require('../config');

// clean dist
gulp.task('clean', function(){
    return del(config.dest);
});

// clean residual folders in dist
gulp.task('post-clean', function(){
    return del(config.dest + '/scss');
});
