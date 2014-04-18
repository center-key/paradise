#!/bin/sh

# PPAGES ~ www.centerkey.com/ppages
# GPL ~ Copyright (c) individual contributors
#
# Build: Creates the release file (zip) and puts the version number in the file
# name (version number is extracted from ppages/src/gallery/console/library.php)

echo
echo "PPAGES - Build"
echo "=============="
cd $(dirname $0)
cd ..
echo Releases:
ls -1 ../releases
version=$(awk -F\" '/version=/ { print $2 }' gallery/console/library.php)
echo
echo Making version ${version}...
zipfile=../releases/ppages-${version}.zip
rm -f $zipfile
zip -r $zipfile gallery/ --exclude "*/.*" "*/3rd-party/*"
exit
cd ../releases
pwd
ls -l ppages-${version}.zip
echo "=============="
echo
