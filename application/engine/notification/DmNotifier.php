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

class DmNotifier {

	function notifyAskForModification($account, $author, $tweet) {
		global $tweetBo;
		global $config;

		$noticeTweet = array("twe_supports" => '["twitter"]');
		$noticeTweet["twe_destination"] = $account["sna_name"];
		$noticeTweet["twe_destination_id"] = $account["sna_id"];

		$noticeTweet["twe_content"] = "D ". $author["use_login"] . " un message demande à être relu pour modification " . $config["base_url"] . "validation.php";
		$tweetBo->sendTweet($noticeTweet);
	}

	function notifyValidationLink($account, $validator, $tweet, $validationLink) {
		global $tweetBo;
		global $config;

		$noticeTweet = array("twe_supports" => '["twitter"]');
		$noticeTweet["twe_destination"] = $account["sna_name"];
		$noticeTweet["twe_destination_id"] = $account["sna_id"];

		$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " un message en attente de validation vous attend : ";
		$tweetBo->sendTweet($noticeTweet);
		time_nanosleep(0, 300000000);

		$tweetContent = "";
		if ($tweet["twe_to_retweet"]) {
			$retweet = json_decode($tweet["twe_to_retweet"], true);

			if ($tweet["twe_content"]) {

			}
			else {
				$tweetContent = "Proposition de retweet";
				$tweetContent .= " https://twitter.com/" . $retweet["user"]["screen_name"] . "/status/" . $retweet["id_str"];
				$tweetContent .= "\n";
				$wteetContent .= $retweet["text"];
			}
		}
		else {
			$tweetContent = $tweet["twe_content"];
		}

		$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " " . $tweetContent;

		if (strlen($noticeTweet["twe_content"]) > 140) {
			$noticeTweet["twe_content"] = substr($noticeTweet["twe_content"], 0, 140);
		}

		$tweetBo->sendTweet($noticeTweet);
		time_nanosleep(0, 300000000);

		$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " Pour valider : " . $validationLink;
		$tweetBo->sendTweet($noticeTweet);
	}
}
?>