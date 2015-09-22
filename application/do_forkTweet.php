<?php /*
	Copyright 2014-2015 Cédric Levieux, Jérémy Collot, ArmagNet

	This file is part of OpenTweetBar.

    OpenTweetBar is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenTweetBar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with OpenTweetBar.  If not, see <http://www.gnu.org/licenses/>.
*/
session_start();
include_once("config/database.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$mediaBo = MediaBo::newInstance($connection);

$tweetId = $_REQUEST["tweetId"];

// The tweet id must be a numeric
if (!is_numeric($tweetId)) {
	exit();
}

$tweet = $tweetBo->getTweet($tweetId);

// The tweet can't be null
if (!$tweet) {
	exit();
}

$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));

$_REQUEST["password"] = "";
$_REQUEST["tweet"] = $tweet["twe_content"];
$_REQUEST["validationDuration"] = $tweet["twe_validation_duration"];
$_REQUEST["cronDate"] = $tweet["twe_cron_datetime"];
$_REQUEST["toRetweet"] = $tweet["twe_to_retweet"];
$_REQUEST["mediaIds"] = "";

$mediaSeparator = "";
foreach($medias as $media) {
	$_REQUEST["mediaIds"] .= $mediaSeparator;
	$mediaSeparator = ",";
	$_REQUEST["mediaIds"] .= $media["med_id"];
}

include("do_addTweet.php");
?>