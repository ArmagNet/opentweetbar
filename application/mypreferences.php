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
require_once("engine/utils/SessionUtils.php");

$userBo = UserBo::newInstance(openConnection());
$dbuser = $userBo->get(SessionUtils::getUserId($_SESSION));

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?></a></li>
		<?php 	if ($user) {?>
		<li><a href="mypage.php"><?php echo $user; ?></a></li>
		<?php 	}?>
		<li class="active"><?php echo lang("breadcrumb_mypreferences"); ?></li>
	</ol>

	<div class="well well-sm">
		<p><?php echo lang("mypreferences_guide"); ?></p>
	</div>

	<?php 	if ($user) {?>

	<form class="form-horizontal">
		<fieldset>

			<!-- Form Name -->
			<legend><?php echo lang("mypreferences_form_legend"); ?></legend>

			<!-- Email input-->
			<div class="form-group has-feedback">
				<label class="col-md-4 control-label" for="xxxInput"><?php echo lang("mypreferences_form_mailInput"); ?></label>
				<div class="col-md-6">
					<input id="xxxInput" name="xxxInput" value="<?php echo @$dbuser["use_mail"]; ?>" type="email"
						placeholder="" class="form-control input-md">
					<span id="mailStatus" class="glyphicon glyphicon-ok form-control-feedback otbHidden" aria-hidden="true"></span>
					<p id="mailHelp" class="help-block otbHidden"></p>
				</div>
 			</div>

			<!-- Password input-->
			<div class="form-group">
				<label class="col-md-4 control-label" for="userPasswordInput"><?php echo lang("mypreferences_form_passwordInput"); ?></label>
				<div class="col-md-6">
					<input id="userPasswordInput" name="userPasswordInput" value="" type="password"
						placeholder="<?php echo lang("mypreferences_form_passwordPlaceholder"); ?>" class="form-control input-md">
					<input id="userOldPasswordInput" name="userOldPasswordInput"
						value="<?php echo @$dbuser["use_password"];?>" type="hidden">
				</div>
			</div>

			<!-- Notification input-->
			<div class="form-group">
				<label class="col-md-4 control-label" for="userNotificationInput"><?php echo lang("mypreferences_form_notificationInput"); ?></label>
				<div class="col-md-8">
					<input id="userNotificationInput" name="userNotificationInput"
						value="<?php echo @$dbuser["use_notification"]; ?>" type="hidden">
					<div id="userNotificationButtons" class="btn-group" role="group" aria-label="...">
						<button value="none" type="button" class="btn btn-default <?php if (@$dbuser["use_notification"] == "none") { echo "active"; } ?>"><span class=""><?php echo lang("mypreferences_form_notification_none"); ?></span></button>
						<button value="mail" type="button" class="btn btn-default <?php if (@$dbuser["use_notification"] == "mail") { echo "active"; } ?>"><span class=""><?php echo lang("mypreferences_form_notification_mail"); ?></span></button>
						<button value="simpledm" type="button" class="btn btn-default <?php if (@$dbuser["use_notification"] == "simpledm") { echo "active"; } ?>"><span class=""><?php echo lang("mypreferences_form_notification_simpledm"); ?></span></button>
						<button value="dm" type="button" class="btn btn-default <?php if (@$dbuser["use_notification"] == "dm") { echo "active"; } ?>"><span class=""><?php echo lang("mypreferences_form_notification_dm"); ?></span></button>
					</div>
				</div>
			</div>

			<!-- Language input-->
			<div class="form-group">
				<label class="col-md-4 control-label" for="userLanguageInput"><?php echo lang("mypreferences_form_languageInput"); ?></label>
				<div class="col-md-8">
					<input id="userLanguageInput" name="userLanguageInput"
						value="<?php echo $language; ?>" type="hidden">
					<div id="userLanguageButtons" class="btn-group" role="group" aria-label="...">
						<button value="en" type="button" class="btn btn-default <?php if ($language == "en") { echo "active"; } ?>"><span class="flag en" title="<?php echo lang("language_en"); ?>"></span></button>
						<button value="fr" type="button" class="btn btn-default <?php if ($language == "fr") { echo "active"; } ?>"><span class="flag fr" title="<?php echo lang("language_fr"); ?>"></span></button>
<!--
						<button value="de" type="button" class="btn btn-default <?php if ($language == "de") { echo "active"; } ?>"><span class="flag de" title="<?php echo lang("language_de"); ?>"></span></button>
-->
					</div>
				</div>
			</div>

			<!-- Button (Double) -->
			<div class="form-group">
				<label class="col-md-4 control-label" for="savePreferencesButton"></label>
				<div class="col-md-8">
					<button id="savePreferencesButton" name="savePreferencesButton" class="btn btn-default"><?php echo lang("mypreferences_save"); ?></button>
				</div>
			</div>
		</fieldset>
	</form>

	<?php echo addAlertDialog("error_cant_change_passwordAlert", lang("error_cant_change_password"), "danger"); ?>
	<?php echo addAlertDialog("ok_operation_successAlert", lang("ok_operation_success"), "success"); ?>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<script type="text/javascript">
var userLanguage = '<?php echo SessionUtils::getLanguage($_SESSION); ?>';
var mypreferences_validation_mail_already_taken = "<?php echo lang("mypreferences_validation_mail_already_taken"); ?>";
var mypreferences_validation_mail_not_valid = "<?php echo lang("mypreferences_validation_mail_not_valid"); ?>";
var mypreferences_validation_mail_empty = "<?php echo lang("mypreferences_validation_mail_empty"); ?>";
</script>
<?php include("footer.php");?>
</body>
</html>