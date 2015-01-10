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
$config = array();

$config["base_url"] = $_REQUEST["baseUrl"];
$config["default_language"] = $_REQUEST["defaultLanguage"];
$config["salt"] = $_REQUEST["salt"];
$config["cron_enabled"] = $_REQUEST["cronEnabled"];

$action = $_REQUEST["action"];

$data = array();

if ($action == "save") {
	$content = "";
	$content .= "<"."?php \n";
	$content .= "if(!isset(\$config)) {\n";
	$content .= "	\$config = array();\n";
	$content .= "}\n";
	$content .= "\n";
	$content .= "\$config[\"base_url\"] = \"" . str_replace("\"", "\\\"", $config["base_url"]) . "\";\n";
	$content .= "\$config[\"default_language\"] = \"" . str_replace("\"", "\\\"", $config["default_language"]) . "\";\n";
	$content .= "\$config[\"salt\"] = \"" . str_replace("\"", "\\\"", $config["salt"]) . "\";\n";
	$content .= "\$config[\"cron_enabled\"] = " . $config["cron_enabled"] . ";\n";
	$content .= "?".">\n";

//	echo $content;

//	echo file_exists("config/config.php") . "\n";

	if (file_exists("config/salt.php")) {
		rename("config/salt.php", "config/salt.php~");
//		echo rename("config/config.php", "config/config.php~") . "\n";
	}
	file_put_contents("config/salt.php" , $content);

	$data["ok"] = "ok";
}

echo json_encode($data);
?>