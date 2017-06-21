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
include_once("config/mail.php");
include_once("language/language.php");
require_once("engine/bo/UserBo.php");
require_once("engine/utils/SessionUtils.php");

$userBo = UserBo::newInstance(openConnection());

$data = array();

if (isset($_REQUEST["mail"]) && $_REQUEST["mail"]) {
	$data["ok"] = "ok";
	$data["message"] = "ok";
	echo json_encode($data);
	exit();
}

if (isset($_REQUEST["cgv"]) && $_REQUEST["cgv"] != "okgirls") {
	$data["ok"] = "ok";
	$data["message"] = "ok";
	echo json_encode($data);
	exit();
}

$login = $_REQUEST["login"];
$email = $_REQUEST["xxx"];
$password = $_REQUEST["password"];
$confirmation = $_REQUEST["confirmation"];
$language = $_REQUEST["language"];
$notification = $_REQUEST["notification"];

SessionUtils::setLanguage($language, $_SESSION);

if ($password != $confirmation) {
	$data["ko"] = "ko";
	$data["message"] = "error_passwords_not_equal";
	echo json_encode($data);
	exit();
}

$hashedPassword = UserBo::computePassword($password);
$activationKey = UserBo::computePassword($config["salt"] . time());
$url = $config["base_url"] . "activate.php?code=$activationKey&mail=" . urlencode($email);

$mail = getMailInstance();

$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addAddress($email);

$mailMessage = lang("register_mail_content", false);
$mailMessage = str_replace("{activationUrl}", $url, $mailMessage);
$mailMessage = str_replace("{login}", $login, $mailMessage);
$mailSubject = lang("register_mail_subject", false);

$mail->Subject = utf8_decode($mailSubject);
$mail->msgHTML(str_replace("\n", "<br>\n", $mailMessage));
$mail->AltBody = utf8_decode($mailMessage);

if (!$mail->send()) {
	$data["ko"] = "ko";
	$data["message"] = "error_cant_send_mail";
	$data["mail"] = $mail->ErrorInfo;
	echo json_encode($data);
	exit();
}

if ($userBo->register($login, $email, $hashedPassword, $activationKey, $language, $notification)) {
	$data["ok"] = "ok";
}
else {
	$data["ko"] = "ko";
	$data["message"] = "error_cant_register";
}

echo json_encode($data);
?>