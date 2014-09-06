cd /home/cubie/youtubeFinder

php downloader.php

if [ `ls -l videoList.txt | awk '{print $5}'` -eq 0 ]
then
    #empty
    echo "the file was empty"
else
    cd /media/hd/series/youtube

    /usr/local/bin/youtube-dl -w -t -a /home/cubie/youtubeFinder/videoList.txt
fi


