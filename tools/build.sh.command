#!/bin/sh
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) individual contributors to Paradise #
#############################################################

# Build:
# Creates the release file (zip) with the version number in the file
# name (extracted from paradise/src/gallery/console/php/library.php)

projectHome=$(cd $(dirname $0)/..; pwd)
version=$(awk -F\" '/version = / { print $2 }' $projectHome/src/gallery/console/php/library.php)

info() {
   # Check for Node.js installation and download project dependencies
   cd $projectHome
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
   echo "      git tag --annotate --force --message 'Beta release' $version"
   echo "      git tag --annotate --force --message 'Current release' current"
   echo "      git remote --verbose"
   echo "      git push origin --tags --force"
   echo "   3) Increment version in src/gallery/php/library.php and check in file with the comment:"
   echo "      Next release"
   echo
   }

echo
echo "Paradise ~ Build"
echo "================"
echo
info
runStaticAnalyzer
zipUpRelease
releasesReport
releaseInstructions
