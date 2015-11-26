#!/bin/sh

###############################################
# PPAGES ~ centerkey.com/ppages               #
# GPL ~ Copyright (c) individual contributors #
###############################################

# Deploy:
# Finds the latest release and upzips it into the web server folder

webServerSetup() {
   echo "*** Apache HTTP Server"
   httpdConf=/private/etc/apache2/httpd.conf
   ls $httpdConf
   grep php5 $httpdConf
   webServerRoot=$(grep ^DocumentRoot $httpdConf | awk -F\" '{ print $2 }')
   webServerPath=ppages-test
   webServerFolder=$webServerRoot/$webServerPath
   echo $webServerFolder
   mkdir -p $webServerFolder
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $projectFolder/releases
   zipFile=$(pwd)/$(ls ppages-*.zip | tail -n 1)
   cd $webServerFolder
   unzip -o $zipFile
   cd gallery
   chmod ugo=rwx data
   pwd
   echo
   }

openConsole() {
   echo "*** Gallery Management Console"
   consoleUrl="http://localhost/$webServerPath/gallery/console/"
   echo $consoleUrl
   open $consoleUrl
   echo
   }

echo
echo "PPAGES ~ Deploy"
echo "==============="
echo
projectFolder=$(dirname $0)/..
webServerSetup
unzipRelease
openConsole
echo "==============="
