#!/bin/sh

#################################################
# Paradise ~ centerkey.com/paradise             #
# GPLv3 ~ Copyright (c) individual contributors #
#################################################

# Build:
# Creates the release file (zip) with the version number in the file
# name (extracted from paradise/src/gallery/console/php/library.php)

projectFolder=$(cd $(dirname $0)/..; pwd)
version=$(awk -F\" '/version = / { print $2 }' $projectFolder/src/gallery/console/php/library.php)

runStaticAnalyzer() {
   echo "*** Analyzing"
   cd $projectFolder/src
   pwd
   find . -name "*.php" -exec php --syntax-check {} \;
   echo
   }

zipUpRelease() {
   echo "*** Zipping"
   cd $projectFolder/src
   echo "Making version ${version}..."
   zipFile=$projectFolder/releases/paradise-install-files.zip
   rm -f $zipFile
   zip --recurse-paths --quiet $zipFile gallery/ --exclude "*/.DS_Store"
   cd $projectFolder/releases
   pwd
   cp paradise-install-files.zip paradise-${version}.zip
   ls -l paradise-install-files.zip paradise-${version}.zip
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
   echo "   3) Increment version in src/gallery/php/library.php and check file in with the comment:"
   echo "      Next release"
   echo
   }

echo
echo "Paradise ~ Build"
echo "================"
echo
runStaticAnalyzer
zipUpRelease
releaseInstructions
