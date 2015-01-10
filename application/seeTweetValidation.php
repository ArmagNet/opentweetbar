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
include_once("header.php");
require_once("engine/bo/UserBo.php");

$userBo = UserBo::newInstance(openConnection());

$userId = $_REQUEST["userId"];

$user = $userBo->get($userId);

$hash = $_REQUEST["hash"];
$tweetId = $_REQUEST["tweetId"];

// The tweet id must be a numeric
if (!is_numeric($tweetId)) {
	exit();
}

$tweet = $tweetBo->getTweet($tweetId);

$trueHash = TweetBo::hash($tweet, $userId);

// The hash is verified (forged form)
if ($trueHash != $hash) {
	exit();
}

$tweets = $tweetBo->getTweets(array("sna_id" => $tweet["twe_destination_id"]), array("inValidation", "validation", "croned"), true, $tweetId);
$tweets = TweetBo::indexValidations($tweets, $userId);
$tweetsByAccount = TweetBo::accounted($tweets);

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li><a href="validation.php"><?php echo lang("breadcrumb_validation"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_seeTweetValidation"); ?></li>
	</ol>

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
				</tr>
			</thead>
			<tbody>
				<?php 	foreach($tweets as $tweet) {?>
				<tr id="row_<?php echo $tweet["twe_id"]; ?>">
					<td class="vertical-middle"><?php echo $tweet["twe_content"]; ?>

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
				</tr>
				<?php 	}?>
			</tbody>
		</table>
	</div>

	<br />

	<?php 	}?>
</div>

<?php include("footer.php");?>
</body>
</html>