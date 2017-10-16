const gulp = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const gulpif = require('gulp-if');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglifycss');
// const gzip = require('gulp-gzip');
const del = require('del');
const runSequence = require('run-sequence');
const config = require('../config').styles;

// Styles
gulp.task('styles', ['css-reset', 'css-sass', 'css-external'], function(){
    return;
});

// External non-sass styles
gulp.task('css-external', function(){
    return gulp.src(config.other.src)
        .pipe(concat('external.css'))
        // .pipe(gzip({}))
        // .pipe(uglify())
        .pipe(gulp.dest(config.dest));
});

// Sass
gulp.task('css-sass', ['css-sass-deps'], function(){
    return gulp.src(config.sass.src)
        .pipe(gulpif(global.IS_DEV, sourcemaps.init()))
        .pipe(sass({
            outputStyle: global.IS_DEV ? 'expanded' : 'compressed'
        }).on('error', sass.logError))
        .pipe(autoprefixer({ browsers: ['last 2 version'], flexbox: false }))
        // .pipe(gzip({}))
        .pipe(gulpif(global.IS_DEV, sourcemaps.write('./')))
        .pipe(gulpif(!global.IS_DEV, uglify()))
        .pipe(gulp.dest(config.dest));
});

// Sass dependencies
gulp.task('css-sass-deps', function(){
    config.sass.dependencies.src.forEach(function(src, i){
        gulp.src(config.sass.dependencies.src[i])
            .pipe(gulp.dest(config.sass.dependencies.dest[i]));
    });
    return;
});

// CSS reset
gulp.task('css-reset', function(){
    return gulp.src(config.reset.src)
        .pipe(uglify())
        .pipe(gulp.dest(config.dest));
});

// Cleans js to make sure src and dest are sync`d
gulp.task('css-clean', function(){
    return del(config.dest);
});

// Watcher function
gulp.task('css-watcher', function(cb){
    runSequence('css-clean', 'styles', 'post-clean', cb);
});
