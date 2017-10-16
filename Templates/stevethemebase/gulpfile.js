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
var sccs_dev = 'src/css/dev/';
var css_dev = 'src/css/dev/*.css';
var css_live = 'src/css/live';
var js_dev = 'src/js/dev/*.js';
var js_live = 'src/js/live';
var img_dev = 'src/imgs/dev/*';
var img_live = 'src/imgs/live';

/*--------------------------Automatic Tasks------------------------------*/
gulp.task('watch', function () {
    gulp.watch(sccs, ['default']);
});

gulp.task('default', [
    'getSCSS',
    'compressJS',
    'minIMG',
    'minSCSS'
], function () {});
/*--------------------------Primary Tasks------------------------------*/

gulp.task("getSCSS", function () {
    gulp.src(sccs)
        .pipe(scss({"bundleExec": true}))
        .pipe(gulp.dest(sccs_dev))
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(css_live));
});
gulp.task("minSCSS", function () {
    gulp.src(css_dev)
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(css_live));
});
gulp.task('compressJS', function (cb) {
    gulp.src(js_dev)
        .pipe(jsmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(js_live));
});
/*------------------------Other Task --------------------------------*/
gulp.task('minIMG', function () {
    gulp.src(img_dev)
        .pipe(imagemin())
        .pipe(gulp.dest(img_live));
});
