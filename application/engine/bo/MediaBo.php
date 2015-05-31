<?php /*
	Copyright 2014-2015 Cédric Levieux, Jérémy Collot, ArmagNet

	This file is part of OpenMediaBar.

    OpenMediaBar is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenMediaBar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with OpenMediaBar.  If not, see <http://www.gnu.org/licenses/>.
*/

class MediaBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new MediaBo($pdo);
	}

	function create(&$media) {
		$query = "	INSERT INTO medias () VALUES ()	";
		$args = array();

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$media["med_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update(&$media) {
		$query = "	UPDATE medias SET ";

		$separator = "";
		foreach($media as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE med_id = :med_id ";

		//		echo showQuery($query, $member);

		$statement = $this->pdo->prepare($query);
		$statement->execute($media);
	}

	function save(&$media) {
		if (!isset($media["med_id"]) || !$media["med_id"]) {
			$this->create($media);
		}

		$this->update($media);
	}

	function getMedias($filters) {
		$args = array();

		$query = "	SELECT *
					FROM medias";

		if (isset($filters["tme_tweet_id"]) && $filters["tme_tweet_id"]) {
			$query .= "	JOIN tweet_medias ON tme_media_id = med_id ";
		}

		$query .= "	WHERE 1 = 1 ";

		if (isset($filters["med_sna_id"]) && $filters["med_sna_id"]) {
			$query .= "	AND med_sna_id = :med_sna_id ";
			$args["med_sna_id"] = $filters["med_sna_id"];
		}

		if (isset($filters["tme_tweet_id"]) && $filters["tme_tweet_id"]) {
			$query .= "	AND tme_tweet_id = :tme_tweet_id ";
			$args["tme_tweet_id"] = $filters["tme_tweet_id"];
		}

		$query .= "	ORDER BY med_name";

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, array_merge($accountIds, $status));

		try {
			$statement->execute($args);
			return $statement->fetchAll();
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getMedia($id) {
		$query = "	SELECT *
					FROM medias
					WHERE 1 = 1 ";

		$query .= " AND med_id = $id";
		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);
		try {
			$statement->execute();
			$media =  $statement->fetchAll();

			foreach($media as $id => $mymedia) {
				return $mymedia;
			}
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}
}