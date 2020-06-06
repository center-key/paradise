// Paradise PHP Photo Gallery ~ GPLv3
// Gulp configuration and tasks

// Imports
const babel =            require('gulp-babel');
const concat =           require('gulp-concat');
const del =              require('del');
const fileInclude =      require('gulp-file-include');
const gap =              require('gulp-append-prepend');
const gulp =             require('gulp');
const header =           require('gulp-header');
const less =             require('gulp-less');
const mergeStream =      require('merge-stream');
const postCss =          require('gulp-postcss');
const postCssNano =      require('cssnano');
const postCssPresetEnv = require('postcss-preset-env');
const replace =          require('gulp-replace');
const size =             require('gulp-size');
const sort =             require('gulp-sort');
const zip =              require('gulp-zip');

// Setup
const pkg =            require('./package.json');
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
   'To release this version, commit and push the three ".zip" file changes with the comment:',
   '   Release v' + pkg.version,
   'After the release is done, increment version in "package.json" and then commit and push:',
   '   Next release',
   ];
const printHelp = (helpLines) => console.log(helpLines.join('\n'));

// Tasks
const task = {
   cleanTarget() {
      return del([targetFolder, 'releases/paradise-*.zip', '**/.DS_Store']);
      },
   buildWebApp() {
      printHelp(releaseHelp);
      const buildPhp = () =>
         gulp.src(['src/gallery/**/*.php', 'src/gallery/**/.htaccess'])
            .pipe(replace('[PARADISE-VERSION]', pkg.version))
            .pipe(fileInclude({ basepath: '@root', indent: true, context: pkg }))
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
         gulp.src(['src/gallery/scripts/gallery.js', 'src/gallery/scripts/*.js'])
            .pipe(babel(babelMinifyJs))
            .pipe(concat('paradise.min.js'))
            .pipe(header(bannerJs))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder));
      const buildAdminCss = () =>
         gulp.src(['src/gallery/console/**/*.css', 'src/gallery/console/**/*.less'])
            .pipe(less())
            .pipe(concat('paradise-console.min.css'))
            .pipe(postCss(postCssPlugins))
            .pipe(header(bannerCss))
            .pipe(gap.appendText('\n'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest(targetFolder + '/console'));
      const buildAdminJs = () =>
         gulp.src(['src/gallery/console/scripts/admin.js', 'src/gallery/console/**/*.js'])
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
      const reportSizes = () =>
         gulp.src(['target/**/*.css', 'target/**/*.js'])
            .pipe(sort())
            .pipe(size({ showFiles: true, gzip: true }));
      const zipIt = () =>
         gulp.src('target/**/*')
            .pipe(zip('paradise-v' + pkg.version + '.zip'))
            .pipe(size({ showFiles: true }))
            .pipe(gulp.dest('releases'))
            .pipe(gulp.dest('releases/previous'));
      return mergeStream(
         reportSizes(),
         zipIt());
      },
   };

// Gulp
gulp.task('clean-target', task.cleanTarget);
gulp.task('build-app',    task.buildWebApp);
gulp.task('make-zip',     task.makeInstallZip);
