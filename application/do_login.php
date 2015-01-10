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
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/LogActionBo.php");
require_once("engine/bo/UserBo.php");

$login = $_REQUEST["login"];
$password = $_REQUEST["password"];
$remoteIp = (isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]) ? $_SERVER["HTTP_X_REAL_IP"] : $_SERVER["REMOTE_ADDR"];

$userBo = UserBo::newInstance(openConnection());
$logActionBo = LogActionBo::newInstance(openConnection());

$numberOfFails = $logActionBo->getNumberOfFails($remoteIp, 600);
if ($numberOfFails > 2) {
	echo json_encode(array("ko" => "ko", "message" => "error_login_ban"));
	exit;
}
//print_r($_REQUEST);

$data = array();

if ($userBo->login($login, $password, $_SESSION)) {
	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "error_login_bad";

	$numberOfFails = $logActionBo->getNumberOfFails($remoteIp, 600);
	if ($numberOfFails > 2) {
		echo json_encode(array("ko" => "ko", "message" => "error_login_ban"));
		exit;
	}
}

$logAction = array();
$logAction["lac_status"] = isset($data["ok"]) ? 1 : 0;
$logAction["lac_label"] = "login";
$logAction["lac_login"] = $login;
$logAction["lac_ip"] = $remoteIp;

$logActionBo->addLogAction($logAction);

echo json_encode($data);
?>