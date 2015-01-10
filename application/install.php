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
session_start();
include_once("config/database.php");
include_once("config/mail.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");
include_once("language/language.php");

$language = SessionUtils::getLanguage($_SESSION);

$opentweetbarPath = str_replace("install.php", "", $_SERVER["SCRIPT_FILENAME"]);
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo lang("opentweetbar_title"); ?></title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<link href="css/opentweetbar.css" rel="stylesheet">
<link href="css/flags.css" rel="stylesheet">
<link rel="shortcut icon" type="image/png" href="favicon.png" />
</head>
<body>

	<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><img src="images/logo.svg" style="position: relative; top: -14px; width: 48px; height: 48px; background-color: #ffffff;"
					title="OpenTweetBar" /></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">

					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo str_replace("{language}", lang("language_$language"), lang("menu_language")); ?> <span
							class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="do_changeLanguage.php?lang=en"><span class="flag en" title="<?php echo lang("language_en"); ?>"></span> <?php echo lang("language_en"); ?></a></li>
							<li><a href="do_changeLanguage.php?lang=fr"><span class="flag fr" title="<?php echo lang("language_fr"); ?>"></span> <?php echo lang("language_fr"); ?></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container theme-showcase" role="main">

		<div class="well well-sm">
			<p><?php echo lang("install_guide");?></p>
		</div>

		<div id="configurationTabs" role="tabpanel">

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#license" aria-controls="license" role="tab" data-toggle="tab"><?php echo lang("install_tabs_license");?></a></li>
				<li role="presentation"><a href="#database" aria-controls="database" role="tab" data-toggle="tab"><?php echo lang("install_tabs_database");?></a></li>
				<li role="presentation"><a href="#mail" aria-controls="mail" role="tab" data-toggle="tab"><?php echo lang("install_tabs_mail");?></a></li>
				<li role="presentation"><a href="#application" aria-controls="application" role="tab" data-toggle="tab"><?php echo lang("install_tabs_application");?></a></li>
  				<li role="presentation"><a href="#final" aria-controls="final" role="tab" data-toggle="tab"><?php echo lang("install_tabs_final");?></a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="license">
					<?php include "licenses/license_".$language.".html"; ?>
				</div>
				<div role="tabpanel" class="tab-pane" id="database">

					<form class="form-horizontal">
						<fieldset>

							<!-- Form Name -->
							<legend>
								<?php echo lang("install_database_form_legend");?>
							</legend>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databaseHostInput"><?php echo lang("install_database_hostInput");?>Hôte</label>
								<div class="col-md-6">
									<input id="databaseHostInput" name="databaseHostInput" value="<?php echo @$config["database"]["host"];?>" type="text"
										placeholder="<?php echo lang("install_database_hostPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databasePortInput"><?php echo lang("install_database_portInput");?></label>
								<div class="col-md-6">
									<input id="databasePortInput" name="databasePortInput" value="<?php echo @$config["database"]["port"];?>" type="text"
										placeholder="<?php echo lang("install_database_portPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databaseLoginInput"><?php echo lang("install_database_loginInput");?></label>
								<div class="col-md-6">
									<input id="databaseLoginInput" name="databaseLoginInput" value="<?php echo @$config["database"]["login"];?>" type="text"
										placeholder="<?php echo lang("install_database_loginPlaceholder");?>" class="form-control input-md"> <span class="help-block"><?php echo lang("install_database_loginHelp");?></span>
								</div>
							</div>

							<!-- Password input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databasePasswordInput"><?php echo lang("install_database_passwordInput");?></label>
								<div class="col-md-6">
									<input id="databasePasswordInput" name="databasePasswordInput" value="<?php echo @$config["database"]["password"];?>" type="password"
										placeholder="<?php echo lang("install_database_passwordPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="databaseDatabaseInput"><?php echo lang("install_database_databaseInput");?></label>
								<div class="col-md-6">
									<input id="databaseDatabaseInput" name="databaseDatabaseInput" value="<?php echo @$config["database"]["database"];?>" type="text"
										placeholder="<?php echo lang("install_database_databasePlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="pingButton"><?php echo lang("install_database_operations");?></label>
								<div class="col-md-8">
									<button id="saveConfigButton" name="saveConfigButton" class="btn btn-default"><?php echo lang("install_database_saveButton");?></button>
									<button id="pingButton" name="pingButton" class="btn btn-primary"><?php echo lang("install_database_pingButton");?></button>
									<button id="createButton" name="createButton" class="btn btn-info"><?php echo lang("install_database_createButton");?></button>
									<button id="deployButton" name="deployButton" class="btn btn-success"><?php echo lang("install_database_deployButton");?></button>
								</div>
							</div>
						</fieldset>
					</form>

				</div>
				<div role="tabpanel" class="tab-pane" id="mail">

					<form class="form-horizontal">
						<fieldset>

							<!-- Form Name -->
							<legend>
								<?php echo lang("install_mail_form_legend");?>
							</legend>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailHostInput"><?php echo lang("install_mail_hostInput");?></label>
								<div class="col-md-6">
									<input id="mailHostInput" name="mailHostInput" value="<?php echo @$config["smtp"]["host"];?>" type="text"
										placeholder="<?php echo lang("install_mail_hostPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailPortInput"><?php echo lang("install_mail_portInput");?>Port</label>
								<div class="col-md-6">
									<input id="mailPortInput" name="mailPortInput" value="<?php echo @$config["smtp"]["port"];?>" type="text"
										placeholder="<?php echo lang("install_mail_portPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailUsernameInput"><?php echo lang("install_mail_usernameInput");?></label>
								<div class="col-md-6">
									<input id="mailUsernameInput" name="mailUsernameInput" value="<?php echo @$config["smtp"]["username"];?>" type="text"
										placeholder="<?php echo lang("install_mail_usernamePlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Password input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailPasswordInput"><?php echo lang("install_mail_passwordInput");?></label>
								<div class="col-md-6">
									<input id="mailPasswordInput" name="mailPasswordInput" value="<?php echo @$config["smtp"]["password"];?>" type="password"
										placeholder="<?php echo lang("install_mail_passwordPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailFromAddressInput"><?php echo lang("install_mail_fromMailInput");?></label>
								<div class="col-md-6">
									<input id="mailFromAddressInput" name="mailFromAddressInput" value="<?php echo @$config["smtp"]["from.address"];?>" type="email"
										placeholder="<?php echo lang("install_mail_fromMailPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailFromNameInput"><?php echo lang("install_mail_fromNameInput");?></label>
								<div class="col-md-6">
									<input id="mailFromNameInput" name="mailFromNameInput" value="<?php echo @$config["smtp"]["from.name"];?>" type="text"
										placeholder="<?php echo lang("install_mail_fromNamePlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<hr />

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="mailTestAddressInput"><?php echo lang("install_mail_testMailInput");?></label>
								<div class="col-md-6">
									<input id="mailTestAddressInput" name="mailTestAddressInput" value="" type="email"
										placeholder="<?php echo lang("install_mail_testMailPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="pingButton"><?php echo lang("install_application_operation");?></label>
								<div class="col-md-8">
									<button id="saveMailConfigButton" name="saveMailConfigButton" class="btn btn-default"><?php echo lang("install_mail_saveButton");?></button>
									<button id="pingMailButton" name="pingMailButton" class="btn btn-primary"><?php echo lang("install_mail_pingButton");?></button>
								</div>
							</div>
						</fieldset>
					</form>

				</div>
				<div role="tabpanel" class="tab-pane" id="application">

					<form class="form-horizontal">
						<fieldset>

							<!-- Form Name -->
							<legend>
								<?php echo lang("install_application_form_legend");?>
							</legend>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="baseUrlInput"><?php echo lang("install_application_baseUrlInput");?></label>
								<div class="col-md-6">
									<input id="baseUrlInput" name="baseUrlInput" value="<?php echo @$config["base_url"];?>" type="text"
										placeholder="" class="form-control input-md">
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-4 control-label">
									<input id="cronEnabledInput" name="cronEnabledInput" value="1" type="checkbox"
										placeholder="" class="input-md" <?php if ($config["cron_enabled"]) { echo 'checked="checked"'; } ?>>
								</div>
								<div class="col-md-6 padding-left-0">
									<label class="form-control labelForCheckbox" for="cronEnabledInput"><?php echo lang("install_application_cronEnabledInput"); ?> </label>
									<span id="cronEnabledHelp" class="help-block" style="padding-left : 15px; <?php if (!@$config["cron_enabled"]) { echo 'display: none;'; } ?>"><?php echo str_replace("{path}", $opentweetbarPath, lang("install_application_cronEnabledHelp")); ?></span>
								</div>
							</div>

							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="saltInput"><?php echo lang("install_application_saltInput");?></label>
								<div class="col-md-6">
									<input id="saltInput" name="saltInput" value="<?php echo @$config["salt"];?>" type="text"
										placeholder="<?php echo lang("install_application_saltPlaceholder");?>" class="form-control input-md">
								</div>
							</div>

							<!-- Language input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="languageInput"><?php echo lang("install_application_defaultLanguageInput");?></label>
								<div class="col-md-8">
									<input id="languageInput" name="languageInput"
										value="<?php echo $language; ?>" type="hidden">
									<div id="languageButtons" class="btn-group" role="group" aria-label="...">
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
								<label class="col-md-4 control-label" for="saveApplicationConfigButton"><?php echo lang("install_application_operation");?></label>
								<div class="col-md-8">
									<button id="saveApplicationConfigButton" name="saveApplicationConfigButton" class="btn btn-default"><?php echo lang("install_application_saveButton");?></button>
								</div>
							</div>
						</fieldset>
					</form>

				</div>
				<div role="tabpanel" class="tab-pane" id="final">
					<br />
 					<div class="well well-sm">
						<p><?php echo lang("install_autodestruct_guide");?></p>
					</div>

					<form class="form-horizontal">
						<fieldset>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="autodestructButton"></label>
								<div class="col-md-8">
									<button id="autodestructButton" name="autodestructButton" class="btn btn-danger"><?php echo lang("install_autodestruct");?></button>
								</div>
							</div>
						</fieldset>
					</form>

 				</div>
			</div>
		</div>
	</div>

	<?php echo addAlertDialog("error_cant_delete_filesAlert", lang("error_cant_delete_files"), "danger"); ?>
	<?php echo addAlertDialog("error_cant_send_mailAlert", lang("error_cant_send_mail"), "danger"); ?>
	<?php echo addAlertDialog("error_cant_connectAlert", lang("error_cant_connect"), "danger"); ?>
	<?php echo addAlertDialog("error_database_already_existsAlert", lang("error_database_already_exists"), "warning"); ?>
	<?php echo addAlertDialog("error_database_dont_existAlert", lang("error_database_dont_exist"), "warning"); ?>
	<?php echo addAlertDialog("ok_operation_successAlert", lang("ok_operation_success"), "success"); ?>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
	<script>

	function responseHandler(data) {
		if (data.ok) {
			$("#ok_operation_successAlert").show().delay(2000).fadeOut(1000);
		}
		else {
			$("#" + data.message + "Alert").show().delay(2000).fadeOut(1000);
		}
	}

	function getApplicationForm() {
		var myform = {
				baseUrl: $("#baseUrlInput").val(),
				salt: $("#saltInput").val(),
				defaultLanguage: $("#languageInput").val(),
				cronEnabled: $("#cronEnabledInput").attr("checked") ? 1 : 0
			};

		return myform;
	}

	function getDatabaseForm() {
		var myform = {
				host: $("#databaseHostInput").val(),
				port: $("#databasePortInput").val(),
				login: $("#databaseLoginInput").val(),
				password: $("#databasePasswordInput").val(),
				database: $("#databaseDatabaseInput").val()
			};

		return myform;
	}

	function getMailForm() {
		var myform = {
				host: $("#mailHostInput").val(),
				port: $("#mailPortInput").val(),
				username: $("#mailUsernameInput").val(),
				password: $("#mailPasswordInput").val(),
				fromAddress: $("#mailFromAddressInput").val(),
				fromName: $("#mailFromNameInput").val(),
				testAddress: $("#mailTestAddressInput").val()
			};

		return myform;
	}

	$(function() {
		$("input[type='checkbox']").click(function(event) {
			if ($(this).attr("checked")) {
				$(this).removeAttr("checked");
				if ($(this).attr("id") == "cronEnabledInput") $("#cronEnabledHelp").hide();
			}
			else {
				$(this).attr("checked", "checked");
				if ($(this).attr("id") == "cronEnabledInput") $("#cronEnabledHelp").show();
			}
		});

		$('#configurationTabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		$('#pingButton').click(function (e) {
			e.preventDefault();
			var myform = getDatabaseForm();
			myform.action = "ping";

			$.post("do_install_database.php", myform, responseHandler, "json");
		});
		$('#createButton').click(function (e) {
			e.preventDefault();
			var myform = getDatabaseForm();
			myform.action = "create";

			$.post("do_install_database.php", myform, responseHandler, "json");
		});
		$('#deployButton').click(function (e) {
			e.preventDefault();
			var myform = getDatabaseForm();
			myform.action = "deploy";

			$.post("do_install_database.php", myform, responseHandler, "json");
		});
		$('#saveConfigButton').click(function (e) {
			e.preventDefault();
			var myform = getDatabaseForm();
			myform.action = "save";

			$.post("do_install_database.php", myform, responseHandler, "json");
		});
		$('#pingMailButton').click(function (e) {
			e.preventDefault();
			var myform = getMailForm();
			myform.action = "mail";

			$.post("do_install_mail.php", myform, responseHandler, "json");
		});
		$('#saveMailConfigButton').click(function (e) {
			e.preventDefault();
			var myform = getMailForm();
			myform.action = "save";

			$.post("do_install_mail.php", myform, responseHandler, "json");
		});
		$('#saveApplicationConfigButton').click(function (e) {
			e.preventDefault();
			var myform = getApplicationForm();
			myform.action = "save";

			$.post("do_install_application.php", myform, responseHandler, "json");
		});

		$('#autodestructButton').click(function (e) {
			e.preventDefault();
			$.post("do_install_destruct.php", {}, function(data) {
				if (data.ok) {
					window.location.assign("index.php");
				}
				else {
					$("#" + data.message + "Alert").show().delay(2000).fadeOut(1000);
				}
			}, "json");
		});

		$("#languageButtons button").click(function(e) {
			$("#languageButtons button").removeClass("active");
			$(this).addClass("active");
			$("#languageInput").val($(this).val());
		});
	});
	</script>
</body>
</html>
