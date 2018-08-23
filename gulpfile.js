// Paradise

// Imported libraries
const babel =            require('gulp-babel');
const concat =           require('gulp-concat');
const del =              require('del');
const fileInclude =      require('gulp-file-include');
const gulp =             require('gulp');
const header =           require('gulp-header');
const less =             require('gulp-less');
const mergeStream =      require('merge-stream');
const postCss =          require('gulp-postcss');
const postCssNano =      require('cssnano');
const postCssPresetEnv = require('postcss-preset-env');

// Setup values
const pkg = require('./package.json');
const banner = `${pkg.name} v${pkg.version} ~~ ${pkg.homepage} ~~ ${pkg.license} License`;
const postCssPlugins = [postCssPresetEnv(), postCssNano({ autoprefixer: false })];
const targetFolder = 'target/gallery';

// Tasks
const task = {
   cleanTarget: function() {
      return del(targetFolder);
      },
   buildWebApp: function() {
      function buildPhp() {
         return gulp.src(['src/gallery/**/*.php', 'src/gallery/**/.htaccess'])
            .pipe(fileInclude({ basepath: '@root', indent: true }))
            .pipe(gulp.dest(targetFolder));
          }
      function buildCss() {
         return gulp.src('src/gallery/style/*.less')
            .pipe(less())
            .pipe(concat('paradise.css'))
            .pipe(postCss(postCssPlugins))
            .pipe(header('/*! ' + banner + ' */\n'))
            .pipe(gulp.dest(targetFolder));
         }
      function buildJs() {
         return gulp.src('src/gallery/scripts/*.js')
            .pipe(babel({ presets: ['env'] }))
            .pipe(concat('paradise.js'))
            .pipe(gulp.dest(targetFolder));
         }
      function buildAdminCss() {
         return gulp.src(['src/gallery/console/**/*.css', 'src/gallery/console/**/*.less'])
            .pipe(less())
            .pipe(concat('admin.css'))
            .pipe(postCss(postCssPlugins))
            .pipe(header('/*! ' + banner + ' */\n'))
            .pipe(gulp.dest(targetFolder + '/console'));
         }
      function buildAdminJs() {
         return gulp.src(['src/gallery/scripts/library.js', 'src/gallery/console/**/*.js'])
            .pipe(babel({ presets: ['env'] }))
            .pipe(concat('admin.js'))
            .pipe(gulp.dest(targetFolder + '/console'));
         }
      function copyLicense() {
         return gulp.src('LICENSE.txt')
            .pipe(gulp.dest(targetFolder));
         }
      return mergeStream(
         buildPhp(),
         buildCss(),
         buildJs(),
         buildAdminCss(),
         buildAdminJs(),
         copyLicense()
         );
      }
   };

// Gulp
gulp.task('clean', task.cleanTarget);
gulp.task('build', task.buildWebApp);
