<?php /*
	Copyright 2014-2018 Cédric Levieux, Jérémy Collot, ArmagNet

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

$accountIdLabels = array();
foreach ($accounts as $account) {
	$accountIdLabels[$account["sna_id"]] = $account["sna_name"];
}
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

	<?php 	foreach ($accounts as $account) {
		if ($account["vgr_show_timeline"] != "1") continue;
	?>
	<div class="col-md-4 account-panel" class="account-panel" id="account-panel-<?php echo $account["sna_id"];?>" data-account-id="<?php echo $account["sna_id"];?>">
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading" style="background-image: url('do_getBanner.php?accountId=<?php echo $account["sna_id"]; ?>'); ">
				<span><?php echo str_replace("{account}", $account["sna_name"], lang("timelines_account_title")); ?></span>
			</div>
			<div class="panel-body" style="overflow-y: scroll; max-height: 500px;">
			</div>
		</div>
	</div>

	<?php 	}?>

	<templates>
		<blockquote data-template-id="template-tweet" class="template" data-tweet-id="${source_id_str}">
			<a href="https://twitter.com/${source_user_screen_name}" target="_blank">${source_user_name}
				<small>@${source_user_screen_name}</small>
			</a>
			<a href="https://twitter.com/${source_user_screen_name}/status/${source_id_str}" target="_blank">
				<small>${source_created_at}</small>
			</a>
			<p>${source_text}</p>
			<div class="tweet-bar text-center"><a href="#" class="retweet-button"><span class="glyphicon glyphicon-retweet"></span></a></div>
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
			<div class="tweet-bar text-center"><a href="#" class="retweet-button"><span class="glyphicon glyphicon-retweet"></span></a></div>
		</blockquote>
		<div data-template-id="template-waiting" class="template text-center wait"><span class="glyphicon glyphicon-refresh spin"></span></div>
		<blockquote data-template-id="template-waiting-tweets" class="template number-of-tweets">
			<p class="text-center"><?php echo lang("timelines_waiting_tweets"); ?></p>
		</blockquote>
		<blockquote data-template-id="template-one-waiting-tweet" class="template number-of-tweets">
			<p class="text-center"><?php echo lang("timelines_waiting_tweet"); ?></p>
		</blockquote>
	</templates>

	<script>
		var accountIdLabels = <?php echo json_encode($accountIdLabels); ?>;
	</script>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>


<?php include("footer.php");?>
</body>
</html>
