// Paradise PHP Photo Gallery ~ GPLv3
// Gulp configuration and tasks

// Imports
import babel            from 'gulp-babel';
import concat           from 'gulp-concat';
import fileInclude      from 'gulp-file-include';
import gap              from 'gulp-append-prepend';
import gulp             from 'gulp';
import header           from 'gulp-header';
import less             from 'gulp-less';
import mergeStream      from 'merge-stream';
import postCss          from 'gulp-postcss';
import postCssNano      from 'cssnano';
import postCssPresetEnv from 'postcss-preset-env';
import replace          from 'gulp-replace';
import size             from 'gulp-size';
import sort             from 'gulp-sort';
import zip              from 'gulp-zip';
import { readFileSync } from 'fs';

// Setup
const pkg =            JSON.parse(readFileSync('./package.json', 'utf8'));
const home =           pkg.homepage.replace('https://', '');
const banner =         'Paradise PHP Photo Gallery v' + pkg.version + ' ~ ' + home + ' ~ GPLv3';
const bannerCss =      '/*! ' + banner + ' */\n';
const bannerJs =       '//! ' + banner + '\n';
const postCssPlugins = [postCssPresetEnv(), postCssNano({ autoprefixer: false })];
const transpileES6 =   ['@babel/env', { modules: false }];
const babelMinifyJs =  { presets: [transpileES6, 'minify'], comments: false };
const targetFolder =   'target/gallery';

// Help
const releaseHelp = [
   'When ready to do the next release:',
   '   Edit pacakge.json to bump ' + pkg.version + ' to next version number',
   'Verify all tests pass and then finalize the release:',
   '   Check in all changed files with the message (X.Y.Z is the version number):',
   '   Release vX.Y.Z',
   ];
const printHelp = (helpLines) => console.log('\n' + helpLines.join('\n') + '\n');

// Tasks
const task = {

   buildWebApp() {
      const buildPhp = () =>
         gulp.src(['src/gallery/**/*.php', 'src/gallery/**/.htaccess'])
            .pipe(sort())
            .pipe(replace('[PARADISE-VERSION]', pkg.version))
            .pipe(fileInclude({ basepath: '@root', indent: true, context: { pkg } }))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder));
      const buildCss = () =>
         gulp.src('src/gallery/style/*.less')
            .pipe(sort())
            .pipe(less())
            .pipe(concat('paradise.min.css'))
            .pipe(postCss(postCssPlugins))
            .pipe(header(bannerCss))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder));
      const buildJs = () =>
         gulp.src(['src/gallery/js/gallery.js', 'src/gallery/js/*.js'])
            .pipe(sort())
            .pipe(babel(babelMinifyJs))
            .pipe(concat('paradise.min.js'))
            .pipe(header(bannerJs))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder));
      const buildAdminCss = () =>
         gulp.src(['src/gallery/console/**/*.css', 'src/gallery/console/**/*.less'])
            .pipe(sort())
            .pipe(less())
            .pipe(concat('paradise-console.min.css'))
            .pipe(postCss(postCssPlugins))
            .pipe(header(bannerCss))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder + '/console'));
      const buildAdminJs = () =>
         gulp.src(['src/gallery/console/js/admin.js', 'src/gallery/console/**/*.js'])
            .pipe(sort())
            .pipe(babel(babelMinifyJs))
            .pipe(concat('paradise-console.min.js'))
            .pipe(header(bannerJs))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder + '/console'));
      const copyLicense = () =>
         gulp.src('LICENSE.txt')
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder));
      return mergeStream(
         buildPhp(),
         buildCss(),
         buildJs(),
         buildAdminCss(),
         buildAdminJs(),
         copyLicense());
      },

   makeInstallZip() {
      printHelp(releaseHelp);
      return gulp.src('target/**/*', { dot: true })
         .pipe(sort())
         .pipe(size({ showFiles: true, gzip: true }))
         .pipe(zip('paradise-v' + pkg.version + '.zip'))
         .pipe(size({ showFiles: true }))
         .pipe(gulp.dest('releases'))
         .pipe(gulp.dest('releases/previous'));
      },

   };

// Gulp
gulp.task('build-app', task.buildWebApp);
gulp.task('make-zip',  task.makeInstallZip);
