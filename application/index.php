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
require_once("engine/utils/UploadUtils.php");

$account = "";
if (isset($_REQUEST["c"])) {
	$account = $_REQUEST["c"];
}

// TODO vérifier la validité du compte (existence, droits)

$password = "";

if (!$account /*&& count($config["accounts"]) == 1*/) {
	foreach($accounts as $gaccount) {
		$account = $gaccount["sna_name"];
		break;
	}
}

if (!$account /*&& count($config["accounts"]) == 1*/) {
	foreach($anonymousAccounts as $gaccount) {
		$account = $gaccount["sna_name"];
		$password = $gaccount["sco_anonymous_password"];
		break;
	}
}
else {
	foreach($anonymousAccounts as $gaccount) {
		if ($gaccount["sna_name"] == $account) {
			$password = $gaccount["sco_anonymous_password"];
			break;
		}
	}
}

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li class="active"><?php echo lang("breadcrumb_index"); ?></li>
	</ol>
	<div class="well well-sm">
		<p><?php echo lang("index_guide"); ?></p>
	</div>

	<?php 	if (SessionUtils::getUser($_SESSION) || count($anonymousAccounts) > 0) {?>

	<?php 	if (!SessionUtils::getUser($_SESSION) || $password) {?>

	<form id="formPanel" class="form-horizontal" style="width: 50%; min-width: 450px;">
		<fieldset>

			<legend><?php echo lang("anonymous_form_legend"); ?></legend>

			<input id="mail" name="mail" value="" type="text" class="mailForm" />

			<?php 	if (!SessionUtils::getUser($_SESSION)) { ?>
			<!-- Text input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="nicknameInput"><?php echo lang("anonymous_form_nicknameInput"); ?></label>
				<div class="col-md-8">
					<input id="nicknameInput" name="nicknameInput" value="" type="text"
						placeholder="" class="form-control input-md">
					<span id="nicknameStatus"
						class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
					<p id="nicknameHelp" class="help-block otbHidden"></p>
				</div>
			</div>

			<!-- Email input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="xxxInput"><?php echo lang("anonymous_form_mailInput"); ?></label>
				<div class="col-md-8">
					<input id="xxxInput" name="xxxInput" value="" type="email"
						placeholder="" class="form-control input-md">
					<span id="mailStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
					<p id="mailHelp" class="help-block otbHidden"></p>
				</div>
 			</div>
 			<?php }?>

 			<?php 	if ($password) {?>
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="apasswordInput"><?php echo lang("anonymous_form_passwordInput"); ?></label>
				<div class="col-md-8">
					<input id="apasswordInput" name="apasswordInput" value="" type="password"
						placeholder="" class="form-control input-md">
					<span id="apasswordStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
					<p id="apasswordHelp" class="help-block otbHidden"></p>
				</div>
 			</div>
 			<?php 	}?>

 			<?php 	if (!SessionUtils::getUser($_SESSION)) { ?>

			<!-- Checkbox input-->
			<div class="form-group">
				<div class="col-md-4 control-label">
					<input id="cgvInput" name="cgvInput" value="cgv" type="checkbox"
						placeholder="" class="input-md" checked="checked">
				</div>
				<div class="col-md-8 padding-left-0">
					<label class="form-control labelForCheckbox" for="cgvInput"><?php echo lang("anonymous_form_iamabot"); ?> </label>
				</div>
			</div>

			<?php 	}?>

 		</fieldset>
 	</form>

	<?php 	}?>

	<input type="hidden" id="account" name="account" value="<?php echo $account; ?>" />

	<div class="input-group">
		<div class="input-group-btn">
			<button id="accountButton" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span id="text"><?php echo $account ? $account : lang("index_accounts"); ?></span>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<?php	foreach($accounts as $gaccount) {?>
				<li><a class="changeAccountLink" href="?c=<?php echo $gaccount["sna_name"]; ?>"><?php echo $gaccount["sna_name"]; ?></a></li>
				<?php 	}?>

				<?php 	if (count($accounts) && count($anonymousAccounts)) {?>
				<li class="divider"></li>
				<?php 	}?>

				<?php	foreach($anonymousAccounts as $gaccount) {?>
				<li><a class="changeAccountLink" href="?c=<?php echo $gaccount["sna_name"]; ?>"><?php echo $gaccount["sna_name"]; ?></a></li>
				<?php 	}?>
			</ul>
		</div>
		<input type="text" id="tweet" name="tweet" class="form-control" placeholder="<?php echo lang("index_tweetPlaceholder"); ?>" />
		<textarea type="text" id="tweet-big" name="tweet-big"
			class="form-control otbHidden" style="height: 100px;"></textarea>
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" id="tweetButton"><?php echo lang("index_tweetButton"); ?></button>
		</span>
	</div>
	<div id="supportDiv" class="input-group text-center" style="width: 100%; padding-top: 5px;">
		<label id="tweetLabel" style="font-weight: normal;"><input type="checkbox" name="supports" value="twitter" /> <?php echo lang("index_supports_tweet"); ?>
			<span class="social grey twitter" style="height: 30px;"></span></label>
		&nbsp;
		<label id="facebookLabel" style="font-weight: normal;"><input type="checkbox" name="supports" value="facebookPage" /> <?php echo lang("index_supports_facebook"); ?>
			<span class="social grey facebook" style="height: 30px;"></span></label>
	</div>
	<div class="text-right">
		<span class="tweeter-count"></span>
	</div>

	<fieldset id="cutTweets" style="display: none;">
		<legend><?php echo lang("index_cutTweets_legend"); ?></legend>
		<ul class="list-group"></ul>
	</fieldset>

	<div class="panel-group" id="accordion">
		<div class="panel panel-default" id="panel1">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-target="#options" href="#options" class="collapsed"> Options </a>
				</h4>

			</div>
			<div id="options" class="panel-collapse collapse">
				<div class="panel-body">

					<form class="form-horizontal" id="optionForm">
						<input type="hidden" id="account2" name="account" value="<?php echo $account; ?>" />
						<fieldset>

<?php if (count($accounts)) {?>
							<div class="form-group has-feedback">
								<label class="col-md-4 control-label" for="nicknameInput"><?php echo lang("index_options_secondaryAccounts"); ?></label>
								<div class="col-md-6 padding-left-0">

<?php	foreach($accounts as $gaccount) {?>
			<label class="control-label"><input type="checkbox" value="<?php echo $gaccount["sna_name"]; ?>" class="secondaryAccounts" /><?php echo $gaccount["sna_name"]; ?></label>
<?php 	}?>
								</div>
							</div>
<?php }?>

							<div class="form-group has-feedback">
								<label class="col-md-4 control-label" for="mediaInput"><?php echo lang("index_options_mediaInput"); ?></label>
								<div class="col-md-6 padding-left-0">

									<div class="progress otbHidden" id="mediaProgress">
										<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
									    	<span class="sr-only"></span>
									  	</div>
									</div>

									<input id="mediaIds" name="mediaIds" value="-1" type="hidden" />
									<input id="mediaInput" name="mediaInput" value="" type="file"
										data-max-size="<?php echo file_upload_max_size(); ?>"
										data-authorized-types="image/jpeg,image/png,image/gif"
										placeholder="" class="form-control input-md">
									<span id="mediaStatus"
										class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
									<p id="mediaHelp" class="help-block otbHidden"></p>
								</div>
							</div>
							<?php echo addAlertDialog("error_media_typeErrorAlert", lang("error_media_typeError"), "danger"); ?>
							<?php echo addAlertDialog("error_media_sizeErrorAlert", lang("error_media_sizeError"), "danger"); ?>
							<?php echo addAlertDialog("error_media_defaultErrorAlert", lang("error_media_defaultError"), "danger"); ?>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="h2Button"><?php echo lang("index_options_validationDurationInput");?></label>
								<div class="col-md-8 padding-left-0">
									<div id="validationDurationButtons" class="btn-group" role="group" aria-label="...">
										<button value="5" type="button" class="btn btn-default">5mn</button>
										<button value="10" type="button" class="btn btn-default">10mn</button>
										<button value="15" type="button" class="btn btn-default">15mn</button>
										<button value="30" type="button" class="btn btn-default">30mn</button>
										<button value="60" type="button" class="btn btn-default">1h</button>
										<button value="120" type="button" class="btn btn-default">2h</button>
										<button value="360" type="button" class="btn btn-default">6h</button>
										<button value="720" type="button" class="btn btn-default">12h</button>
										<button value="1440" type="button" class="btn btn-default">24h</button>
										<button value="0" type="button" class="btn btn-default active">&#8734;</button>
									</div>
									<input id="validationDurationInput" name="validationDurationInput"
										value="0" type="hidden">
								</div>
							</div>

<?php 	if (isset($config["cron_enabled"]) && $config["cron_enabled"]) {?>
							<!-- Datetimepicker input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databaseHostInput"><?php echo lang("index_options_cronDateInput");?></label>
								<div class="col-md-3">
						            <div class="form-group">
						                <div class='input-group date'>
						                    <input id='cronDateInput' type='text' class="form-control" placeholder="<?php echo lang("index_options_cronDatePlaceholder");?>"
						                    	data-date-format="YYYY-MM-DD HH:mm"/>
						                    <span class="input-group-addon"><span
						                    	class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
					                    <p class="help-block"><?php echo lang("index_options_cronDateGuide");?></p>
					                </div>
						        </div>
							</div>

						</fieldset>
					</form>

				</div>
			</div>
		</div>
	</div>
	<?php }?>

	<br>

	<?php echo addAlertDialog("okTweetAlert", lang("okTweet"), "success"); ?>
	<?php echo addAlertDialog("koTweetAlert", lang("koTweet"), "warning"); ?>

	<?php 	}
			else {
				include("connectButton.php");
			}?>

</div>

<script type="text/javascript">
var accounts = {};

<?php	foreach($accounts as $gaccount) {?>
	accounts["<?php echo $gaccount["sna_name"]; ?>"] = {
			label : "<?php echo $gaccount["sna_name"]; ?>",
			hasTwitter : <?php echo $gaccount["stc_access_token"] ? "true" : "false"; ?>,
			hasFacebookPage : <?php echo $gaccount["sfp_access_token"] ? "true" : "false"; ?>};
<?php 	}?>

<?php	foreach($anonymousAccounts as $gaccount) {?>
	accounts["<?php echo $gaccount["sna_name"]; ?>"] = {
			label : "<?php echo $gaccount["sna_name"]; ?>",
			hasTwitter : <?php echo $gaccount["stc_access_token"] ? "true" : "false"; ?>,
			hasFacebookPage : <?php echo $gaccount["sfp_access_token"] ? "true" : "false"; ?>};
<?php 	}?>

</script>
<script type="text/javascript">
var userLanguage = '<?php echo SessionUtils::getLanguage($_SESSION); ?>';
var register_validation_user_empty = "<?php echo lang("register_validation_user_empty"); ?>";
var register_validation_password_empty = "<?php echo lang("register_validation_password_empty"); ?>";
var register_validation_mail_empty = "<?php echo lang("register_validation_mail_empty"); ?>";
var register_validation_mail_not_valid = "<?php echo lang("register_validation_mail_not_valid"); ?>";
</script>
<?php include("footer.php");?>
</body>
</html>