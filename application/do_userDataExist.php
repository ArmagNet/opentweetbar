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
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$userId = SessionUtils::getUserId($_SESSION);

// TODO verify referer
if (!isset($_SERVER["HTTP_REFERER"]) || !$_SERVER["HTTP_REFERER"]) {
	exit();
}

$userBo = UserBo::newInstance(openConnection());

$field = $_REQUEST["field"];
$value = $_REQUEST["value"];

$data = array();

switch ($field) {
	case "mail":
		$field = "use_mail";
		break;
	case "login":
		$field = "use_login";
		break;
	default:
		$data["ko"] = "ko";
		$data["message"] = "error_not_permitted";
}

if (!isset($data["ko"])) {
	$dataExists = $userBo->hasDataExist($field, $value, $userId);

	$data["exist"] = $dataExists;
	$data["ok"] = "ok";
}

echo json_encode($data);
?>