// Paradise PHP Photo Gallery ~ GPLv3
// Gulp configuration and tasks

// Imports
import fileInclude from 'gulp-file-include';
import fs          from 'fs';
import gulp        from 'gulp';
import replace     from 'gulp-replace';
import size        from 'gulp-size';
import sort        from 'gulp-sort';

// Setup
const pkg = JSON.parse(fs.readFileSync('package.json', 'utf-8'));

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

   buildPhp() {
      printHelp(releaseHelp);
      return gulp.src(['src/gallery/**/*.php', 'src/gallery/**/.htaccess'])
         .pipe(sort())
         .pipe(replace('[PARADISE-VERSION]', pkg.version))
         .pipe(fileInclude({ basepath: '@root', indent: true, context: { pkg } }))
         .pipe(size({ showFiles: true }))
         .pipe(gulp.dest('build/3-tst/gallery'));
      },

   };

// Gulp
gulp.task('build-php', task.buildPhp);
