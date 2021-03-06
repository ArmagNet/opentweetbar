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
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

$connection = openConnection();

$userId = SessionUtils::getUserId($_SESSION);

$requestedAccountId = $_REQUEST["accountId"];

$accountBo = AccountBo::newInstance($connection);
$accounts = $accountBo->getAccessibleAccounts($userId);
$account = null;

foreach($accounts as $caccount) {
	if ($caccount["sna_id"] == $requestedAccountId) {
		$account = $accountBo->getAccount($requestedAccountId);
	}
}

if (!$account) {
	echo json_encode(array("ko" => "no account access found"));
	exit();
}

$sinceId = null;
if (isset($_REQUEST["sinceId"])) {
	$sinceId = intval($_REQUEST["sinceId"]);
}

$numberOfTweets = null;
if (isset($_REQUEST["numberOfTweets"])) {
	$numberOfTweets = intval($_REQUEST["numberOfTweets"]);
}

// error_log("Start retrieve for $requestedAccountId");
// error_log(print_r($account, true));
// error_log($sinceId);
$timeline = TweetBo::getTimeline($account, $sinceId, $numberOfTweets);
// error_log("Retrieve for $requestedAccountId");

// error_log(print_r($timeline, true));

echo json_encode(array("ok" => "ok", "accountId" => $requestedAccountId, "timeline" => $timeline));
?>