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
include_once("config/mail.php");
$config = array("smtp" => array());

$config["smtp"]["host"] = $_REQUEST["host"];
$config["smtp"]["port"] = $_REQUEST["port"];
$config["smtp"]["username"] = $_REQUEST["username"];
$config["smtp"]["password"] = $_REQUEST["password"];
$config["smtp"]["from.address"] = $_REQUEST["fromAddress"];
$config["smtp"]["from.name"] = $_REQUEST["fromName"];

$testMail = $_REQUEST["testAddress"];

$action = $_REQUEST["action"];

$data = array();

if ($action == "mail") {
	$mail = getMailInstance();

	$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addAddress($testMail);

	$mailMessage = "This is a mail test";

	$mail->Subject = "[OTB] OpenTweetBar Mail Test";
	$mail->msgHTML($mailMessage);
	$mail->AltBody = $mailMessage;

	if (!$mail->send()) {
		$data["ko"] = "ko";
		$data["message"] = "error_cant_send_mail";
		$data["mail"] = $mail->ErrorInfo;
	}
	else {
		$data["ok"] = "ok";
	}
}
else if ($action == "save") {
	$content = "";
	$content .= "<"."?php \n";
	$content .= "if(!isset(\$config)) {\n";
	$content .= "	\$config = array();\n";
	$content .= "}\n";
	$content .= "\n";
	$content .= "\$config[\"smtp\"] = array();\n";
	$content .= "\$config[\"smtp\"][\"host\"] = \"" . $config["smtp"]["host"] . "\";\n";
	$content .= "\$config[\"smtp\"][\"port\"] = " . $config["smtp"]["port"] . ";\n";
	$content .= "\$config[\"smtp\"][\"username\"] = \"" . str_replace("\"", "\\\"", $config["smtp"]["username"]) . "\";\n";
	$content .= "\$config[\"smtp\"][\"password\"] = \"" . str_replace("\"", "\\\"", $config["smtp"]["password"]) . "\";\n";
	$content .= "\$config[\"smtp\"][\"from.address\"] = \"" . str_replace("\"", "\\\"", $config["smtp"]["from.address"]) . "\";\n";
	$content .= "\$config[\"smtp\"][\"from.name\"] = \"" . str_replace("\"", "\\\"", $config["smtp"]["from.name"]) . "\";\n";
	$content .= "?".">\n";

//	echo $content;

//	echo file_exists("config/config.php") . "\n";

	if (file_exists("config/mail.config.php")) {
		rename("config/mail.config.php", "config/mail.config.php~");
//		echo rename("config/config.php", "config/config.php~") . "\n";
	}
	file_put_contents("config/mail.config.php" , $content);

	$data["ok"] = "ok";
}

echo json_encode($data);
?>