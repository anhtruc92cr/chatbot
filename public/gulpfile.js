'use strict';

var gulp = require('gulp');
var del = require('del');
var sass = require('gulp-sass')(require('sass'));
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var csso = require('gulp-csso');
var imagemin = require('gulp-imagemin');


// Compile from scss file to css file
gulp.task('sass', function() {
	return gulp.src('src/scss/soxes-chatbot-public.scss')
     .pipe(sass().on('error', sass.logError))
     .pipe(csso())
     .pipe(rename({ suffix: '.min' }))
     .pipe(gulp.dest('css'))    
});

//copy vendor js files
gulp.task('scripts-lib', function() {
	return gulp.src('src/js/vendor/**/*.js')         
     .pipe(gulp.dest('js/lib'))    
});
//copy vendor js files
gulp.task('css-lib', function() {
  return gulp.src('src/css/**/*.css')         
     .pipe(gulp.dest('css/vendor'))    
});
//minify js file
gulp.task('scripts', gulp.series( function() {
	return gulp.src('src/js/*.js')         
     .pipe(uglify())
     .pipe(rename({ suffix: '.min' }))
     .pipe(gulp.dest('js'))    
}));

gulp.task('images', function(){
    return gulp.src('src/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest('img'))
});

// Auto compile when save sccss file
gulp.task('watch', function(){
  gulp.watch('src/scss/**/*.scss', gulp.series('sass')); 
  gulp.watch('src/js/**/*.js', gulp.series('scripts')); 
  gulp.watch('src/img/*', gulp.series('images')); 
});

// Clean script files
gulp.task('clean-scripts', function() {
  return del(['js/**']);
})

// Clean styles files
gulp.task('clean-styles', function() {
  return del(['css/**']);
});

// Clean images files
gulp.task('clean-images', function() {
  return del(['img/**']);
});

//Clean all built files
gulp.task('clean-dist', gulp.series('clean-scripts', 'clean-styles', function(done) {
	done();
}));

// Build all tasks
gulp.task('build',gulp.parallel('sass','scripts','css-lib','images', function (done) {
  done();
}));

// Default task
gulp.task('default', gulp.parallel('build','watch',function (done) {
  done();
}));