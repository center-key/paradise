#!/bin/bash
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) individual contributors to Paradise #
#############################################################

# Build:
#     Creates the release file (zip) with the version number in the file
#     name (extracted from paradise/src/gallery/console/admin-server/library.php)

banner="Paradise ~ Build"
projectHome=$(cd $(dirname $0)/..; pwd)
apacheCfg=/usr/local/etc/httpd
apacheLog=/usr/local/var/log/httpd/error_log
webDocRoot=$(grep ^DocumentRoot $apacheCfg/httpd.conf | awk -F'"' '{ print $2 }')
cliFlagMsg="Use the '--fast' flag to skip npm update"
cliFlag=$1

npmUpdate() {
   npm install --no-fund
   npm update --no-fund
   npm outdated
   }

setupTools() {
   # Check for Node.js installation and download project dependencies
   cd $projectHome
   echo
   echo $banner
   echo $(echo $banner | sed s/./=/g)
   pwd
   test -d .git || { echo "Project must be in a git repository."; exit; }
   git restore dist/* &>/dev/null
   git pull --ff-only
   echo
   echo "Node.js:"
   which node || { echo "Need to install Node.js: https://nodejs.org"; exit; }
   node --version
   test "$cliFlag" = "--fast" && echo "Fast mode (--fast) enabled." || echo $cliFlagMsg
   test "$cliFlag" != "--fast" && npmUpdate
   echo
   }

releaseInstructions() {
   cd $projectHome
   repository=$(grep repository package.json | awk -F'"' '{print $4}' | sed s/github://)
   package=https://raw.githubusercontent.com/$repository/main/package.json
   version=v$(grep '"version"' package.json | awk -F'"' '{print $4}')
   pushed=v$(curl --silent $package | grep '"version":' | awk -F'"' '{print $4}')
   minorVersion=$(echo ${pushed:1} | awk -F"." '{ print $1 "." $2 }')
   echo "Local changes:"
   git status --short
   echo
   echo "Release progress:"
   echo "   $version (local) --> $pushed (pushed)"
   echo
   test "$version" ">" "$pushed" && mode="NOT released" || mode="RELEASED"
   echo "Current version is: $mode"
   echo
   nextActionBump() {
      echo "When ready to do the next release:"
      echo
      echo "   === Increment version ==="
      echo "   Edit pacakge.json to bump $version to next version number"
      echo "   $projectHome/package.json"
      }
   nextActionPush() {
      echo "Verify all tests pass and then finalize the release:"
      echo
      echo "   === Commit and push ==="
      echo "   Check in all changed files with the message:"
      echo "   Release $version"
      }
   test "$version" ">" "$pushed" && nextActionPush || nextActionBump
   echo
   }

analyzePhp() {
   echo "*** Analyze PHP"
   cd $projectHome
   php -v
   pwd
   find src -name "*.php" -exec php --syntax-check {} \;
   echo
   echo "Recent releases:"
   git restore releases/previous  #don't overwrite previous releases
   git ls-files releases/previous/*.zip | tail -5
   echo
   echo "Released version (GitHub folder: releases):"
   releasePage=https://github.com/center-key/paradise/tree/main/releases
   curl --silent $releasePage | grep paradise-v | awk -F'"' '{ print $6 }'
   echo
   }

buildZip() {
   echo "*** Build zip"
   cd $projectHome
   pwd
   npm test
   echo
   }

setupPhpServer() {
   echo "*** Apache HTTP Server"
   cd $projectHome
   grep php $apacheCfg/httpd.conf
   apachectl configtest  #to start web server: brew services restart httpd
   deployFolder=$webDocRoot/paradise-deploy
   test -w $webDocRoot && mkdir -p $deployFolder
   echo $webDocRoot
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $deployFolder
   pwd
   unzip -o $projectHome/releases/paradise-v*.zip
   chmod -v uo+rwx gallery
   accessData() {  #avoid problems if web server runs as a different user
      chmod -v uo+rwx gallery/~data~
      cd gallery/~data~
      chmod -v uo+rwx backups* portfolio secure* uploads
      chmod -Rv uo+rw *.json
      }
   test -d gallery/~data~ && accessData
   echo
   }

openConsole() {
   echo "*** Open Console"
   consoleUrl=http://localhost/paradise-deploy/gallery/console/
   echo $consoleUrl
   sleep 2
   open $consoleUrl
   echo
   }

deployRelease() {
   unzipRelease
   openConsole
   }

setupTools
releaseInstructions
analyzePhp
buildZip
setupPhpServer
test -w $deployFolder && deployRelease
