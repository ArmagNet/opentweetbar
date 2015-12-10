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

$accountIdLabels = array();
foreach ($accounts as $account) {
	$accountIdLabels[$account["sna_id"]] = $account["sna_name"];
}

$mediaBo = MediaBo::newInstance($connection);

$tweets = $tweetBo->expurgeExpired($tweets);
$tweetsByAccount = TweetBo::accounted($tweets);
$administratedAccounts = $accountBo->getAdministratedAccounts($userId);

function isAdministrator($accountId) {
	global $administratedAccounts;

	foreach($administratedAccounts as $administratedAccount) {
		if ($administratedAccount["sna_id"] == $accountId) {
			return true;
		}
	}

	return false;
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_validation"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("validation_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<?php 	foreach($accounts as $accountArray) {

				$account = $accountArray["sna_name"];
				$tweets = $tweetsByAccount[$account];

				if (!count($tweets)) continue;
	?>
	<div class="panel panel-default">
		<!-- Default panel contents -->
		<div class="panel-heading">
			<?php echo str_replace("{account}", $account, lang("validation_account_title")); ?>
		</div>

		<!-- Table -->
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo lang("property_tweet"); ?></th>
					<th class="authorColumn"><?php echo lang("property_author"); ?></th>
					<th class="dateColumn"><?php echo lang("property_date"); ?></th>
					<th class="supportsColumn"><?php echo lang("property_supports"); ?></th>
					<th class="validationColumn"><?php echo lang("property_validation"); ?></th>
					<th class="actionColumn"><?php echo lang("property_actions"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 	foreach($tweets as $tweet) {

					$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));

				?>
				<tr id="row_<?php echo $tweet["twe_id"]; ?>" class="tweet-row">
					<td class="vertical-middle">
						<?php if ($tweet["twe_to_retweet"]) {?>
							<?php echo lang("validation_retweet_proposition"); ?>

							<div id="retweetDiv-<?php echo $tweet["twe_id"];?>">
<script type="text/javascript">

$(function() {
	var tweetHtml = getHtmlTweet(<?php echo $tweet["twe_to_retweet"]; ?>);
	$("#retweetDiv-<?php echo $tweet["twe_id"];?>").html(tweetHtml);
});

</script>
							</div>

						<?php }?>
						<span class="tweet-content">
						<?php echo str_replace("\n", "<br/>", $tweet["twe_content"]); ?>
						</span>
						<sup><span class="glyphicon glyphicon-pencil"></span></sup>

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

						<?php 	if ($tweet["twe_ask_modification"]) {?>
							<br/><span class="text-muted ask-for-modification"><span class="glyphicon glyphicon-warning-sign"></span>
						<?php		echo lang("validation_ask_modification");?>
								</span>
						<?php	}?>

						<?php 	if ($tweet["twe_cron_datetime"]) {?>
							<br/><span class="text-muted"><span class="glyphicon glyphicon-calendar"></span>
						<?php		$date = new DateTime($tweet["twe_cron_datetime"]);

									echo str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("validation_cron_datetime_format")));?>
								</span>
						<?php	}?>

						<?php 	if ($tweet["twe_validation_duration"]) {?>
							<br/><span class="text-muted"><span class="glyphicon glyphicon-time"></span>
						<?php		echo str_replace("{duration}", $tweet["twe_validation_duration"], lang("validation_duration_remaining")); ?>
							</span>
						<?php	}?>

						<?php
							foreach($tweet["validations"] as $validation) {
								if (!$validation["tva_status"] || $validation["tva_status"] == "validation") continue;
						?>
							<br/><span class="text-muted reject-information"><span class="glyphicon glyphicon-ban-circle"></span>
						<?php		echo $validation["tva_validator"]; ?> : <?php		echo $validation["tva_motivation"]; ?>
							</span>
						<?php
							}
						?>

						<input id="hash_<?php echo $tweet["twe_id"]; ?>" type="hidden"
						value="<?php echo TweetBo::hash($tweet, $userId); ?>" />
						<input id="user_<?php echo $tweet["twe_id"]; ?>" type="hidden"
						value="<?php echo $userId; ?>" />
					</td>
					<td class="vertical-middle">
						<?php 	if ($tweet["twe_author_id"]) {?>
						<a href="https://twitter.com/<?php echo $tweet["twe_author"]; ?>" target="_blank"><?php echo $tweet["twe_author"]; ?></a>
						<?php 	} else { ?>
						<?php echo $tweet["twe_anonymous_nickname"]; ?> <?php echo lang("validation_anonymous"); ?>
						<?php 	}?>
					</td>
					<td class="vertical-middle"><?php
						if ($tweet["twe_creation_datetime"]) {
							$date = new DateTime($tweet["twe_validation_datetime"]);

							echo str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("datetime_format")));
						}
					?></td>
					<td class="vertical-middle text-center"><?php
						$supports = json_decode($tweet["twe_supports"]);
//						print_r($supports);
						foreach($supports as $support) {
							switch($support) {
								case "twitter":
									echo "<span class=\"social grey twitter\" title=\"Twitter\" data-toggle=\"tooltip\" data-placement=\"top\" style=\"height: 30px;\"></span>";
									break;
								case "facebookPage":
									echo "<span class=\"social grey facebook\" title=\"Facebook\" data-toggle=\"tooltip\" data-placement=\"top\" style=\"height: 30px;\"></span>";
									break;
							}
						}
					?></td>
					<td class="vertical-middle">
						<?php 	$totalScore = $tweet["validation"][0] + $tweet["validation"][1] + $tweet["validation"][2]; ?>
						<div class="progress margin-bottom-0"
							data-toggle="tooltip" data-placement="top"
							title="<?php echo $totalScore; ?> / <?php echo $tweet["twe_validation_score"]; ?>">
							<div class="progress-bar progress-bar-primary"
								data-toggle="tooltip" data-placement="bottom"
								title="<?php echo lang("validation_tooltip_author_validation"); ?>"
								style="width: <?php echo round($tweet["validation"][0] * 100 / $tweet["twe_validation_score"]); ?>%">
								<span class="sr-only"><?php echo round($tweet["validation"][0] * 100 / $tweet["twe_validation_score"]); ?>% Complete</span>
							</div>
							<div class="progress-bar progress-bar-success"
								data-toggle="tooltip" data-placement="bottom"
								title="<?php echo lang("validation_tooltip_mine_validation"); ?>"
								style="width: <?php echo round($tweet["validation"][1] * 100 / $tweet["twe_validation_score"]); ?>%">
								<span class="sr-only"><?php echo round($tweet["validation"][1] * 100 / $tweet["twe_validation_score"]); ?>% Complete</span>
							</div>
							<div class="progress-bar progress-bar-info"
								data-toggle="tooltip" data-placement="bottom"
								title="<?php echo lang("validation_tooltip_other_validation"); ?>"
								style="width: <?php echo round($tweet["validation"][2] * 100 / $tweet["twe_validation_score"]); ?>%">
								<span class="sr-only"><?php echo round($tweet["validation"][2] * 100 / $tweet["twe_validation_score"]); ?>% Complete</span>
							</div>
						</div>
					</td>
					<td class="vertical-middle">

						<?php 	if ($tweet["validation"][1] == 0 && ($tweet["twe_author_id"] != $userId || $tweet["validation"][0] == 0)) {?>
						<button id="validate_<?php echo $tweet["twe_id"]; ?>" class="btn btn-success validate-button" type="button">
							<?php echo lang("common_validate"); ?> <span class="glyphicon glyphicon-ok"></span>
						</button>
						<?php 	}?>

						<?php 	if ($tweet["validation"][1] == 0 && $tweet["twe_author_id"] != $userId) {?>
						<button id="reject_<?php echo $tweet["twe_id"]; ?>" class="btn btn-danger reject-button" type="button">
							<?php echo lang("common_reject"); ?> <span class="glyphicon glyphicon-ban-circle"></span>
						</button>
						<?php 	}?>

						<?php 	if ($tweet["twe_author_id"]) {?>
						<button id="fork_<?php echo $tweet["twe_id"]; ?>"
							data-account="<?php echo $account; ?>"
							data-tweet-id="<?php echo $tweet["twe_id"]; ?>" class="btn btn-primary fork-button" type="button">
							<?php echo lang("common_fork"); ?> <span class="glyphicon glyphicon-duplicate"></span>
						</button>
						<?php 	}?>

						<?php	if ($tweet["twe_author_id"] != $userId) {?>
						<button id="askForModification_<?php echo $tweet["twe_id"]; ?>" class="btn btn-warning ask-for-modification-button" type="button">
							<?php echo lang("common_ask_for_modification"); ?> <span class="glyphicon glyphicon-warning-sign"></span>
						</button>
						<?php 	}?>

						<?php	if ($tweet["twe_author_id"] == $userId || isAdministrator($tweet["twe_destination_id"])) {?>
						<button id="delete_<?php echo $tweet["twe_id"]; ?>" class="btn btn-danger delete-button" type="button">
							<?php echo lang("common_delete"); ?> <span class="glyphicon glyphicon-remove"></span>
						</button>
						<?php 	}?>

					</td>
				</tr>
				<?php 	}?>
			</tbody>
		</table>

		<?php echo addPagination(count($tweets), 5); ?>
	</div>

	<br>

	<?php 	}?>

	<?php echo addAlertDialog("okAskForModificationAlert", lang("okAskForModificationTweet"), "success"); ?>
	<?php echo addAlertDialog("okDeleteTweetAlert", lang("okDeleteTweet"), "success"); ?>
	<?php echo addAlertDialog("okValidateTweetAlert", lang("okValidateTweet"), "info"); ?>
	<?php echo addAlertDialog("okRejectTweetAlert", lang("okRejectTweet"), "info"); ?>
	<?php echo addAlertDialog("okFinalValidateTweetAlert", lang("okFinalValidateTweet"), "success"); ?>

	<script>
		var accountIdLabels = <?php echo json_encode($accountIdLabels) ?>;
	</script>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<templates>
	<div data-template-id="template-reject-tweet" class="template">
		<br/><span class="text-muted reject-information"><span class="glyphicon glyphicon-ban-circle"></span>
			<?php echo $user; ?> : ${motivation}
		</span>
	</div>
	<div data-template-id="template-ask-for-modification" class="template">
		<br/><span class="text-muted ask-for-modification"><span class="glyphicon glyphicon-warning-sign"></span>
			<?php		echo lang("validation_ask_modification");?>
		</span>
	</div>
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
<script type="text/javascript">
</script>
</body>
</html>