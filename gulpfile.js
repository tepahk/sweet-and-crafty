var gulp = require('gulp'),
	watch = require('gulp-watch'),
	sass = require('gulp-sass'),
	rename = require('gulp-rename');

gulp.task('sass', function() {
	gulp.src('wordpress/wp-content/themes/attitude-child/scss/style.scss')
		.pipe(watch('wordpress/wp-content/themes/attitude-child/scss/style.scss'))
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('wordpress/wp-content/themes/attitude-child'));
});

gulp.task('default', ['sass']);