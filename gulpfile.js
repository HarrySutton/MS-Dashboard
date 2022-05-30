// CONFIGURATION

// Initial JS file - will be at the top of the output file
var jsdevi  = "dev/js/initial_script.js";   

// JS Dev File(s) - will be appended onto the initial JS file
var jsdev   = "dev/js/*.js";                

// JS Output Directory
var jsout   = "public/library/js/";

// SCSS Dev File(s)
var cssdev  = "dev/custom_style.scss";

// SCSS Output Directory
var cssout  = "public/library/css/";        

// DEPENDENCIES
var gulp    = require('gulp');
var fs      = require('fs');
var gap     = require('gulp-append-prepend');
var sass    = require('gulp-sass');
var clean   = require('gulp-clean');
var concat  = require('gulp-concat');
var minify  = require('gulp-minify');

// SCSS
gulp.task('sass', function(){
    return gulp.src('dev/custom_style.scss')
    .pipe(sass())
    .pipe(gulp.dest('public/library/css'))
});

// REMOVE SCRIPT FILE
gulp.task('clean-script', async function(){
    var file = 'dev/script.js'
    fs.stat(file, function(err, stats) {
        if(err == null){
            return gulp.src(file).pipe(clean(),{allowEmpty:true});
        }
    });
});

// PREP DOCUMENT READY FOR JQUERY
gulp.task('finish-script', function(){
    return gulp.src('dev/script.js')
        .pipe(gap.prependText('$(document).ready(function(){'))
        .pipe(gap.appendText('});'))
        .pipe(gulp.dest('dev/'));
});

// CONCATINATE SCRIPTS
gulp.task('concat-scripts', function(){
    return gulp.src(['dev/js/initial_script.js', 'dev/js/*.js'])
    .pipe(concat('script.js'))
    .pipe(gulp.dest('dev/'));
});

// MINIFY SCRIPT
gulp.task('compress-scripts', function(){
    return gulp.src(['dev/script.js'])
      .pipe(minify())
      .pipe(gulp.dest('public/library/js'))
});

gulp.task('watch', function(){
    gulp.watch(cssdev, gulp.series(['sass'])); 
    gulp.watch(jsdev, gulp.series(['clean-script', 'concat-scripts', 'finish-script', 'compress-scripts', 'clean-script'])); 
})