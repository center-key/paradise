#!/bin/sh
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) individual contributors to Paradise #
#############################################################

# Build:
#     Creates the release file (zip) with the version number in the file
#     name (extracted from paradise/src/gallery/console/php/library.php)

banner="Paradise ~ Build"
projectHome=$(cd $(dirname $0)/..; pwd)
version=$(awk -F\" '/version = / { print $2 }' $projectHome/src/gallery/console/php/library.php)

setupTools() {
   # Check for Node.js installation and download project dependencies
   cd $projectHome
   echo
   echo $banner
   echo $(echo $banner | sed -e "s/./=/g")
   pwd
   echo
   echo "Node.js:"
   which node || { echo "Need to install Node.js: https://nodejs.org"; exit; }
   node --version
   npm install
   npm update
   npm outdated
   echo
   }

runStaticAnalyzer() {
   echo "*** Analyzing"
   cd $projectHome
   npm test
   cd $projectHome/src
   pwd
   find . -name "*.php" -exec php --syntax-check {} \;
   echo
   }

zipUpRelease() {
   echo "*** Zipping"
   cd $projectHome/src
   echo "Making release ${version}..."
   find . -name ".DS_Store" -delete
   zipFile=$projectHome/releases/paradise-install-files.zip
   rm -f $zipFile
   zip --recurse-paths --quiet $zipFile gallery/
   cd $projectHome/releases
   pwd
   cp paradise-install-files.zip previous/paradise-${version}.zip
   ls -l paradise-install-files.zip previous/paradise-${version}.zip
   echo
   }

releasesReport() {
   echo "*** Releases"
   cd $projectHome
   git tag | tail -10
   echo
   }

releaseInstructions() {
   echo "*** Instructions"
   cd $projectHome
   echo "Steps to publish this release:"
   echo "   1) Check in release files (.zip) with the comment:"
   echo "      Release $version"
   echo "   2) Tag release:"
   echo "      cd $(pwd)"
   echo "      git tag --annotate --force --message 'Release' $version"
   echo "      git tag --annotate --force --message 'Current release' current"
   echo "      git remote --verbose"
   echo "      git push origin --tags --force"
   echo "   3) Increment version in src/gallery/php/library.php and check in file with the comment:"
   echo "      Next release"
   echo
   }

setupPhpServer() {
   cd $projectHome
   echo "*** Apache HTTP Server"
   publishWebRoot=$(grep ^DocumentRoot /private/etc/apache2/httpd.conf | awk -F\" '{ print $2 }')
   echo $publishWebRoot
   grep php /private/etc/apache2/httpd.conf
   apachectl configtest  #to start web server: sudo apachectl restart
   deployFolder=$publishWebRoot/paradise-deploy
   test -w $publishWebRoot && mkdir -p $deployFolder
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $deployFolder
   pwd
   unzip -o $projectHome/releases/paradise-install-files
   chmod o+rwx gallery
   echo
   }

openConsole() {
   echo "*** Administrator Console"
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
runStaticAnalyzer
zipUpRelease
releasesReport
releaseInstructions
setupPhpServer
test -w $deployFolder && deployRelease
