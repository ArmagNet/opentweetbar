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

class UserBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new UserBo($pdo);
	}

	static function computePassword($password) {
		global $config;

		return hash("sha256", $config["salt"] . $password . $config["salt"], false);
	}

	function update($user) {
		$query = "UPDATE users ";
		$separator = " SET ";

		if (isset($user["use_language"])) {
			$query .= $separator . "	use_language = :use_language ";
			$separator = ", ";
		}

		if (isset($user["use_notification"])) {
			$query .= $separator . "	use_notification = :use_notification ";
			$separator = ", ";
		}

		if (isset($user["use_mail"])) {
			$query .= $separator . "	use_mail = :use_mail ";
			$separator = ", ";
		}

		if (isset($user["use_password"])) {
			$query .= $separator . "	use_password = :use_password ";
			$separator = ", ";
		}

		$query .= "WHERE use_id = :use_id";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($user);
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}

	function autologin($cookies, &$session) {
		if (!isset($cookies["userId"])) return;
		if (!isset($cookies["userCode"])) return;

		global $config;

		$userCode = $cookies["userCode"];
		$userId = $cookies["userId"];

		$verifyUserCode = hash("sha512", $userId . $config["salt"], false);

		if ($verifyUserCode != $userCode) return;

		$user = $this->get($userId);

		if (!$user) return;

		SessionUtils::login($session, $user);
	}

	function login($login, $password, &$session) {
		$args = array("use_login" => $login);
		$query = "SELECT * FROM users WHERE (use_login = :use_login OR use_mail = :use_login) AND use_activated = 1 ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				if ($user["use_password"] == UserBo::computePassword($password)) {
					SessionUtils::login($session, $user);
					return true;
				}
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function get($userId) {
		$args = array("use_id" => $userId);
		$query = "SELECT * FROM users WHERE use_id = :use_id";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				return $user;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getUserId($user) {
		$args = array("use_login" => $user);
		$query = "SELECT * FROM users WHERE use_login = :use_login";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				$user = $users[0];

				return $user["use_id"];
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function activate($mail, $code) {
		$args = array("use_activated" => 0, "use_mail" => $mail, "use_activation_key" => $code);
		$query = "	UPDATE users
					SET use_activated = 1, use_activation_key = ''
					WHERE
						use_activated = :use_activated
					AND	use_activation_key = :use_activation_key
					AND	use_mail = :use_mail ";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$rowCount = $statement->rowCount();

			if ($rowCount) {
				return true;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function forgotten($mail, $hashedPassword) {
		$args = array(	"use_mail" => $mail,
						"use_password" => $hashedPassword);

		$query = "	UPDATE users
					SET use_password = :use_password
					WHERE
						use_mail = :use_mail ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function register($login, $mail, $hashedPassword, $activationKey, $language, $notification) {
		$args = array(	"use_activated" => 0,
						"use_mail" => $mail,
						"use_activation_key" => $activationKey,
						"use_language" => $language,
						"use_login" => $login,
						"use_password" => $hashedPassword,
						"use_notification" => $notification);

		$query = "	INSERT INTO users
						(use_login, use_password, use_mail, use_activated, use_activation_key, use_language, use_notification)
					VALUES
						(:use_login, :use_password, :use_mail, :use_activated, :use_activation_key, :use_language, :use_notification) ";

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function hasDataExist($field, $value, $exceptUserId = null) {
		$args = array($field => $value);
		$query = "SELECT * FROM users WHERE $field = :$field ";

		if ($exceptUserId) {
			$args["user_id"] = $exceptUserId;
			$query .= " AND use_id != :use_id ";
		}

		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$users = $statement->fetchAll();

			if (count($users)) {
				return true;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function getTimeStats($userId, $delay = 31) {
		$stats = array();
		$fromDate = null;

		for($from = -$delay; $from <= 0; $from++) {
			$date = new DateTime();
			$date->setTime(0, 0, 0);
			$date = $date->sub(new DateInterval("P" . -$from . "D"));
			$timestamp = $date->getTimestamp();
			$date = $date->format("Y-m-d");
			$fromDate = $fromDate ? $fromDate : $date;
			$stats[$date] = array(	"stat_date" => $date, "stat_timestamp" => $timestamp,
									"twe_tweets" => 0, "tva_validations" => 0, "tva_scores" => 0);
		}

		$args = array("use_id" => $userId, "tva_datetime" => $fromDate);
		$query = " SELECT DATE_FORMAT(tva_datetime, '%Y-%m-%d') AS stat_date,
						COUNT( twe_id ) AS twe_tweets,
						UNIX_TIMESTAMP(DATE_FORMAT(tva_datetime, '%Y-%m-%d')) AS stat_timestamp
					FROM tweet_validations
					JOIN tweets ON tva_tweet_id = twe_id
					AND tva_validator = twe_author
					WHERE twe_author = :use_id AND tva_datetime > :tva_datetime
					GROUP BY stat_date";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args) . "\n<br>";

		try {
			$statement->execute($args);
			$statsB = $statement->fetchAll();

			foreach($statsB as $stat) {
				foreach($stats as $index => $curstat) {
					if ($curstat["stat_date"] == $stat["stat_date"]) {
						$found = true;
						$stats[$index]["twe_tweets"] = $stat["twe_tweets"];
					}
				}
			}

		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		$query = "	SELECT DATE_FORMAT(tva_datetime, '%Y-%m-%d') AS stat_date,
						COUNT( tva_id ) AS tva_validations, SUM( tva_score ) AS tva_scores,
						UNIX_TIMESTAMP(DATE_FORMAT(tva_datetime, '%Y-%m-%d')) AS stat_timestamp
					FROM tweet_validations
					WHERE tva_validator = :use_id AND tva_datetime > :tva_datetime
					GROUP BY stat_date";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args) . "\n<br>";

		try {
			$statement->execute($args);
			$statsB = $statement->fetchAll();

			foreach($statsB as $stat) {
				foreach($stats as $index => $curstat) {
					if ($curstat["stat_date"] == $stat["stat_date"]) {
						$found = true;
						$stats[$index]["tva_validations"] = $stat["tva_validations"];
						$stats[$index]["tva_scores"] = $stat["tva_scores"];
					}
				}
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $stats;
	}

	function getStats($userId) {
		$args = array("use_id" => $userId);
		$query = "	SELECT sna_id, sna_name, COUNT(twe_id) as sna_tweets
					FROM  `tweets`
					JOIN social_network_accounts ON twe_destination = sna_id
					WHERE twe_author = :use_id
					GROUP BY sna_id";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$stats = $statement->fetchAll();
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		$query = "	SELECT sna_id, sna_name, COUNT(tva_id) as sna_validations, SUM(tva_score) as sna_scores
					FROM tweet_validations
					JOIN tweets ON tva_tweet_id = twe_id
					JOIN social_network_accounts ON twe_destination = sna_id
					WHERE tva_validator = :use_id
					GROUP BY sna_id";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$statsB = $statement->fetchAll();

			foreach($statsB as $stat) {
				$found = false;
				foreach($stats as $index => $curstat) {
					if ($curstat["sna_id"] == $stat["sna_id"]) {
						$found = true;
						$stats[$index]["sna_validations"] = $stat["sna_validations"];
						$stats[$index]["sna_scores"] = $stat["sna_scores"];
					}
				}

				if (!$found) {
					$stat["sna_tweets"] = 0;
					$stats[] = $stat;
				}
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $stats;
	}
}