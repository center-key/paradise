// Paradise PHP Photo Gallery

// Imports
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
const replace =          require('gulp-replace');
const size =             require('gulp-size');
const zip =              require('gulp-zip');

// Setup
const pkg =            require('./package.json');
const banner =         `${pkg.name} v${pkg.version} ~~ ${pkg.homepage} ~~ ${pkg.license} License`;
const postCssPlugins = [postCssPresetEnv(), postCssNano({ autoprefixer: false })];
const targetFolder =   'target/gallery';

// Help
const releaseHelp = [
   'To release this version, commit and push the two ".zip" files with the comment:',
   '   Release v' + pkg.version,
   'After the release is done, increment version in "package.json" and then commit and push:',
   '   Next release'
   ];
function printHelp(helpLines) { console.log(helpLines.join('\n')); }

// Tasks
const task = {
   cleanTarget: function() {
      return del([targetFolder, 'releases/paradise-*.zip']);
      },
   buildWebApp: function() {
      function buildPhp() {
         printHelp(releaseHelp);
         return gulp.src(['src/gallery/**/*.php', 'src/gallery/**/.htaccess'])
            .pipe(replace('[PARADISE-VERSION]', pkg.version))
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
      },
   makeInstallZip: function() {
      return gulp.src('target/**/*')
         .pipe(zip('paradise-v' + pkg.version + '.zip'))
         .pipe(gulp.dest('releases'))
         .pipe(gulp.dest('releases/previous'))
         .pipe(size({ showFiles: true }));
      }
   };

// Gulp
gulp.task('clean', task.cleanTarget);
gulp.task('build', task.buildWebApp);
gulp.task('zip',   task.makeInstallZip);
