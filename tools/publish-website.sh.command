#!/bin/bash
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) individual contributors to Paradise #
#############################################################

banner="Paradise ~ Publish Website"
projectHome=$(cd $(dirname $0)/..; pwd)
apacheCfg=/usr/local/etc/httpd
apacheLog=/usr/local/var/log/httpd/error_log
webDocRoot=$(grep ^DocumentRoot $apacheCfg/httpd.conf | awk -F'"' '{ print $2 }')

displayIntro() {
   cd $projectHome
   echo
   echo $banner
   echo $(echo $banner | sed s/./=/g)
   pwd
   echo
   }

publishWebFiles() {
   cd $projectHome
   publishSite=$webDocRoot/centerkey.com
   publishFolder=$publishSite/paradise
   publish() {
      echo "Publishing:"
      mkdir -p $publishFolder/graphics
      cp -v website/*.css website/*.html $publishFolder
      cp -v website/graphics/*           $publishFolder/graphics
      echo
      }
   test -w $publishSite && publish
   }

launchBrowser() {
   cd $projectHome
   echo "Opening:"
   pwd
   ls -1 website/*.html
   sleep 2
   open website/logo.html
   open website/index.html
   echo
   }

displayIntro
publishWebFiles
launchBrowser
