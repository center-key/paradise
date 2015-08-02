#!/bin/sh

###############################################
# PPAGES ~ centerkey.com/ppages               #
# GPL ~ Copyright (c) individual contributors #
###############################################

# Update Web Files:
# Copies files to FTP folder

ftpFolder=~/Sites/centerkey.com/ppages

cd $(dirname $0)
echo
echo "PPAGES ~ Update Web Files"
echo "========================="
echo "Source:"
cd ..
pwd
echo "website/"
ls -l website
mkdir -p $ftpFolder
cp -r website/ $ftpFolder
echo "\nDestination:"
cd $ftpFolder
pwd
ls -l
echo
