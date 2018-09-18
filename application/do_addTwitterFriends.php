<?php /*
	Copyright 2018 Cédric Levieux, Jérémy Collot, ArmagNet

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
include_once("config/mail.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/TweetBo.php");
include_once("language/language.php");

require_once("engine/notification/NotifierFactory.php");
require_once("engine/notification/MailNotifier.php");
require_once("engine/notification/SimpleDmNotifier.php");
require_once("engine/notification/DmNotifier.php");

$data = array();

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$logActionBo = LogActionBo::newInstance($connection);

$accountBo = AccountBo::newInstance($connection);

$userId = SessionUtils::getUserId($_SESSION);
$accounts = $accountBo->getAdministratedAccounts($userId);

$account = null;
foreach($accounts as $currentAccount) {
	if ($currentAccount["sna_id"] == intval($_REQUEST["accountId"])) {
		$account = $currentAccount;
		break;
	}
}

if ($account == null) {
}
else {
	$friends = explode(",", $_REQUEST["friends"]);

	foreach($friends as $friend) {
		$friend = trim($friend);
		if (substr($friend, 0, 1) == "@") $friend = substr($friend, 1); // if it starts with a @

		TweetBo::addTwitterFriend($account, $friend);
	}

	$data["ok"] ="ok";
}

if (!isset($data["ok"])) {
	$data["ko"] = "ko";
}

echo json_encode($data);
?>
