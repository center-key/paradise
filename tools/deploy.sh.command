#!/bin/sh
# PPAGES ~ www.centerkey.com/ppages ~ Copyright (c) individual contributors
# Rights granted under GNU General Public License ~ ppages/src/gallery/license.txt
#
# Deploy: Finds the latest release and upzips it into the web server folder

dest=~/Sites/ppages

echo
echo "PPAGES - Deploy"
echo "==============="
cd $(dirname $0)
cd ../../releases
zipfile=$(pwd)/$(ls ppages-*.zip | tail -n 1)
mkdir -p $dest
cd $dest
unzip -o $zipfile
cd gallery
chmod ugo=rwx data
echo "Web Folder:"
echo "   $(pwd)"
echo "Gallery Management Console:"
echo "   http://localhost/~`whoami`/ppages/gallery/console"
echo "==============="
echo
