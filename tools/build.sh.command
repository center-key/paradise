#!/bin/bash
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) individual contributors to Paradise #
#############################################################

# Build:
#     Creates the release file (zip) with the version number in the file
#     name (extracted from paradise/src/gallery/console/server/library.php)

banner="Paradise ~ Build"
projectHome=$(cd $(dirname $0)/..; pwd)
apacheCfg=/usr/local/etc/httpd
apacheLog=/usr/local/var/log/httpd/error_log

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

analyzeAndBuild() {
   echo "*** Analyze and build"
   cd $projectHome
   pwd
   npx browserslist@latest --update-db
   find src -name "*.php" -exec php --syntax-check {} \;
   echo
   echo "Recent releases:"
   git restore releases/previous  #don't overwrite previous releases
   git ls-files releases/previous/*.zip | tail -5
   echo
   echo "Released version (GitHub folder: releases):"
   releasePage=https://github.com/center-key/paradise/tree/main/releases
   curl --silent $releasePage | grep paradise-v | awk -F'"' '{ print $6 }'
   npm test
   echo
   }

setupPhpServer() {
   cd $projectHome
   echo "*** Apache HTTP Server"
   publishWebRoot=$(grep ^DocumentRoot $apacheCfg/httpd.conf | awk -F'"' '{ print $2 }')
   grep php $apacheCfg/httpd.conf
   apachectl configtest  #to start web server: brew services restart httpd
   deployFolder=$publishWebRoot/paradise-deploy
   test -w $publishWebRoot && mkdir -p $deployFolder
   echo $publishWebRoot
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $deployFolder
   pwd
   unzip -o $projectHome/releases/paradise-v*.zip
   chmod -v o+rwx gallery gallery/~data~
   cd $deployFolder/gallery/~data~
   chmod -v o+rwx backups* portfolio secure* uploads
   chmod -Rv o+rw *.json
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
analyzeAndBuild
setupPhpServer
test -w $deployFolder && deployRelease
