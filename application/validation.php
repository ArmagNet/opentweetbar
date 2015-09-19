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

	<?php 	foreach ($tweetsByAccount as $account => $tweets) {?>
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
					<th class="validationColumn"><?php echo lang("property_validation"); ?></th>
					<th class="actionColumn"><?php echo lang("property_actions"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 	foreach($tweets as $tweet) {

					$medias = $mediaBo->getMedias(array("tme_tweet_id" => $tweet["twe_id"]));

				?>
				<tr id="row_<?php echo $tweet["twe_id"]; ?>">
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

						<input id="hash_<?php echo $tweet["twe_id"]; ?>" type="hidden"
						value="<?php echo TweetBo::hash($tweet, $userId); ?>" />
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
					<td class="vertical-middle">
						<div class="progress margin-bottom-0">
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
					<td class="vertical-middle"><?php 				if ($tweet["twe_author_id"] == $userId || isAdministrator($tweet["twe_destination_id"])) {?>
						<button id="delete_<?php echo $tweet["twe_id"]; ?>" class="btn btn-danger" type="button">
							<?php echo lang("common_delete"); ?> <span class="glyphicon glyphicon-remove"></span>
						</button> <?php 	}
											if ($tweet["validation"][1] == 0 && $tweet["twe_author_id"] != $userId) {?>
						<button id="validate_<?php echo $tweet["twe_id"]; ?>" class="btn btn-success" type="button">
							<?php echo lang("common_validate"); ?> <span class="glyphicon glyphicon-ok"></span>
						</button> <?php 	}?>
					</td>
				</tr>
				<?php 	}?>
			</tbody>
		</table>

		<?php echo addPagination(count($tweets), 5); ?>
	</div>

	<br>

	<?php 	}?>

	<?php echo addAlertDialog("okDeleteTweetAlert", lang("okDeleteTweet"), "success"); ?>
	<?php echo addAlertDialog("okValidateTweetAlert", lang("okValidateTweet"), "info"); ?>
	<?php echo addAlertDialog("okFinalValidateTweetAlert", lang("okFinalValidateTweet"), "success"); ?>

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
<script>

	function getElementId(element) {
		var id = element.attr("id");

		if (id.lastIndexOf("_") != -1) {
			id = id.substring(id.lastIndexOf("_") + 1);
		}

		return id;
	}

	function deleteTweetUI(id) {
		$("#row_" + id).fadeOut(400, function() {
			var table = $("#row_" + id).parents("table");
			var nav = table.siblings("nav");
			var currentPage = nav.find("li.active").text();

			$("#row_" + id).remove();
			var badge = $("#validationMenuItem .badge");
			var value = badge.text() - 1;
			badge.text(value);
			if (value == 0) {
				badge.hide();
			}

			// TODO count last trs and remove the last page if needed
			if (currentPage) {
				showPage(table, currentPage);
			}
		});
	}

	$(function() {
		$(".btn-danger").click(function() {
			var id = getElementId($(this));

			var myform = {	"tweetId" : id,
							"userId" : '<?php echo $userId; ?>',
							"hash" : $("#hash_" + id).val()};

			$.post("do_deleteTweet.php", myform, function(data) {
				if (data.ok) {
					$("#okDeleteTweetAlert").show().delay(2000).fadeOut(1000);
					deleteTweetUI(id);
				}
			}, "json");
		});

		$(".btn-success").click(function() {
			var id = getElementId($(this));

			var myform = {	"tweetId" : id,
							"userId" : '<?php echo $userId; ?>',
							"hash" : $("#hash_" + id).val()};
			$.post("do_validateTweet.php", myform, function(data) {
				if (data.ok) {
					$("#row_" + id + " .progress-bar-success").css("width", data.score + "%");

					if (data.validated) {
						$("#okFinalValidateTweetAlert").show().delay(2000).fadeOut(1000);
						deleteTweetUI(id);
					}
					else {
						$("#okValidateTweetAlert").show().delay(2000).fadeOut(1000);
						$("#validate_" + id).fadeOut().remove();
					}
				}
			}, "json");
		});
	});

	</script>
</body>
</html>