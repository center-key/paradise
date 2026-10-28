#!/bin/bash
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) Individual contributors to Paradise #
#############################################################

banner="Paradise ~ Publish Website"
projectHome=$(realpath $0/../..)
pkgInstallHome=$(dirname $(dirname $(which httpd)))
apacheCfg=$pkgInstallHome/etc/httpd
apacheLog=$pkgInstallHome/var/log/httpd/error_log
webDocRoot=$(grep ^DocumentRoot $apacheCfg/httpd.conf | awk -F'"' '{ print $2 }')

displayIntro() {
   cd $projectHome
   echo
   echo $banner
   echo $(echo $banner | sed s/./=/g)
   date
   pwd
   echo
   }

publishWebFiles() {
   cd $projectHome
   publishSite=$webDocRoot/centerkey.com
   publishFolder=$publishSite/paradise
   publish() {
      echo "Publishing:"
      rm -rf $publishFolder
      cp -Rv website/ $publishFolder
      [ -x "$(which tree)" ] && tree $publishFolder
      }
   [ -w $publishSite ] && publish
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
