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

class MailNotifier {

	function notifyValidationLink($account, $validator, $tweet, $validationLink) {
		global $config;

		$mail = getMailInstance();

		$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
		$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
		$mail->addAddress($validator["use_mail"]);

		$mailMessage = lang("add_tweet_mail_content", false, $validator["use_language"]);
		$mailMessage = str_replace("{validationLink}", $validationLink, $mailMessage);
		$mailMessage = str_replace("{login}", $validator["use_login"], $mailMessage);

		$tweetContent = "";
		if ($tweet["twe_to_retweet"]) {
			$retweet = json_decode($tweet["twe_to_retweet"], true);

			if ($tweet["twe_content"]) {

			}
			else {
				$tweetContent = lang("add_tweet_mail_only_a_retweet");
				$tweetContent .= " https://twitter.com/" . $retweet["user"]["screen_name"] . "/" . $retweet["id_str"];
				$tweetContent .= "\n";
				$wteetContent .= $retweet["text"];
			}
		}
		else {
			$tweetContent = $tweet["twe_content"];
		}
		$mailMessage = str_replace("{tweet}", $tweetContent, $mailMessage);

		$mailMessage = str_replace("{account}", $account["sna_name"], $mailMessage);
		$mailSubject = lang("add_tweet_mail_subject", false, $validator["use_language"]);

		$mail->Subject = mb_encode_mimeheader(utf8_decode($mailSubject), "ISO-8859-1");
		$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
		$mail->AltBody = utf8_decode($mailMessage);

		$mail->send();
	}

}
?>