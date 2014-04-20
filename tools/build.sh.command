#!/bin/sh

###############################################
# PPAGES ~ www.centerkey.com/ppages           #
# GPL ~ Copyright (c) individual contributors #
###############################################

# Build:
# Creates the release file (zip) with the version number in the file
# name (extracted from ppages/src/gallery/console/library.php)

echo
echo "PPAGES ~ Build"
echo "=============="
cd $(dirname $0)
cd ../releases
echo Releases:
pwd
ls -1
cd ../src
version=$(awk -F\" '/version=/ { print $2 }' gallery/console/library.php)
echo
echo Making version ${version}...
zipFile=../releases/ppages-${version}.zip
rm -f $zipFile
zip -r $zipFile gallery/ --exclude "*/.*" "*/3rd-party/*"
cd ../releases
pwd
ls -l ppages-${version}.zip
echo
