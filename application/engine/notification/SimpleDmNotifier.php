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

class SimpleDmNotifier {

	function notifyAskForModification($account, $author, $tweet) {
		global $tweetBo;
		global $config;

		$noticeTweet = array("twe_supports" => '["twitter"]');
		$noticeTweet["twe_destination"] = $account["sna_name"];
		$noticeTweet["twe_destination_id"] = $account["sna_id"];

//		$noticeTweet["twe_content"] = "D ". $author["use_login"] . " un message demande à être relu pour modification " . $config["base_url"] . "validation.php";
//		$tweetBo->sendTweet($noticeTweet);
		$noticeTweet["twe_content"] = "un message demande à être relu pour modification " . $config["base_url"] . "validation.php";
		$tweetBo->sendDMOnTwitter($author["use_login"], $noticeTweet);
	}

	function notifyValidationLink($account, $validator, $tweet, $validationLink) {
		global $tweetBo;
		global $config;

		$noticeTweet = array("twe_supports" => '["twitter"]');
		$noticeTweet["twe_destination"] = $account["sna_name"];
		$noticeTweet["twe_destination_id"] = $account["sna_id"];

		if ($tweet["twe_to_retweet"]) {
			$retweet = json_decode($tweet["twe_to_retweet"], true);

			if ($tweet["twe_content"]) {

			}
			else {
				$tweetContent = lang("add_tweet_mail_only_a_retweet", false, $validator["use_language"]);
				$tweetContent .= " https://twitter.com/" . $retweet["user"]["screen_name"] . "/status/" . $retweet["id_str"];
				$tweetContent .= "\n";
				$wteetContent .= $retweet["text"];
			}
		}
		else {
			$tweetContent = $tweet["twe_content"];
		}

//		$noticeTweet["twe_content"] = "D ". $validator["use_login"] . " un message en attente de validation vous attend sur " . $config["base_url"] . " : \n" .  $tweetContent;
//		$tweetBo->sendTweet($noticeTweet);
		$noticeTweet["twe_content"] = "un message en attente de validation vous attend sur " . $config["base_url"] . " : \n" .  $tweetContent;
		$tweetBo->sendDMOnTwitter($validator["use_login"], $noticeTweet);
	}
}
?>