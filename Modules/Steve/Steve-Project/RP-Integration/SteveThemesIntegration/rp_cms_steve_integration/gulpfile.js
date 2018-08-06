/*-----------------------Modules Require---------------------------------*/
var gulp = require('gulp');
const imagemin = require('gulp-imagemin');
var cssmin = require('gulp-cssmin');
var rename = require('gulp-rename');
var scss = require("gulp-sass");
var build = require('gulp-build');
var jsmin = require('gulp-jsmin');
/*-----------------------Source Variables---------------------------------*/
var sccs = 'src/scss/*.scss';
var css = 'src/css/**/*.css';
var css_path = 'src/css';
var js = 'src/js';
var img = 'src/images/*';

/*--------------------------Automatic Tasks------------------------------*/
gulp.task('watch', function () {
    gulp.watch(sccs, ['default']);
});

gulp.task('default', [
    'getSCSS',
    'compressJS',
    'minIMG',
    'minSCSS',
    'watch',
], function () {});
/*--------------------------Primary Tasks------------------------------*/

gulp.task("getSCSS", function () {
    gulp.src(sccs)
        .pipe(scss({"bundleExec": true}))
        //.pipe(gulp.dest(sccs))
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(css_path));
});
gulp.task("minSCSS", function () {
   /* gulp.src(css)
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(css_path));*/
});
gulp.task('compressJS', function (cb) {
    gulp.src(js)
        .pipe(jsmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(js));
});
/*------------------------Other Task --------------------------------*/
gulp.task('minIMG', function () {
    gulp.src(img)
        .pipe(imagemin())
        .pipe(gulp.dest(img));
});
