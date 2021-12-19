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

npmUpdate() {
   npm install --no-fund
   npm update
   npm outdated
   }

setupTools() {
   # Check for Node.js installation and download project dependencies
   cd $projectHome
   echo
   echo $banner
   echo $(echo $banner | sed s/./=/g)
   pwd
   test -d .git && git pull --ff-only
   echo
   echo "Node.js:"
   which node || { echo "Need to install Node.js: https://nodejs.org"; exit; }
   node --version
   test "$mode" == "fast" && echo "Mode: FAST --> to disable: unset mode"
   test "$mode" != "fast" && echo "To enable FAST mode: export mode=fast"
   test "$mode" != "fast" && test -d node_modules && npmUpdate
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
   test "$mode" != "fast" && npx browserslist@latest --update-db
   npm test
   echo
   }

setupPhpServer() {
   cd $projectHome
   echo "*** Apache HTTP Server"
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
analyzePhp
buildZip
setupPhpServer
test -w $deployFolder && deployRelease
