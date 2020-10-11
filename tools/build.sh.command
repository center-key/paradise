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
   npm install --no-fund
   npm update
   npm outdated
   echo
   }

analyzeAndBuild() {
   echo "*** Analyze and build"
   cd $projectHome
   pwd
   find src -name "*.php" -exec php --syntax-check {} \;
   echo
   echo "Recent releases:"
   git ls-files releases/previous/*.zip | tail -5
   echo
   echo "Released version (GitHub folder: releases):"
   releasePage=https://github.com/center-key/paradise/tree/master/releases
   curl --silent $releasePage | grep paradise-v | awk -F'"' '{ print $6 }'
   npm test
   echo
   }

setupPhpServer() {
   cd $projectHome
   echo "*** Apache HTTP Server"
   publishWebRoot=$(grep ^DocumentRoot /private/etc/apache2/httpd.conf | awk -F'"' '{ print $2 }')
   grep php /private/etc/apache2/httpd.conf
   apachectl configtest  #to start web server: sudo apachectl restart
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
