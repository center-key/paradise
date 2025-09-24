#!/bin/bash
#############################################################
# Paradise ~ centerkey.com/paradise                         #
# GPLv3 ~ Copyright (c) Individual contributors to Paradise #
#############################################################

banner="Paradise ~ Publish Website"
projectHome=$(cd $(dirname $0)/..; pwd)
pkgInstallHome=$(dirname $(dirname $(which httpd)))
apacheCfg=$pkgInstallHome/etc/httpd
apacheLog=$pkgInstallHome/var/log/httpd/error_log
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
      mkdir -pv $publishFolder/graphics
      cp -v website/*.css website/*.html $publishFolder
      cp -v website/graphics/*           $publishFolder/graphics
      test -x "$(which tree)" && tree $publishFolder
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
