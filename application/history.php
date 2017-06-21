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
include_once("header.php");
require_once("engine/bo/MediaBo.php");

$accountIdLabels = array();
foreach ($accounts as $account) {
	$accountIdLabels[$account["sna_id"]] = $account["sna_name"];
}

$mediaBo = MediaBo::newInstance($connection);

if (isset($_REQUEST["accountId"])) {
	$strictAccounts = array();

	foreach($accounts as $account) {
		if ($account["sna_id"] == $_REQUEST["accountId"]) {
			$strictAccounts[] = $account;
		}
	}

	$accounts = $strictAccounts;
}

$tweetPage = 1;
if (isset($_REQUEST["page"])) {
	$tweetPage =	$_REQUEST["page"];
}

$numberPerPage = 5;
if (isset($_REQUEST["numberPerPage"])) {
	$numberPerPage = $_REQUEST["numberPerPage"];
}

$tweets = $tweetBo->getTweets($accounts, array('validated','expired','croned','rejected','deleted'));
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

	<?php 	foreach($accounts as $accountArray) {

				$account = $accountArray["sna_name"];
				$tweets = $tweetsByAccount[$account];

				if (!count($tweets)) continue;
	?>
	<div class="panel panel-default account" id="account-<?php echo $accountArray["sna_id"]; ?>" data-account-id="<?php echo $accountArray["sna_id"]; ?>">
		<!-- Default panel contents -->
		<div class="panel-heading">
			<?php echo str_replace("{account}", "$account", lang("history_account_title")); ?>
		</div>

		<!-- Table -->
		<div class="container table table-striped">
			<div class="row header">
					<div class="col-xs-6 col-sm-3"><?php echo lang("property_tweet"); ?></div>
					<div class="col-xs-6 col-sm-1" class="authorColumn"><?php echo lang("property_author"); ?></div>
					<div class="col-xs-6 col-xs-offset-6 col-sm-2 col-sm-offset-0" class="dateColumn"><?php echo lang("property_date"); ?></div>
					<div class="col-xs-2 col-xs-offset-6 col-sm-1 col-sm-offset-0" class="supportsColumn"><?php echo lang("property_supports"); ?></div>
					<div class="col-xs-4 col-sm-2" class="validationColumn"><?php echo lang("property_validators"); ?></div>
					<div class="col-xs-6 col-xs-offset-6 col-sm-3 col-sm-offset-0" class="actionColumn"><?php echo lang("property_actions"); ?></div>
			</div>

				<?php

				$index = -1;
				foreach($tweets as $tweet) {
					$index++;

					// After the current page
					if ($index >= $tweetPage * $numberPerPage) continue;
					// Before the current page
					if ($index < ($tweetPage - 1) * $numberPerPage) continue;

					$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));

				?>
				<div id="row_<?php echo $tweet["twe_id"]; ?>" class="tweet-row row data <?php

							switch($tweet["twe_status"]) {
								case "expired":
									echo ' warning ';
									break;
								case "croned":
									echo ' info ';
									break;
								case "deleted":
								case "rejected":
									echo ' danger ';
									break;
							}
					?>">
					<div class="col-xs-6 col-sm-3 vertical-middle tweet-cell">
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
						<?php echo str_replace("\n", "<br/>", $tweet["twe_content"]); ?>

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
					</div>
					<div class="col-xs-6 col-sm-1 vertical-middle">
						<?php 	if ($tweet["twe_author_id"]) {?>
						<a href="https://twitter.com/<?php echo $tweet["twe_author"]; ?>" target="_blank"><?php echo $tweet["twe_author"]; ?></a>
						<?php 	} else { ?>
						<?php echo $tweet["twe_anonymous_nickname"]; ?> <?php echo lang("validation_anonymous"); ?>
						<?php 	}?>
					</div>
					<div class="col-xs-6 col-sm-2 vertical-middle"><?php
						if ($tweet["twe_validation_datetime"]) {
							$date = new DateTime($tweet["twe_validation_datetime"]);

							echo str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("datetime_format")));
						}
					?></div>
					<div class="col-xs-2 col-sm-1 vertical-middle text-center"><?php
						$supports = json_decode($tweet["twe_supports"]);
//						print_r($supports);
						foreach($supports as $support) {
							switch($support) {
								case "mastodon":
									echo "<img src=\"images/mastodon.svg\" title=\"Mastodon\" data-toggle=\"tooltip\" data-placement=\"top\" style=\"height: 24px; position: relative; top: -3px;\">";
									break;
								case "twitter":
									echo "<span class=\"social grey twitter\" title=\"Twitter\" data-toggle=\"tooltip\" data-placement=\"top\" style=\"height: 30px;\"></span>";
									break;
								case "facebookPage":
									echo "<span class=\"social grey facebook\" title=\"Facebook\" data-toggle=\"tooltip\" data-placement=\"top\" style=\"height: 30px;\"></span>";
									break;
							}
						}
					?></div>
					<div class="col-xs-4 col-sm-2 vertical-middle">
						<div class="dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" id="validators_5" data-toggle="dropdown" aria-expanded="true">
								<?php echo lang("history_button_validators"); ?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu" aria-labelledby="validators_5">
								<?php 	foreach($tweet["validations"] as $validation) {?>
								<li role="presentation"
									title="<?php echo $validation["tva_motivation"]; ?>"
									data-toggle="tooltip" data-placement="left"
									class="<?php if ($validation["tva_status"] == "rejection") { echo "text-danger"; } ?>">
									<a href="https://twitter.com/<?php echo $validation["tva_validator"]; ?>" target="_blank" style="color: inherit;"><?php echo $validation["tva_validator"]; ?></a>
								</li>
								<?php 	}?>
							</ul>
						</div>
					</div>
					<div class="col-xs-6 col-sm-3 vertical-middle">

						<?php 	if ($tweet["twe_author_id"]) {?>
						<button id="fork_<?php echo $tweet["twe_id"]; ?>"
							data-account="<?php echo $account; ?>"
							data-tweet-id="<?php echo $tweet["twe_id"]; ?>" class="btn btn-primary fork-button" type="button">
							<?php echo lang("common_fork"); ?> <span class="glyphicon glyphicon-duplicate"></span>
						</button>
						<?php 	}?>
					</div>
				</div>
				<?php 	}?>
		</div>

		<?php echo addPagination(count($tweets), 5, $tweetPage, false); ?>
	</div>

	<br>

	<?php 	}?>

	<script>
		var accountIdLabels = <?php echo json_encode($accountIdLabels) ?>;
	</script>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<templates>
	<blockquote data-template-id="template-tweet" class="template" data-tweet-id="${source_id_str}">
		<a href="https://twitter.com/${source_user_screen_name}" target="_blank">${source_user_name}
			<small>@${source_user_screen_name}</small>
		</a>
		<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}" target="_blank">
			<small>${source_created_at}</small>
		</a>
		<p>${source_text}</p>
	</blockquote>
	<blockquote data-template-id="template-retweet" class="template" data-tweet-id="${source_id_str}">
		<a href="https://twitter.com/${source_user_screen_name}" target="_blank">${source_user_name}
			<small>@${source_user_screen_name}</small>
		</a>
		<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}" target="_blank">
			<small>${source_created_at}</small>
		</a>
		<p>${source_text}</p>
		<small>
			<a href="https://twitter.com/${tweet_user_screen_name}" target="_blank">
				<?php echo lang("property_retweet_by"); ?>
			</a>
		</small>
	</blockquote>
</templates>

<?php include("footer.php");?>
</body>
</html>