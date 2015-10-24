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
//require_once("engine/bo/MediaBo.php");
require_once("engine/bo/TweetBo.php");
//require_once("engine/bo/UserBo.php");
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
// TODO works for twitter-ed account
$bannerUrl = TweetBo::getProfileBanner($account);
$bannerUrl .= "/300x100";

readfile($bannerUrl);
?>