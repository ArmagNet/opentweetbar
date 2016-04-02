<?php /*
	Copyright 2014-2015 Cédric Levieux, Jérémy Collot, ArmagNet

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

class TweetBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new TweetBo($pdo);
	}

	static function urlized($tweetContent) {
		$regex = '/((https?):\/\/([a-z.\/0-9\-\_%#]*))/mi';
		preg_match_all($regex, $tweetContent, $matches, PREG_OFFSET_CAPTURE);

		//	print_r($matches);

		$result = array("urls" => array(), "content" => $tweetContent);

		foreach($matches[0] as $index => $match) {
			if (strlen($matches[3][$index][0]) > 15) {
				$result["content"] = str_replace($match[0], $matches[2][$index][0] . "://##############" . count($result["urls"]), $result["content"]);
				$result["urls"][] = $match[0];
			}
		}

		return $result;
	}

	static function cutTweet($text, &$tweets, $urls, $hasImage = false) {
		$maxLength = 140 - 7 - ($hasImage ? 24 : 0);

		if (strlen(utf8_decode($text)) > $maxLength) {
			$cutLength = regexLastIndexOf($text, '/[ ,;]/mi', $maxLength);

			$tweet = trim(substr($text, 0, $cutLength + 1));
			$tweets[] = $tweet;

			$text = trim(substr($text, $cutLength + 1));

			TweetBo::cutTweet($text, $tweets, $urls);

			return;
		}

		$tweets[] = $text;

		// add n/m
		foreach($tweets as $index => $tweet) {
			foreach($urls as $jndex => $url) {
				$tweet = str_replace("http://##############" . $jndex, $url, $tweet);
				$tweet = str_replace("https://##############" . $jndex, $url, $tweet);
			}

			$tweets[$index] = $tweet . " " . ($index + 1) . "/" . count($tweets);
		}

		return;
	}

	function getAccount($accountId) {
		$query = "";
		$query .= "	SELECT *";
		$query .= "	FROM social_network_accounts sna";
		$query .= "	LEFT JOIN sna_configuration ON sco_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_twitter_configuration ON stc_sna_id = sna_id";
		$query .= "	LEFT JOIN sna_facebook_page_configuration ON sfp_sna_id = sna_id";
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

		return array();
	}

	static function testTwitter($config) {
		include_once "engine/twitter/twitteroauth.php";

		$connection = new TwitterOAuth($config["api_key"], $config["api_secret"], $config["access_token"], $config["access_token_secret"]);
		$parameters = array('count' => 1);
		$status = $connection->get('statuses/home_timeline', $parameters);

		return $status;
	}

	static function getTweetFromTwitter($account, $tweetId) {
		include_once "engine/twitter/twitteroauth.php";

		$key = $account["stc_api_key"];
		$secret = $account["stc_api_secret"];
		$token = $account["stc_access_token"];
		$token_secret = $account["stc_access_token_secret"];

		//		print_r($account);

		$connection = new TwitterOAuth($key, $secret, $token, $token_secret);

		$parameters = array("id" => $tweetId);

		$result = $connection->get('statuses/show', $parameters);

		return $result;
	}

	static function getOembed($account, $tweetId) {
		include_once "engine/twitter/twitteroauth.php";

		$key = $account["stc_api_key"];
		$secret = $account["stc_api_secret"];
		$token = $account["stc_access_token"];
		$token_secret = $account["stc_access_token_secret"];

		//		print_r($account);

		$connection = new TwitterOAuth($key, $secret, $token, $token_secret);

		$parameters = array("id" => $tweetId, "omit_script" => 1);

		$result = $connection->get('statuses/oembed', $parameters);

		return $result->html;
	}

	static function getCachedTimeline($account, $sinceId, $numberOfTweets = 20) {
		if (!file_exists("cache/" . $account["sna_id"])) return null;

		$tweetFiles = scandir("cache/" . $account["sna_id"], SCANDIR_SORT_DESCENDING);
		$finalTweetFiles = array();

		foreach($tweetFiles as $tweetFile) {
			$tweetId = str_replace(".tweet", "", $tweetFile);

			if (strlen($tweetId) > strlen($sinceId) ||									// We change the range
					(strlen($tweetId) == strlen($sinceId) && $tweetId > $sinceId)) {	// We are in the same range
				$finalTweetFiles[] = $tweetFile;
				if ($numberOfTweets && $numberOfTweets == count($finalTweetFiles)) {
					break;
				}
			}
		}

		$timeline = array();
		foreach($finalTweetFiles as $tweetFile) {
			$timeline[] = json_decode(file_get_contents("cache/" . $account["sna_id"] . "/". $tweetFile));
		}

		if (count($timeline)) {
			return $timeline;
		}

		return null;
	}

	static function cacheTimeline($account, $timeline) {
		if (!file_exists("cache/" . $account["sna_id"])) {
			mkdir("cache/" . $account["sna_id"], 0770);
			file_put_contents("cache/" . $account["sna_id"] . "/index.html", "");
		}

		foreach($timeline as $tweet) {
			$filepath = "cache/" . $account["sna_id"] . "/" . $tweet->id_str . ".tweet";
			file_put_contents($filepath, json_encode($tweet));
		}
	}

	static function getTimeline($account, $sinceId = null, $numberOfTweets = 20) {
		// We check the cache of sinceId is given
		if ($sinceId) {
			$timeline = TweetBo::getCachedTimeline($account, $sinceId, $numberOfTweets = 20);

			if ($timeline) return $timeline;
		}

		include_once "engine/twitter/twitteroauth.php";

		$key = $account["stc_api_key"];
		$secret = $account["stc_api_secret"];
		$token = $account["stc_access_token"];
		$token_secret = $account["stc_access_token_secret"];

		//		print_r($account);

		$connection = new TwitterOAuth($key, $secret, $token, $token_secret);

		$parameters = array();
			if ($sinceId) {
			$parameters["since_id"] = $sinceId;
		}
		if ($numberOfTweets) {
			$parameters["count"] = $numberOfTweets;
		}

		$timeline = $connection->get('statuses/home_timeline', $parameters);

		if ($timeline) {
			TweetBo::cacheTimeline($account, $timeline);
		}

		return $timeline;
	}

	function sendTweet($tweet) {
		error_log("send tweet");

		$supports = json_decode($tweet["twe_supports"]);

		foreach($supports as $support) {
			switch($support) {
				case "twitter":
					$this->sendTweetOnTwitter($tweet);
					break;
				case "facebookPage":
					$this->sendTweetOnFacebookPage($tweet);
					break;
			}
		}
	}

	function sendTweetOnFacebookPage($tweet) {
		error_log("send tweet on facebook");

		include_once "engine/facebook/facebook.php";
		include_once "engine/bo/MediaBo.php";
		include_once "engine/bo/UserBo.php";

		$account = $this->getAccount($tweet["twe_destination_id"]);

		$pageId = $account["sfp_page_id"];
		$accessToken = $account["sfp_access_token"];

		$facebookApiClient = new FacebookApiClient($accessToken);

		$medias = array();

		if (isset($tweet["twe_id"])) {
			$mediaBo = MediaBo::newInstance($this->pdo);
			$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));
		}

		$facebookImageIds = array();

//		if (count($medias) == 1) {
		if (count($medias)) {
			global $config;
			$media = $medias[0];
			$media["med_hash"] = UserBo::computePassword($media["med_id"]);
			$url = $config["base_url"] . "/do_loadMedia.php?med_id=" . $media["med_id"] . "&med_hash=" . $media["med_hash"];
//			$response = $facebookApiClient->postImageByUrl($pageId, $url);

			error_log("Url : $url");
// 			error_log(print_r($response, true));

// 			$id = $response["id"];

//			$facebookImageIds[] = $id;
			$facebookImageIds[] = $url;
		}
// 		else if (count($medias) > 1) {
// 			global $config;
// 			foreach($medias as $index => $media) {
// 				if ($index >= 5) continue;

// 				$media["med_hash"] = UserBo::computePassword($media["med_id"]);

// 				$url = $config["base_url"] . "/do_loadMedia.php?med_id=" . $media["med_id"] . "&med_hash=" . $media["med_hash"];

// 				$facebookImageIds[] = $url;
// 			}
// 		}

		$response = $facebookApiClient->postMessage($pageId, $tweet["twe_content"], $facebookImageIds);

		$updateTweet = array("twe_id" => $tweet["twe_id"]);
		$updateTweet["twe_facebook_page_id"] = $response["id"];

		$this->update($updateTweet);

		error_log(print_r($response, true));
	}

	function sendTweetOnTwitter($tweet) {
		error_log("send tweet on twitter");

		include_once "engine/twitter/twitteroauth.php";
		include_once "engine/bo/MediaBo.php";

		$account = $this->getAccount($tweet["twe_destination_id"]);

		$key = $account["stc_api_key"];
		$secret = $account["stc_api_secret"];
		$token = $account["stc_access_token"];
		$token_secret = $account["stc_access_token_secret"];

		//		print_r($account);

		$connection = new TwitterOAuth($key, $secret, $token, $token_secret);

		$twitterMediaIds = array();
		$medias = array();

		if (isset($tweet["twe_id"])) {
			$mediaBo = MediaBo::newInstance($this->pdo);
			$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));
		}

		// We change the url for media sending
		$connection->host = "https://upload.twitter.com/1.1/";

		foreach($medias as $media) {
			$parameters = array('media_data' => base64_encode($media["med_content"]));
			$status = $connection->post('media/upload', $parameters);

			$twitterMediaIds[] = $status->media_id;
		}

		// We change back the url for tweet sending
		$connection->host = "https://api.twitter.com/1.1/";
		$status = null;

		if ($tweet["twe_to_retweet"]) {
			$retweet = json_decode($tweet["twe_to_retweet"], true);
			$retweetId = $retweet["id_str"];

			//			error_log("Will retweet $retweetId");

			if ($tweet["twe_content"]) {
				//				error_log("with content");
			}
			else {
				//				error_log("without content");
				$parameters = array('id' => $retweetId);

				$status = $connection->post('statuses/retweet/' . $retweetId, $parameters);
			}
		}
		else {
			//			error_log("Will send a tweet");

			$result = TweetBo::urlized($tweet["twe_content"]);

			if (strlen(utf8_decode($result["content"])) <= 140 - 24 * (count($twitterMediaIds) ? 1 : 0)) {
				$parameters = array('status' => $tweet["twe_content"]);

				if (count($twitterMediaIds)) {
					$parameters["media_ids"] = implode(",", $twitterMediaIds);
				}

				$status = $connection->post('statuses/update', $parameters);
			}
			else {
				include_once "engine/utils/StringUtils.php";

				$contents = array();
				TweetBo::cutTweet($result["content"], $contents, $result["urls"], count($twitterMediaIds) ? true : false);

				foreach($contents as $index => $content) {
					$parameters = array('status' => $content);

					if (count($twitterMediaIds) && $index == 0) {
						$parameters["media_ids"] = implode(",", $twitterMediaIds);
					}

					$status = $connection->post('statuses/update', $parameters);

					time_nanosleep(0, 500000000);
				}
			}
		}

		if ($status && isset($status->id_str)) {
			$updateTweet = array("twe_id" => $tweet["twe_id"]);
			$updateTweet["twe_twitter_id"] = $status->id_str;

			$this->update($updateTweet);
		}

//		error_log(print_r($response, true));
		//		print_r($status);
	}

	static function getProfileBanner($account) {
		include_once "engine/twitter/twitteroauth.php";

		$key = $account["stc_api_key"];
		$secret = $account["stc_api_secret"];
		$token = $account["stc_access_token"];
		$token_secret = $account["stc_access_token_secret"];

		//		print_r($account);

		$connection = new TwitterOAuth($key, $secret, $token, $token_secret);

		$parameters = array();

		$result = $connection->get('account/verify_credentials', $parameters);

		error_log(print_r($result, true));

		return $result->profile_banner_url;
	}

	function updateStatus($tweet, $status) {
		$args = array("twe_id" => $tweet["twe_id"], "twe_status" => $status);
		$query = "	UPDATE tweets SET
						twe_status = :twe_status
					WHERE
						twe_id = :twe_id";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($args);

			return true;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function addTweet(&$tweet) {

		$mediaIds = array();

		if (isset($tweet["twe_media_ids"])) {
			$mediaIds = $tweet["twe_media_ids"];
			unset($tweet["twe_media_ids"]);
		}

		$query = "	INSERT INTO tweets
						(twe_author, twe_anonymous_nickname, twe_anonymous_mail, twe_destination,
							twe_content, twe_validation_score, twe_validation_duration, twe_supports,
							twe_cron_datetime, twe_creation_datetime, twe_to_retweet)
					VALUES
						(:twe_author, :twe_anonymous_nickname, :twe_anonymous_mail, :twe_destination,
							:twe_content, :twe_validation_score, :twe_validation_duration, :twe_supports,
							:twe_cron_datetime, :twe_creation_datetime, :twe_to_retweet) ";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($tweet);
			$tweet["twe_id"] = $this->pdo->lastInsertId();

			$mediaQuery = "	INSERT tweet_medias
								(tme_tweet_id, tme_media_id)
							VALUES
								(:tme_tweet_id, :tme_media_id)";

			$mediaStatement = $this->pdo->prepare($mediaQuery);
			foreach($mediaIds as $mediaId) {
				$tweetMedia = array("tme_tweet_id" => $tweet["twe_id"],
									"tme_media_id" => $mediaId);
				$mediaStatement->execute($tweetMedia);
			}

			$tweet["twe_media_ids"] = $mediaIds;

			return true;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update(&$tweet) {
		$query = "	UPDATE tweets SET ";

		$separator = "";
		foreach($tweet as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", \n";
		}

		$query .= "	WHERE twe_id = :twe_id ";

//		echo showQuery($query, $tweet);

		$statement = $this->pdo->prepare($query);
		$statement->execute($tweet);
	}

	function addValidation(&$validation) {
		$query = "	INSERT INTO tweet_validations
						(tva_validator, tva_tweet_id, tva_status, tva_score, tva_ip, tva_referer, tva_datetime, tva_motivation)
					VALUES
						(:tva_validator, :tva_tweet_id, :tva_status, :tva_score, :tva_ip, :tva_referer, :tva_datetime, :tva_motivation) ";

		$statement = $this->pdo->prepare($query);
		try {
			$statement->execute($validation);
			$validation["tva_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function removeValidation($validation) {
		$query = "	DELETE FROM tweet_validations
					WHERE
						tva_id = :tva_id ";

		$args = array();
		$args["tva_id"] = $validation["tva_id"];

//		echo showQuery($query, $args);

		$statement = $this->pdo->prepare($query);
		$statement->execute($args);

		return true;
	}

	function getCronedTweets($limitDate) {
		$args = array("twe_cron_datetime" => $limitDate);
		$query = "	SELECT twe_id, twe_content, twe_to_retweet, twe_validation_score,
						twe_supports, twe_status, twe_ask_modification
						twe_anonymous_mail, twe_anonymous_nickname,
						twe_creation_datetime,
						twe_cron_datetime,
						twe_validation_duration,
						sna_name as twe_destination,
						sna_id as twe_destination_id
						FROM tweets
						LEFT JOIN social_network_accounts ON tweets.twe_destination = sna_id
						LEFT JOIN users authors ON authors.use_id = twe_author
						WHERE tweets.twe_status = 'croned' AND twe_cron_datetime < :twe_cron_datetime ";

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			return $statement->fetchAll();
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getTweets($accounts, $status = "inValidation", $withValidation = true, $tweetId = null) {

		$accountIds = array();
		foreach ($accounts as $account) {
			if (isset($account["sna_id"])) {
				$accountIds[] = $account["sna_id"];
			}
		}

		if (!is_array($status)) {
			$status = array($status);
		}

		$query = "	SELECT twe_id, twe_content, twe_to_retweet, twe_validation_score,
						twe_supports, twe_status, twe_ask_modification,
						twe_anonymous_mail, twe_anonymous_nickname,
						twe_creation_datetime,
						twe_cron_datetime,
						twe_validation_duration,
						sna_name as twe_destination,
						sna_id as twe_destination_id,
						authors.use_login as twe_author,
						authors.use_id as twe_author_id,
						authors.*,
						tva_datetime,
						tva_score,
						tva_status, tva_motivation,
						validators.use_login as tva_validator,
						validators.use_id as tva_validator_id
					FROM tweets";

		$query .= " LEFT JOIN social_network_accounts ON tweets.twe_destination = sna_id";
		$query .= " LEFT JOIN users authors ON authors.use_id = twe_author";

		if ($withValidation) {
			$query .= "	LEFT JOIN tweet_validations ON tva_tweet_id = twe_id";
			$query .= " LEFT JOIN users validators ON validators.use_id = tva_validator";
		}

		if (count($accountIds)) {
			$qMarks = str_repeat('?,', count($accountIds) - 1) . '?';
		}
		else {
			$qMarks = "-1";
		}
		$qMarks2 = str_repeat('?,', count($status) - 1) . '?';

		$query .= " WHERE tweets.twe_destination IN ($qMarks) AND tweets.twe_status IN ($qMarks2)";

		if ($tweetId) {
			$query .= "	AND twe_id = $tweetId ";
		}

		$query .= "	ORDER BY sna_name";

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, array_merge($accountIds, $status));

		try {
			$statement->execute(array_merge($accountIds, $status));
			return $statement->fetchAll();
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getTweet($id) {
		$query = "	SELECT *,
							twe_destination as twe_destination_id,
							twe_author as twe_author_id,
							tva_validator as tva_validator_id
					FROM tweets
					LEFT JOIN tweet_validations ON tva_tweet_id = twe_id";

		$query .= " WHERE twe_id = $id";
		$statement = $this->pdo->prepare($query);

		//		echo showQuery($query, $args);
		try {
			$statement->execute();
			$tweet =  $statement->fetchAll();
			$tweet = TweetBo::indexValidations($tweet, null);
			foreach($tweet as $id => $mytweet) {
				return $mytweet;
			}
		}
		catch(Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	static function hash($tweet, $userId) {
		global $config;

		$source = $tweet["twe_content"];
		$source .= "#";
		$source .= $tweet["twe_id"];
		$source .= "#";
		$source .= $userId;
		$source .= "#";
		$source .= $config["salt"];

//		return hash("sha256", $source, false);
		return hash("sha1", $source, false);
	}

	static function accounted($tweets) {
		$accountedTweets = array();

		foreach($tweets as $tweet) {
			if (!isset($accountedTweets[$tweet["twe_destination"]])) {
				$accountedTweets[$tweet["twe_destination"]]  = array();
			}
			$accountedTweets[$tweet["twe_destination"]][] = $tweet;
		}

		return $accountedTweets;
	}

	function expurgeExpired($tweets) {
		$notExpired = array();
		$expired = array();

		$now = date("Y-m-d H:i:s");

		foreach($tweets as $tweetId => $tweet) {
			if (!$tweet["twe_validation_duration"]) {
				$notExpired[$tweetId] = $tweet;
				continue;
			}

			$date = new DateTime($tweet["twe_creation_datetime"]);
			$delay = new DateInterval("PT".$tweet["twe_validation_duration"]."M");
			$date = $date->add($delay);

			$formattedDate = $date->format("Y-m-d H:i:s");

			if ($formattedDate < $now) {
				$expired[$tweetId] = $tweet;
				continue;
			}

			$nowDate = new DateTime($now);
			$duration = $nowDate->diff($date);

			$tweet["twe_validation_duration"] = $duration->format("%H:%I:%S");
			$notExpired[$tweetId] = $tweet;
		}

		foreach($expired as $tweetId => $tweet) {
			$this->updateStatus($tweet, "expired");
		}

		return $notExpired;
	}

	static function indexValidations($tweets, $userId) {
		$indexedTweets = array();

		foreach($tweets as $tweet) {
			$indexedTweets[$tweet["twe_id"]]["twe_id"] = $tweet["twe_id"];
			$indexedTweets[$tweet["twe_id"]]["twe_content"] = $tweet["twe_content"];
			$indexedTweets[$tweet["twe_id"]]["twe_to_retweet"] = $tweet["twe_to_retweet"];
			$indexedTweets[$tweet["twe_id"]]["twe_author"] = $tweet["twe_author"];
			$indexedTweets[$tweet["twe_id"]]["twe_author_id"] = $tweet["twe_author_id"];
			$indexedTweets[$tweet["twe_id"]]["twe_anonymous_mail"] = $tweet["twe_anonymous_mail"];
			$indexedTweets[$tweet["twe_id"]]["twe_anonymous_nickname"] = $tweet["twe_anonymous_nickname"];
			$indexedTweets[$tweet["twe_id"]]["twe_destination"] = $tweet["twe_destination"];
			$indexedTweets[$tweet["twe_id"]]["twe_destination_id"] = isset($tweet["twe_destination_id"]) ? $tweet["twe_destination_id"] : $tweet["twe_destination"] ;
			$indexedTweets[$tweet["twe_id"]]["twe_validation_score"] = $tweet["twe_validation_score"];
			$indexedTweets[$tweet["twe_id"]]["twe_status"] = $tweet["twe_status"];
			$indexedTweets[$tweet["twe_id"]]["twe_supports"] = $tweet["twe_supports"];
			$indexedTweets[$tweet["twe_id"]]["twe_ask_modification"] = $tweet["twe_ask_modification"];
			$indexedTweets[$tweet["twe_id"]]["twe_validation_duration"] = $tweet["twe_validation_duration"];

			if (!isset($indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"]) && $tweet["twe_creation_datetime"] && $tweet["twe_creation_datetime"] != "0000-00-00 00:00:00") {
				$indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] = $tweet["twe_creation_datetime"];
			}

			if (!isset($indexedTweets[$tweet["twe_id"]]["twe_cron_datetime"]) && isset($tweet["twe_cron_datetime"]) && $tweet["twe_cron_datetime"] != "0000-00-00 00:00:00") {
				$indexedTweets[$tweet["twe_id"]]["twe_cron_datetime"] = $tweet["twe_cron_datetime"];
			}
			else if (!isset($indexedTweets[$tweet["twe_id"]]["twe_cron_datetime"])) {
				$indexedTweets[$tweet["twe_id"]]["twe_cron_datetime"] = "";
			}

			if (!$tweet["tva_datetime"] || $tweet["tva_datetime"] == "0000-00-00 00:00:00") {
				$tweet["tva_datetime"] = "";
			}

			if (!isset($indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"])) {
				$indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"] = $tweet["tva_datetime"];
			}
			else if ($indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"] < $tweet["tva_datetime"]) {
				$indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"] = $tweet["tva_datetime"];
			}

			if (!isset($indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"])) {
				$indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] = $tweet["tva_datetime"];
			}
			else if ($indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] > $tweet["tva_datetime"]) {
				$indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] = $tweet["tva_datetime"];
			}

			if ($indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"] < $tweet["tva_datetime"]) {
				$indexedTweets[$tweet["twe_id"]]["twe_validation_datetime"] = $tweet["tva_datetime"];
			}
			if ($indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] > $tweet["tva_datetime"]) {
				$indexedTweets[$tweet["twe_id"]]["twe_creation_datetime"] = $tweet["tva_datetime"];
			}

			if (!isset($indexedTweets[$tweet["twe_id"]]["validation"])) {
				$indexedTweets[$tweet["twe_id"]]["validation"] = array(0, 0, 0);
			}

			if (!isset($indexedTweets[$tweet["twe_id"]]["validators"])) {
				$indexedTweets[$tweet["twe_id"]]["validations"] = array();
				$indexedTweets[$tweet["twe_id"]]["validators"] = array();
				$indexedTweets[$tweet["twe_id"]]["validatorIds"] = array();
			}

			$indexedTweets[$tweet["twe_id"]]["validators"][] = $tweet["tva_validator"];
			$indexedTweets[$tweet["twe_id"]]["validatorIds"][] = $tweet["tva_validator_id"];
			$indexedTweets[$tweet["twe_id"]]["validations"][] = $tweet;

			if ($tweet["tva_validator_id"] == $tweet["twe_author_id"]) {
				$indexedTweets[$tweet["twe_id"]]["validation"][0] += ($tweet["tva_score"] * ($tweet["tva_status"] == "validation" ? 1: -1));
			}
			else if ($tweet["tva_validator_id"] == $userId) {
				$indexedTweets[$tweet["twe_id"]]["validation"][1] += ($tweet["tva_score"] * ($tweet["tva_status"] == "validation" ? 1: -1));
			}
			else {
				$indexedTweets[$tweet["twe_id"]]["validation"][2] += ($tweet["tva_score"] * ($tweet["tva_status"] == "validation" ? 1: -1));
			}
		}

		krsort($indexedTweets);

		return $indexedTweets;
	}
}
