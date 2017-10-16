const gulp = require('gulp');
// const runSequence = require('run-sequence');
const config = require('../config');

// dev watcher
gulp.task('watch', function(){
    gulp.watch([config.styles.sass.src, config.styles.other.src], ['css-watcher']);
    gulp.watch(config.images.src, ['images-watcher']);
    gulp.watch(config.js.src, ['js-watcher']);
    gulp.watch(config.templates.src, ['templates-watcher']);
    gulp.watch(config.theme.root.src, ['theme-root-watcher']);
    gulp.watch(config.theme.config.src, ['theme-config-watcher']);
    gulp.watch(config.theme.extensions.src, ['theme-extensions-watcher']);
    gulp.watch(config.fonts.src, ['fonts-watcher']);
    gulp.watch(config.static.src, ['static-watcher']);
});
