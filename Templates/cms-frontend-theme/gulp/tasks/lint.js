const gulp = require('gulp');
const eslint = require('gulp-eslint');
const config = require('../config').lint;

gulp.task('lint', function(){
    return gulp.src(config.src)
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});
