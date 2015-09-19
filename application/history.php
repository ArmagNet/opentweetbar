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
include_once("header.php");
require_once("engine/bo/MediaBo.php");

$mediaBo = MediaBo::newInstance($connection);

$tweets = $tweetBo->getTweets($accounts, array('validated','expired','croned','rejected'));
$tweets = TweetBo::indexValidations($tweets, $user);
$tweetsByAccount = TweetBo::accounted($tweets);

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_history"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("history_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<?php 	foreach ($tweetsByAccount as $account => $tweets) {?>
	<div class="panel panel-default">
		<!-- Default panel contents -->
		<div class="panel-heading">
			<?php echo str_replace("{account}", "$account", lang("history_account_title")); ?>
		</div>

		<!-- Table -->
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo lang("property_tweet"); ?></th>
					<th class="authorColumn"><?php echo lang("property_author"); ?></th>
					<th class="dateColumn"><?php echo lang("property_date"); ?></th>
					<th class="validationColumn"><?php echo lang("property_validators"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 	foreach($tweets as $tweet) {

					$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));

				?>
				<tr id="row_<?php echo $tweet["twe_id"]; ?>" <?php

							switch($tweet["twe_status"]) {
								case "expired":
									echo ' class="warning" ';
									break;
								case "croned":
									echo ' class="info" ';
									break;
								case "rejected":
									echo ' class="danger" ';
									break;
							}
					?>>
					<td class="vertical-middle">
						<?php if ($tweet["twe_to_retweet"]) {?>
							<?php echo lang("history_retweet_proposition"); ?>

							<div id="retweetDiv-<?php echo $tweet["twe_id"];?>">
<script type="text/javascript">

$(function() {
	var tweetHtml = getHtmlTweet(<?php echo $tweet["twe_to_retweet"]; ?>);
	$("#retweetDiv-<?php echo $tweet["twe_id"];?>").html(tweetHtml);
});

</script>
							</div>

						<?php }?>
						<?php echo $tweet["twe_content"]; ?>

						<?php 	if (count($medias)) {?>
							<br />
							<?php foreach($medias as $media) {
								$hash = UserBo::computePassword($media["med_id"]);
								$mediaUrl = "do_loadMedia.php?med_id=" . $media["med_id"];
								$mediaUrl .= "&med_hash=" . $hash;
							 ?>
							 	<a href="<?php echo $mediaUrl; ?>"
							 		data-type="image"
							 		data-toggle="lightbox"
							 		data-footer="<?php echo htmlentities($tweet["twe_content"]); ?>"
							 		data-gallery="<?php echo $account?>">
									<img src="<?php echo $mediaUrl; ?>" style="max-width: 32px; max-height: 32px; " />
								</a>
							<?php }?>
						<?php 	}?>

						<?php 	if ($tweet["twe_cron_datetime"] && $tweet["twe_status"] == "croned") {?>
							<br/><span class="text-muted"><span class="glyphicon glyphicon-calendar"></span>
						<?php		$date = new DateTime($tweet["twe_cron_datetime"]);

									echo str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("history_cron_datetime_format")));?>
								</span>
						<?php	}?>
					</td>
					<td class="vertical-middle">
						<?php 	if ($tweet["twe_author_id"]) {?>
						<a href="https://twitter.com/<?php echo $tweet["twe_author"]; ?>" target="_blank"><?php echo $tweet["twe_author"]; ?></a>
						<?php 	} else { ?>
						<?php echo $tweet["twe_anonymous_nickname"]; ?> <?php echo lang("validation_anonymous"); ?>
						<?php 	}?>
					</td>
					<td class="vertical-middle"><?php
						if ($tweet["twe_validation_datetime"]) {
							$date = new DateTime($tweet["twe_validation_datetime"]);

							echo str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("datetime_format")));
						}
					?></td>
					<td class="vertical-middle">
						<div class="dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" id="validators_5" data-toggle="dropdown" aria-expanded="true">
								<?php echo lang("history_button_validators"); ?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu" aria-labelledby="validators_5">
								<?php 	foreach($tweet["validators"] as $validator) {?>
								<li role="presentation">
									<a href="https://twitter.com/<?php echo $validator; ?>" target="_blank"><?php echo $validator; ?></a>
								</li>
								<?php 	}?>
							</ul>
						</div>
					</td>
				</tr>
				<?php 	}?>
			</tbody>
		</table>

		<?php echo addPagination(count($tweets), 5); ?>
	</div>

	<br>

	<?php 	}?>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<templates>
	<blockquote aria-template-id="template-tweet" class="template" data-tweet-id="${source_id_str}">
		<a href="https://twitter.com/${source_user_screen_name}">${source_user_name}
			<small>@${source_user_screen_name}</small>
		</a>
		<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}">
			<small>${source_created_at}</small>
		</a>
		<p>${source_text}</p>
	</blockquote>
	<blockquote aria-template-id="template-retweet" class="template" data-tweet-id="${source_id_str}">
		<a href="https://twitter.com/${source_user_screen_name}">${source_user_name}
			<small>@${source_user_screen_name}</small>
		</a>
		<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}">
			<small>${source_created_at}</small>
		</a>
		<p>${source_text}</p>
		<small>
			<a href="https://twitter.com/${tweet_user_screen_name}">
				<?php echo lang("property_retweet_by"); ?>
			</a>
		</small>
	</blockquote>
<templates>

<?php include("footer.php");?>
</body>
</html>