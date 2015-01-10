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

$user = SessionUtils::getUser($_SESSION);
$password = $_REQUEST["password"];
$oldPassword = $_REQUEST["oldPassword"];
$language = $_REQUEST["language"];
$notification = $_REQUEST["notification"];
$mail = $_REQUEST["xxx"];

$userBo = UserBo::newInstance(openConnection());
$user = $userBo->get(SessionUtils::getUserId($_SESSION));

if ($oldPassword != $user["use_password"]) {
	echo json_encode(array("ko" => "ko", "message" => "error_cant_change_password", $oldPassword => $user["use_password"]));
	exit();
}

$data = array();
$user = array("use_id" => SessionUtils::getUserId($_SESSION));

if ($password) {
	$data["password"] = UserBo::computePassword($password);
	$user["use_password"] = $data["password"];
}
else {
	$data["password"] = $oldPassword;
}

$user["use_language"] = $language;
$user["use_notification"] = $notification;
$user["use_mail"] = $mail;
$userBo->update($user);

$user["use_login"] = SessionUtils::getUser($_SESSION);

SessionUtils::login($_SESSION, $user);

$data["ok"] = "ok";

echo json_encode($data);
?>