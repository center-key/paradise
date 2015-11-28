#!/bin/sh

###############################################
# PPAGES ~ centerkey.com/ppages               #
# GPL ~ Copyright (c) individual contributors #
###############################################

# Build:
# Creates the release file (zip) with the version number in the file
# name (extracted from ppages/src/gallery/console/library.php)

projectFolder=$(dirname $0)/..
version=$(awk -F\" '/version=/ { print $2 }' $projectFolder/src/gallery/console/library.php)

runStaticAnalyzer() {
   echo "*** Analyzing"
   cd $projectFolder/src
   for file in gallery/*.php gallery/console/*.php; do
      php -l $file
      done
   echo
   }

zipUpRelease() {
   echo "*** Zipping"
   cd $projectFolder/src
   echo Making version ${version}...
   zipFile=$projectFolder/releases/ppages-install-files.zip
   rm -f $zipFile
   zip -r $zipFile gallery/ --exclude "*/.*" "*/3rd-party/*"
   echo
   }

listReleases() {
   echo "*** Releases"
   cd $projectFolder/releases
   pwd
   cp ppages-install-files.zip ppages-${version}.zip
   ls -l
   echo
   }

releaseInstructions() {
   echo "*** Instructions"
   cd $projectFolder
   echo "To publish this release:"
   echo "   cd $(pwd)"
   echo "   git tag -af $version -m \"Beta release\""
   echo "   git tag -af current -m \"Current release\""
   echo "   git remote -v"
   echo "   git push origin --tags --force"
   echo
   }

echo
echo "PPAGES ~ Build"
echo "=============="
echo
runStaticAnalyzer
zipUpRelease
listReleases
releaseInstructions
