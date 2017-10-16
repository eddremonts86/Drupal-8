const gulp = require('gulp');
const Server = require('karma').Server;
const config = require('../config').test.unit;

// Smoke tests
// TODO
gulp.task('smoke-tests', function(done){
    new Server(config, function(failCount){
        done(failCount ? new Error(`Failed ${failCount} tests.`) : null);
    }).start();
});
