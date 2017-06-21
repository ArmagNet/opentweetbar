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

class AccountBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new AccountBo($pdo);
	}

	function save(&$account) {
		if (!$account["sna_id"]) {
			// Create an instance
			$args = array();
			$query = "INSERT INTO social_network_accounts (sna_id) VALUES (null) ";
			$statement = $this->pdo->prepare($query);
			$statement->execute();

			$args["sna_id"] = $this->pdo->lastInsertId();
			$account["sna_id"] = $args["sna_id"];

//			print_r($args);

			$query = "INSERT INTO sna_configuration (sco_sna_id) VALUES (:sna_id) ";
			$statement = $this->pdo->prepare($query);
			$statement->execute($args);

			$query = "INSERT INTO sna_twitter_configuration (stc_sna_id) VALUES (:sna_id) ";
			$statement = $this->pdo->prepare($query);
			$statement->execute($args);
			
			$query = "INSERT INTO sna_facebook_page_configuration (sfp_sna_id) VALUES (:sna_id) ";
			$statement = $this->pdo->prepare($query);
			$statement->execute($args);
			
			$query = "INSERT INTO sna_mastodon_configuration (smc_sna_id) VALUES (:sna_id) ";
			$statement = $this->pdo->prepare($query);
			$statement->execute($args);
		}

//		echo "Post insert \n";

		$administrators = $account["administrators"];
		unset($account["administrators"]);
		$validatorGroups = $account["validatorGroups"];
		unset($account["validatorGroups"]);

		// Update the row
		$query = "	UPDATE social_network_accounts, sna_configuration, sna_twitter_configuration, sna_facebook_page_configuration, sna_mastodon_configuration
					SET
						sna_name = :sna_name,
						sco_validation_score = :sco_validation_score,
						sco_anonymous_permitted = :sco_anonymous_permitted,
						sco_anonymous_password = :sco_anonymous_password,

						stc_api_key = :stc_api_key,
						stc_api_secret = :stc_api_secret,
						stc_access_token = :stc_access_token,
						stc_access_token_secret = :stc_access_secret,

						sfp_page_id = :sfp_page_id,
						sfp_access_token = :sfp_access_token,

						smc_url = :smc_url,
						smc_client_id = :smc_client_id,
						smc_client_secret = :smc_client_secret,
						smc_user_token = :smc_user_token,
						smc_token_type = :smc_token_type

						WHERE sna_id = :sna_id 
							AND sco_sna_id = :sna_id 
							AND stc_sna_id = :sna_id 
							AND sfp_sna_id = :sna_id
							AND smc_sna_id = :sna_id
";
		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $account);

		$account["stc_access_secret"] = $account["stc_access_token_secret"];
		unset($account["stc_access_token_secret"]);

		$statement->execute($account);

		// Update the administrator list
		$args = array();
		$args["sna_id"] = $account["sna_id"];
		$query = "	DELETE FROM administrators WHERE adm_sna_id = :sna_id";
		$statement = $this->pdo->prepare($query);
		$statement->execute($args);

//		print_r($args);

//		echo "\n";

		$query = "	INSERT administrators (adm_sna_id, adm_user_id) VALUES (:sna_id, :use_id)";
		$statement = $this->pdo->prepare($query);

		foreach($administrators as $administratror) {
			$args["use_id"] = $administratror["use_id"];
			$statement->execute($args);
		}

		// Update the validators list
		$args = array();
		$args["sna_id"] = $account["sna_id"];
		$query = "	DELETE vgr, val
					FROM validator_groups vgr
					JOIN validators val ON val_validator_group_id = vgr_id
					WHERE vgr_sna_id = :sna_id";
		$statement = $this->pdo->prepare($query);
		$statement->execute($args);

		$query = "	INSERT validator_groups (vgr_sna_id, vgr_name, vgr_score, vgr_show_timeline) VALUES (:sna_id, :vgr_name, :vgr_score, :vgr_show_timeline)";
		$statement = $this->pdo->prepare($query);

		$vquery = "	INSERT validators (val_validator_group_id, val_user_id) VALUES (:val_validator_group_id, :val_user_id)";
		$vstatement = $this->pdo->prepare($vquery);

		foreach($validatorGroups as $validatorGroup) {
			$args["vgr_name"] = $validatorGroup["vgr_name"];
			$args["vgr_score"] = $validatorGroup["vgr_score"];
			$args["vgr_show_timeline"] = $validatorGroup["vgr_show_timeline"];
			$statement->execute($args);

			$vargs = array("val_validator_group_id" => $this->pdo->lastInsertId());

			foreach($validatorGroup["validators"] as $validator) {
				$vargs["val_user_id"] = $validator["use_id"];
				$vstatement->execute($vargs);
			}
		}
	}

	function getValidators($accountId) {
		$query = "	SELECT *
					FROM validator_groups
					JOIN validators ON val_validator_group_id = vgr_id
					JOIN users ON use_id = val_user_id
					WHERE vgr_sna_id = :sna_id";
		$args = array("sna_id" => $accountId);

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);

			$validatorGroups = array();
			$validatorGroup = array("vgr_name" => "");

			$results = $statement->fetchAll();

			foreach($results as $line) {
				if ($validatorGroup["vgr_name"] != $line["vgr_name"]) {
					$validatorGroup = array("vgr_name" => $line["vgr_name"], "validators" => array());
					$validatorGroups[] = $validatorGroup;
				}

				$validatorGroup = $validatorGroups[count($validatorGroups) - 1];
				$validatorGroup["vgr_score"] = $line["vgr_score"];
				$validatorGroup["vgr_show_timeline"] = $line["vgr_show_timeline"];
				$validatorGroup["validators"][] = array("use_id" => $line["use_id"], "use_login" => $line["use_login"]);

				$validatorGroups[count($validatorGroups) - 1] = $validatorGroup;
			}

			return $validatorGroups;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getValidator($accountId, $userId) {
		$query = "	SELECT *
					FROM validator_groups
					JOIN validators ON val_validator_group_id = vgr_id
					WHERE vgr_sna_id = :sna_id AND val_user_id = :use_id";
		$args = array("sna_id" => $accountId, "use_id" => $userId);

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchall();

			if (count($results)) {
				return $results[0];
			}
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getAnonymouslyAccessibleAccounts($excludedAccounts = array()) {
		$query = "";
		$query .= "	SELECT DISTINCT *";
		$query .= "	FROM social_network_accounts sna";
		$query .= "	JOIN sna_configuration ON sco_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_mastodon_configuration ON smc_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_facebook_page_configuration ON sfp_sna_id = sna_id";
		$query .= "	WHERE sco_anonymous_permitted = 1 ";
 
		$excludedIds = array();

		if (count($excludedAccounts)) {
			$qMarks = str_repeat('?,', count($excludedAccounts) - 1) . '?';

			$query .= " AND sna_id NOT IN ($qMarks)";

			foreach ($excludedAccounts as $excludedAccount) {
				$excludedIds[] = $excludedAccount["sna_id"];
			}
		}

		$query .= "	ORDER BY sna_name ASC";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($excludedIds);

//			echo showQuery($query, $excludedIds);

			$results = $statement->fetchAll();

			return $results;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getAccessibleAccounts($userId) {
		$query = "";
		$query .= "	SELECT DISTINCT *";
		$query .= "	FROM social_network_accounts sna";
		$query .= "	JOIN sna_configuration ON sco_sna_id = sna_id";
		$query .= "	JOIN validator_groups ON vgr_sna_id = sna_id";
		$query .= "	JOIN validators ON val_validator_group_id = vgr_id";
 		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
 		$query .= "	LEFT JOIN sna_facebook_page_configuration ON sfp_sna_id = sna_id";
 		$query .= "	LEFT JOIN sna_mastodon_configuration ON smc_sna_id = sna_id";
 		$query .= "	WHERE val_user_id = :use_id";
		$query .= "	ORDER BY sna_name ASC";

		$args = array("use_id" => $userId);

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);

			$results = $statement->fetchAll();
			return $results;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getAccountId($account) {
		$query = "	SELECT sna_id FROM social_network_accounts WHERE sna_name = :sna_name";
		$args = array("sna_name" => $account);


		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchall();

			if (count($results)) {
				return $results[0]["sna_id"];
			}
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getAccountValidators($accountId) {
		$query = "	SELECT *
					FROM users
					JOIN validators ON val_user_id = use_id
					JOIN validator_groups ON vgr_id = val_validator_group_id
					WHERE vgr_sna_id = :sna_id
					GROUP BY vgr_name, use_login";

		$args = array("sna_id" => $accountId);

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);
			$results = $statement->fetchall();

			return $results;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getAccountAdministrators($accountId) {
		$query = "";
		$query .= "	SELECT users.*";
		$query .= "	FROM social_network_accounts sna";
// 		$query .= "	LEFT JOIN sna_configuration ON sco_sna_id = sna_id";
// 		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
		$query .= "	LEFT JOIN administrators ON adm_sna_id = sna_id";
		$query .= "	LEFT JOIN users ON adm_user_id = use_id";
		$query .= "	WHERE sna_id = :sna_id";

		$args = array("sna_id" => $accountId);

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);

			$results = $statement->fetchAll();
			return $results;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getAdministratedAccounts($userId) {
		$query = "";
		$query .= "	SELECT *";
		$query .= "	FROM social_network_accounts sna";
 		$query .= "	LEFT JOIN sna_configuration ON sco_sna_id = sna_id";
 		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
 		$query .= "	LEFT JOIN sna_facebook_page_configuration ON sfp_sna_id = sna_id";
 		$query .= "	LEFT JOIN sna_mastodon_configuration ON smc_sna_id = sna_id";
 		$query .= "	LEFT JOIN administrators ON adm_sna_id = sna_id";
		$query .= "	WHERE adm_user_id = :adm_user_id";

		$args = array("adm_user_id" => $userId);

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);

			$results = $statement->fetchAll();
			return $results;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getAccount($accountId) {
		$query = "";
		$query .= "	SELECT *";
		$query .= "	FROM social_network_accounts sna";
		$query .= "	LEFT JOIN sna_configuration ON sco_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_facebook_page_configuration ON sfp_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_mastodon_configuration ON smc_sna_id = sna_id";
		$query .= "	WHERE sna_id = :sna_id";

		$args = array("sna_id" => $accountId);

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);

			$results = $statement->fetchAll();
			if (count($results)) {
				return $results[0];
			}
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}
}
