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

// foreach($accounts as $key => $account) {
// 	$currentAccount = $accountBo->getAccount($account["sna_id"]);

// //	print_r($currentAccount);
// //	echo "<br />";

// 	$accounts[$key]["timeline"] = array();

// 	if ($currentAccount["stc_api_key"]) {
// 		$timeline = TweetBo::getTimeline($currentAccount, null, 50);

// 		$accounts[$key]["timeline"] = $timeline;

// //		echo "<pre>";
// //		print_r($timeline);
// //		echo "</pre>";
// //		echo "<br />";
// 	}
// }

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<li class="active"><?php echo lang("breadcrumb_timelines"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("timelines_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<div class="col-md-12">
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<?php echo lang("timelines_search_header"); ?>
			</div>
			<div class="panel-body">

				<form id="searchTweetForm" class="form-horizontal">
					<fieldset>

						<div class="form-group">
							<label class="col-md-4 control-label" for="searchInput"><?php echo lang("timelines_search_label"); ?></label>
							<div class="col-md-6">
								<div class="input-group">
									<input id="searchInput" name="searchInput" class="form-control"
										placeholder="<?php echo str_replace("\"", "&quot;", lang("timelines_search_placeholder")); ?>" type="text">
									<span id="searchButton" type="button" class="btn btn-default input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>
						</div>

					</fieldset>
				</form>

				<div id="found-tweet-div">
				</div>
			</div>
		</div>
	</div>

	<?php 	foreach ($accounts as $account) {?>
	<div class="col-md-4 account-panel" id="account-panel-<?php echo $account["sna_id"];?>" data-account-id="<?php echo $account["sna_id"];?>">
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<?php echo str_replace("{account}", $account["sna_name"], lang("timelines_account_title")); ?>
			</div>
			<div class="panel-body" style="overflow-y: scroll; max-height: 500px;">

			<?php 	if (false) {
					foreach	($account["timeline"] as $line) {
						$date = new DateTime($line->created_at);
						$date = $date->add(new DateInterval('PT2H'));
						$dateString = str_replace("{date}", $date->format(lang("date_format")), str_replace("{time}", $date->format(lang("time_format")), lang("datetime_format")));

				?>
					<blockquote>
						<?php echo $line->user->name; ?> -
						@<?php echo $line->user->screen_name; ?> -
						<a href="https://twitter.com/<?php echo $line->user->screen_name; ?>/status/<?php echo $line->id; ?>"><?php echo $dateString; ?></a>
						<p>
							<?php echo str_replace("\n", "<br>", $line->text); ?>
						</p>
					</blockquote>
			<?php 	}}?>
			</div>
		</div>
	</div>

	<?php 	}?>

	<templates>
		<blockquote aria-template-id="template-tweet" class="template">
			<a href="https://twitter.com/${source_user_screen_name}">${source_user_name}
				<small>@${source_user_screen_name}</small>
			</a>
			<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}">
				<small>${source_created_at}</small>
			</a>
			<p>${source_text}</p>
		</blockquote>
		<blockquote aria-template-id="template-retweet" class="template">
			<a href="https://twitter.com/${source_user_screen_name}">${source_user_name}
				<small>@${source_user_screen_name}</small>
			</a>
			<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}">
				<small>${source_created_at}</small>
			</a>
			<p>${source_text}</p>
			<small>
				RT by <a href="https://twitter.com/${tweet_user_screen_name">${tweet_user_name}
					@${tweet_user_screen_name}
				</a>
			</small>
		</blockquote>
		<div aria-template-id="template-waiting" class="template text-center wait"><span class="glyphicon glyphicon-refresh spin"></span></div>
		<blockquote aria-template-id="template-waiting-tweets" class="template number-of-tweets">
			<p class="text-center">Voir ${numberOfTweets} nouveaux Tweets</p>
		</blockquote>
		<blockquote aria-template-id="template-one-waiting-tweet" class="template number-of-tweets">
			<p class="text-center">Voir 1 nouveau Tweet</p>
		</blockquote>
	</templates>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>

<?php include("footer.php");?>
</body>
</html>