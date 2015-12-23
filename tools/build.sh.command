#!/bin/sh

#################################################
# PPAGES ~ centerkey.com/ppages                 #
# GPLv3 ~ Copyright (c) individual contributors #
#################################################

# Build:
# Creates the release file (zip) with the version number in the file
# name (extracted from ppages/src/gallery/console/php/library.php)

projectFolder=$(cd $(dirname $0)/..; pwd)
version=$(awk -F\" '/version = / { print $2 }' $projectFolder/src/gallery/console/php/library.php)

runStaticAnalyzer() {
   echo "*** Analyzing"
   cd $projectFolder/src
   for file in gallery/*.php gallery/console/*.php gallery/console/php/*.php; do
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
   echo "Steps to publish this release"
   echo "   1) Check in release files (.zip) with the comment:"
   echo "      Release $version"
   echo "   2) Tag release:"
   echo "      cd $(pwd)"
   echo "      git tag -af $version -m \"Beta release\""
   echo "      git tag -af current -m \"Current release\""
   echo "      git remote -v"
   echo "      git push origin --tags --force"
   echo "   3) Increment version in src/gallery/php/common.php and check in with the comment:"
   echo "      Next release"
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
