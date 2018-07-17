#!/bin/bash
readonly SITE_NAMES='atosdrg valleyview ubmc wrmc staging-wrmc wrms'
readonly BASEURL=usvsolutions.com

cd /var/www/rev/tmp
for site_name in ${SITE_NAMES}
do
    echo Testing ${site_name}
    url=https://${site_name}.${BASEURL}
    filename=scr_${site_name}.png
    echo url: ${url}
    #take a picture after waiting a bit for the load to finish
    sleep 1
    google-chrome --headless --disable-gpu --hide-scrollbars --screenshot --window-size=1920,1080 ${url} --screenshot=${filename}
    convert ${filename} -pointsize 9 -draw "text 167,167 '${url}'" ${filename}
done

if [ -f scr_all.pdf ]; then rm scr_all.pdf; fi;
convert scr_*.png scr_all.pdf
delete scr_*.png
