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
$config = array("database" => array());

$config["database"]["host"] = $_REQUEST["host"];
$config["database"]["port"] = $_REQUEST["port"];
$config["database"]["login"] = $_REQUEST["login"];
$config["database"]["password"] = $_REQUEST["password"];
$config["database"]["database"] = $_REQUEST["database"];

$action = $_REQUEST["action"];

$dns = 'mysql:host='.$config["database"]["host"].';dbname=' . $config["database"]["database"];

if (isset($config["database"]["port"]) && $config["database"]["port"]) {
	$dns .= ";port=" . $config["database"]["port"];
}

$user = $config["database"]["login"];
$password = $config["database"]["password"];

$error = null;

try {
	$pdo = new PDO($dns, $user, $password);
}
catch(Exception $e) {
	// 	print_r($e);

	preg_match("/SQLSTATE\\[([A-Z0-9]*)\\] \\[([0-9]*)\\] ([A-Za-z0-9\\'\\ \\.\\(\\\"\\)]*)/", $e->getMessage(), $matches);

	//	print_r($matches);

	$error = array("code" => $matches[2], "state" => $matches[1], "message" => $matches[3]);

	// 	print_r($error);
}

$data = array();

if ($action == "ping") {
	if (!$error) {
		$data["ok"] = "ok";
	}
	else if ($error["code"] >= 2000) {
		$data["ko"] = "ko";
		$data["message"] = "error_cant_connect";
	}
	else if ($error["code"] == 1049) {
		$data["ko"] = "ko";
		$data["message"] = "error_database_dont_exist";
	}
}
else if ($action == "create") {
	if (!$error) {
		$data["ko"] = "ko";
		$data["message"] = "error_database_already_exists";
	}
	else if ($error["code"] >= 2000) {
		$data["ko"] = "ko";
		$data["message"] = "error_cant_connect";
	}
	else {

		$dns = 'mysql:host='.$config["database"]["host"];

		if (isset($config["database"]["port"]) && $config["database"]["port"]) {
			$dns .= ";port=" . $config["database"]["port"];
		}

		$pdo = new PDO($dns, $user, $password);

		$pdo->exec("CREATE DATABASE `".$config["database"]["database"]."`;
				CREATE USER '$user'@'localhost' IDENTIFIED BY '$password';
				GRANT ALL ON `".$config["database"]["database"]."`.* TO '$user'@'localhost';
				FLUSH PRIVILEGES;");
		$data["ok"] = "ok";
	}
}
else if ($action == "deploy") {
	if (!$error) {
		$createTweetsTableQuery = "CREATE TABLE IF NOT EXISTS `tweets` (
		`twe_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of the handled tweet',
		`twe_author` varchar(255) NOT NULL COMMENT 'id of the tweet author',
		`twe_content` varchar(255) NOT NULL COMMENT 'Content of the tweet',
		`twe_destination` varchar(255) NOT NULL COMMENT 'Account which will tweet this tweet',
		`twe_status` enum('inValidation','validated','expirated','rejected','deleted') NOT NULL DEFAULT 'inValidation' COMMENT 'Status of the tweet',
		`twe_validation_score` int(11) NOT NULL COMMENT 'The score for the validation',
		PRIMARY KEY (`twe_id`),
		KEY `twe_author` (`twe_author`),
		KEY `twe_status` (`twe_status`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createTweetValidationsTableQuery = "CREATE TABLE IF NOT EXISTS `tweet_validations` (
		`tva_id` int(11) NOT NULL AUTO_INCREMENT,
		`tva_tweet_id` int(11) NOT NULL,
		`tva_validator` varchar(255) NOT NULL,
		`tva_status` enum('validation','rejection') NOT NULL,
		`tva_score` int(11) NOT NULL,
		`tva_ip` VARCHAR( 50 ) NOT NULL ,
		`tva_referer` VARCHAR( 255 ) NOT NULL ,
		`tva_datetime` TIMESTAMP NOT NULL ;
		PRIMARY KEY (`tva_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createSNAConfigurationTableQuery = "CREATE TABLE IF NOT EXISTS `sna_configuration` (
				`sco_id` int(11) NOT NULL AUTO_INCREMENT,
				`sco_sna_id` int(11) NOT NULL,
				`sco_validation_score` int(11) NOT NULL,
				PRIMARY KEY (`sco_id`),
				KEY `sco_sna_id` (`sco_sna_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$createSNATwitterConfigurationTableQuery = "CREATE TABLE IF NOT EXISTS `sna_twitter_configuration` (
				`stc_id` int(11) NOT NULL AUTO_INCREMENT,
				`stc_sna_id` int(11) NOT NULL,
				`stc_api_key` varchar(255) NOT NULL,
				`stc_api_secret` varchar(255) NOT NULL,
				`stc_access_token` varchar(255) NOT NULL,
				`stc_access_token_secret` varchar(255) NOT NULL,
				PRIMARY KEY (`stc_id`),
				KEY `stc_sna_id` (`stc_sna_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createSocialNetworkAccountTableQuery = "CREATE TABLE IF NOT EXISTS `social_network_accounts` (
				`sna_id` int(11) NOT NULL AUTO_INCREMENT,
				`sna_name` varchar(255) NOT NULL,
				PRIMARY KEY (`sna_id`),
				UNIQUE KEY `sna_name` (`sna_name`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createUsersTableQuery = "CREATE TABLE IF NOT EXISTS `users` (
				  `use_id` int(11) NOT NULL AUTO_INCREMENT,
				  `use_login` varchar(255) NOT NULL,
				  `use_password` varchar(255) NOT NULL,
				  `use_activated` tinyint(1) NOT NULL DEFAULT '0',
				  `use_activation_key` varchar(255) NOT NULL,
				  `use_mail` varchar(255) NOT NULL,
				  `use_language` char(2) NOT NULL DEFAULT 'fr',
				  `use_notification` enum('none', 'mail', 'simpledm', 'dm') NOT NULL DEFAULT  'dm',
				  PRIMARY KEY (`use_id`),
				  KEY `use_login` (`use_login`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createUserTwitterTableQuery = "CREATE TABLE IF NOT EXISTS `user_twitter` (
				`utw_id` int(11) NOT NULL AUTO_INCREMENT,
				`utw_user_id` int(11) NOT NULL,
				`utw_account` varchar(255) NOT NULL,
				PRIMARY KEY (`utw_id`),
				KEY `utw_user_id` (`utw_user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createValidatorsTableQuery = "CREATE TABLE IF NOT EXISTS `validators` (
				`val_validator_group_id` int(11) NOT NULL,
				`val_user_id` int(11) NOT NULL,
				PRIMARY KEY (`val_validator_group_id`,`val_user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$createValidatorGroupsTableQuery = "CREATE TABLE IF NOT EXISTS `validator_groups` (
				`vgr_id` int(11) NOT NULL AUTO_INCREMENT,
				`vgr_sna_id` int(11) NOT NULL,
				`vgr_name` varchar(255) NOT NULL,
				`vgr_score` int(11) NOT NULL,
				PRIMARY KEY (`vgr_id`),
				KEY `vgr_sna_id` (`vgr_sna_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

		$createAdministratorsTableQuery = "CREATE TABLE IF NOT EXISTS `administrators` (
				`adm_sna_id` int(11) NOT NULL,
				`adm_user_id` int(11) NOT NULL,
				PRIMARY KEY (`adm_sna_id`,`adm_user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$createLogActionsTableQuery = "CREATE TABLE IF NOT EXISTS `log_actions` (
				`lac_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`lac_label` varchar(255) NOT NULL,
				`lac_status` tinyint(4) NOT NULL,
				`lac_login` varchar(255) NOT NULL,
				`lac_ip` varchar(255) NOT NULL,
				`lac_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`lac_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$pdo->exec($createSNAConfigurationTableQuery);
		$pdo->exec($createSNATwitterConfigurationTableQuery);
		$pdo->exec($createSocialNetworkAccountTableQuery);
		$pdo->exec($createUsersTableQuery);
		$pdo->exec($createUserTwitterTableQuery);
		$pdo->exec($createValidatorsTableQuery);
		$pdo->exec($createValidatorGroupsTableQuery);
		$pdo->exec($createTweetsTableQuery);
		$pdo->exec($createTweetValidationsTableQuery);
		$pdo->exec($createAdministratorsTableQuery);
		$pdo->exec($createLogActionsTableQuery);

		$data["ok"] = "ok";
	}
	else if ($error["code"] >= 2000) {
		$data["ko"] = "ko";
		$data["message"] = "error_cant_connect";
	}
	else if ($error["code"] == 1049) {
		$data["ko"] = "ko";
		$data["message"] = "error_database_dont_exist";
	}
}
else if ($action == "save") {
	$content = "";
	$content .= "<"."?php \n";
	$content .= "if(!isset(\$config)) {\n";
	$content .= "	\$config = array();\n";
	$content .= "}\n";
	$content .= "\n";
	$content .= "\$config[\"database\"] = array();\n";
	$content .= "\$config[\"database\"][\"host\"] = \"" . $config["database"]["host"] . "\";\n";
	if ($config["database"]["port"]) {
		$content .= "\$config[\"database\"][\"port\"] = " . $config["database"]["port"] . ";\n";
	}
	$content .= "\$config[\"database\"][\"login\"] = \"" . str_replace("\"", "\\\"", $config["database"]["login"]) . "\";\n";
	$content .= "\$config[\"database\"][\"password\"] = \"" . str_replace("\"", "\\\"", $config["database"]["password"]) . "\";\n";
	$content .= "\$config[\"database\"][\"database\"] = \"" . $config["database"]["database"] . "\";\n";
	$content .= "\$config[\"database\"][\"prefix\"] = \"\";\n";
	$content .= "?".">\n";

//	echo $content;

//	echo file_exists("config/config.php") . "\n";

	if (file_exists("config/config.php")) {
		rename("config/config.php", "config/config.php~");
//		echo rename("config/config.php", "config/config.php~") . "\n";
	}
	file_put_contents("config/config.php" , $content);

	$data["ok"] = "ok";
}

echo json_encode($data);
?>