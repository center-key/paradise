#!/bin/sh
#################################################
# Paradise ~ centerkey.com/paradise             #
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
   webServerPath=paradise-test
   webServerFolder=$webServerRoot/$webServerPath
   echo "Web server folder:"
   echo $webServerFolder
   mkdir -p $webServerFolder
   echo
   }

unzipRelease() {
   echo "*** Unzip Release"
   cd $webServerFolder
   unzip -o $projectFolder/releases/paradise-install-files
   chmod -R o+rwx gallery
   pwd
   echo
   }

openConsole() {
   echo "*** Administrator Console"
   consoleUrl="http://localhost/$webServerPath/gallery/console/"
   echo $consoleUrl
   open $consoleUrl
   echo
   }

echo
echo "Paradise ~ Deploy Test"
echo "======================"
echo
webServerSetup
unzipRelease
openConsole
