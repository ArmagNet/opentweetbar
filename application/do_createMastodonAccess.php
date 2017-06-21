<?php /*
	Copyright 2014-2017 Cédric Levieux, Jérémy Collot, ArmagNet

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
$mastodonConfig = array();

$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
if (!$referer) exit();

$mastodonConfig["smc_url"] = trim($_REQUEST["url"]);
$mastodonConfig["smc_client_name"] = trim($_REQUEST["clientName"]);
$mastodonConfig["smc_email"] = trim($_REQUEST["email"]);
$mastodonConfig["smc_password"] = trim($_REQUEST["password"]);

$tweetBo = TweetBo::newInstance(openConnection());

if ($status = TweetBo::createMastodonAccess($mastodonConfig)) {
//	print_r($status);
	if (!isset($status["clientSecret"])) {
		$data["ko"] = "ko";
		$data["message"] = "error_mastodon_cant_connect";
	}
	else if (!isset($status["userToken"])) {
		$data["ko"] = "ko";
		$data["message"] = "error_mastodon_cant_authenticate";
	}
	else {
 		$data["ok"] = "ok";
 		$data["smc_client_id"] = $status["clientId"];
 		$data["smc_client_secret"] = $status["clientSecret"];
 		$data["smc_user_token"] = $status["userToken"];
 	}
}
else {
	$data["ko"] = "ko";
	$data["message"] = "error_mastodon_cant_authenticate";
}

echo json_encode($data);
?>
