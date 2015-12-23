#!/bin/sh

#################################################
# PPAGES ~ centerkey.com/ppages                 #
# GPLv3 ~ Copyright (c) individual contributors #
#################################################

# Update Web Files:
# Copies files to FTP folder

websiteFolder=$(dirname $0)
httpdConf=/private/etc/apache2/httpd.conf
webServerRoot=$(grep ^DocumentRoot $httpdConf | awk -F\" '{ print $2 }')
webServerPath=centerkey.com/ppages
ftpFolder=$webServerRoot/$webServerPath

getColorBlocks() {
   echo "*** Reuse Color Blocks CSS"
   cd $websiteFolder
   note="/* src/gallery/console/css/color-blocks.css */"
   echo "$note" | cat - ../src/gallery/console/css/color-blocks.css > $websiteFolder/color-blocks.css
   head -1 color-blocks.css
   ls -l color-blocks.css
   echo
   }

viewWebsite() {
   echo "*** Open HTML files"
   cd $websiteFolder
   pwd
   ls -l *.html
   open logo.html
   open index.html
   echo
   }

copyToFtpFolder() {
   echo "*** Copy to FTP folder"
   cd $websiteFolder
   echo $ftpFolder
   mkdir -p $ftpFolder/graphics
   cp -v *.css *.html $ftpFolder
   cp -v graphics/*   $ftpFolder/graphics
   open http://localhost/$webServerPath
   echo
   }

echo
echo "PPAGES ~ View Project Website"
echo "============================="
echo
getColorBlocks
viewWebsite
[ -d $ftpFolder ] && copyToFtpFolder
