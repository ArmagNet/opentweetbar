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
$testConfig = array();

$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
if (!$referer) exit();

$testConfig["smc_url"] = trim($_REQUEST["url"]);
$testConfig["smc_client_id"] = trim($_REQUEST["clientId"]);
$testConfig["smc_client_secret"] = trim($_REQUEST["clientSecret"]);
$testConfig["smc_user_token"] = trim($_REQUEST["userToken"]);
$testConfig["smc_token_type"] = trim($_REQUEST["tokenType"]);

$tweetBo = TweetBo::newInstance(openConnection());

if ($status = TweetBo::testMastodon($testConfig)) {
//	print_r($status);
 	if (!isset($status["html"]["uri"])) {
 		$data["ko"] = "ko";
 		$data["message"] = "error_mastodon_cant_authenticate";
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
