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

class LogActionBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new LogActionBo($pdo);
	}

	function getNumberOfFails($ip, $duration) {
		$args = array("lac_ip" => $ip);
		$query = "	SELECT
						count(lac_id) as lac_fails
					FROM log_actions
					WHERE
						lac_ip = :lac_ip
					AND	UNIX_TIMESTAMP(lac_timestamp) + $duration > UNIX_TIMESTAMP()
					AND	lac_status = 0 ";
		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				return $results[0]["lac_fails"];
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return 0;
	}

	function addLogAction(&$logAction) {
		$query = "	INSERT INTO log_actions
						(lac_label, lac_status, lac_login, lac_ip, lac_timestamp)
					VALUES
						(:lac_label, :lac_status, :lac_login, :lac_ip, NOW()) ";

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($logAction);
			$logAction["lac_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}
}