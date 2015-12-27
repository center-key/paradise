#!/bin/sh

#################################################
# PPAGES ~ centerkey.com/ppages                 #
# GPLv3 ~ Copyright (c) individual contributors #
#################################################

# Deploy:
# Unzips current release into web server folder

projectFolder=$(cd $(dirname $0)/..; pwd)

webServerSetup() {
   echo "*** Apache HTTP Server"
   httpdConf=/private/etc/apache2/httpd.conf
   ls $httpdConf
   grep php5 $httpdConf
   apachectl configtest  #to start web server: sudo apachectl restart
   webServerRoot=$(grep ^DocumentRoot $httpdConf | awk -F\" '{ print $2 }')
   webServerPath=ppages-test
   webServerFolder=$webServerRoot/$webServerPath
   echo "Web server folder: $webServerFolder"
   mkdir -p $webServerFolder
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $webServerFolder
   unzip -o $projectFolder/releases/ppages-install-files
   chmod o+rwx gallery
   pwd
   echo
   }

openConsole() {
   echo "*** PPAGES Console"
   consoleUrl="http://localhost/$webServerPath/gallery/console/"
   echo $consoleUrl
   open $consoleUrl
   echo
   }

echo
echo "PPAGES ~ Deploy Test"
echo "===================="
echo
webServerSetup
unzipRelease
openConsole
