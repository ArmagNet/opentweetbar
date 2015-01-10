<?php /*
	Copyright 2014 Cédric Levieux, Jérémy Collot, ArmagNet

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
require_once("engine/bo/TweetBo.php");

$data = array();
$testConfig = array();

$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
if (!$referer) exit();

$testConfig["api_key"] = $_REQUEST["apiKey"];
$testConfig["api_secret"] = $_REQUEST["apiSecret"];
$testConfig["access_token"] = $_REQUEST["accessToken"];
$testConfig["access_token_secret"] = $_REQUEST["accessTokenSecret"];

$tweetBo = TweetBo::newInstance(openConnection());

if ($status = TweetBo::testTwitter($testConfig)) {
//	print_r($status);
	if (count($status->errors)) {
		$data["ko"] = "ko";
		$data["message"] = "error_twitter_cant_authenticate";
	}
	else {
		$data["ok"] = "ok";
	}
}
else {
	$data["ko"] = "ko";
}

echo json_encode($data);
?>