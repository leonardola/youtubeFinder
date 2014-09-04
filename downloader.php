<?php

getChannelList();

/**
 * gets the channel list
 */
function getChannelList(){
    file_put_contents("videoList.txt","");

    $channelsJson = file_get_contents("channelsList.json");
    $channels = json_decode($channelsJson,true);


    $badWords = getBadWords();

    foreach($channels as $key=>$channel){

        $newDownloadDate = getVideosFromChannel($channel['channel'], $channel['lastDownload'],$badWords);

        if($newDownloadDate){

        	$channels[$key]['lastDownload'] = $newDownloadDate;

	    }
    }

    $channelsJson = json_encode($channels,JSON_PRETTY_PRINT);

    file_put_contents("channelsList.json",$channelsJson);

}

/**
 * tries to find new videos from the given channel
 * @param $channel
 * @param $lastDownloadDate
 * @param $badWords
 * @return bool|string
 */
function getVideosFromChannel($channel,$lastDownloadDate, $badWords){

    $jsonData = file_get_contents(
        "https://gdata.youtube.com/feeds/api/videos?author=$channel&v=2&orderby=updated&alt=jsonc&max-results=5"
    );

    if(!$jsonData){return false;}

    $data = json_decode($jsonData,true);

    $newDownloadDate = $lastDownloadDate;

    foreach($data['data']['items'] as $video){

        $videoDate = new dateTime($video['uploaded']);

        //if the video is older than the last downloaded gets the hell out
        if($videoDate->format('c') < $lastDownloadDate){

            return $newDownloadDate;

        }

        if($videoDate->format('c') > $newDownloadDate){

            $newDownloadDate = $videoDate->format('c');

        }


        if(!titleContainsBadWords($video['title'], $badWords)){

            $videoLink = "https://www.youtube.com/watch?v=".$video['id'];
            file_put_contents("videoList.txt",$videoLink . PHP_EOL, FILE_APPEND);

        }

    }

    return $newDownloadDate;

}

/**
 * gets the list of bad words
 * @return mixed
 */
function getBadWords(){

    $badWords = file("badWords.txt");

    return str_replace(array("\t", "\n"), "", $badWords);

}


/**
 * tries to find a bad word in the given title
 * @param $title
 * @param $badWords
 * @return bool
 */
function titleContainsBadWords($title, $badWords){

    foreach($badWords as $badWord){

        if(strpos($title,$badWord)){
            return true;
        }
    }

    return false;

}
