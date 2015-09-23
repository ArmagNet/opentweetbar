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
include_once("language/language.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/TweetBo.php");
require_once("engine/bo/UserBo.php");

$page = $_SERVER["SCRIPT_NAME"];
if (strrpos($page, "/") !== false) {
	$page = substr($page, strrpos($page, "/") + 1);
}
$page = str_replace(".php", "", $page);

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$tweetBo = TweetBo::newInstance($connection);
$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$language = SessionUtils::getLanguage($_SESSION);

if (!SessionUtils::getUserId($_SESSION)) {
	$userBo = UserBo::newInstance($connection);
	$userBo->autologin($_COOKIE, $_SESSION);
}

$accounts = $accountBo->getAccessibleAccounts($userId);
$anonymousAccounts = $accountBo->getAnonymouslyAccessibleAccounts($accounts);
$tweets = array();

if (count($accounts)) {
	$tweets = $tweetBo->getTweets($accounts);
	$tweets = TweetBo::indexValidations($tweets, $userId);
}

?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo lang("opentweetbar_title"); ?></title>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.1.min.js"></script>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
<link href="css/ekko-lightbox.min.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<link href="css/jquery.template.css" rel="stylesheet" />
<link href="css/opentweetbar.css" rel="stylesheet" />
<link href="css/flags.css" rel="stylesheet" />
<link href="css/social.css" rel="stylesheet" />
<!--link href="css/fileinput.min.css" rel="stylesheet" /-->

<link rel="shortcut icon" type="image/png" href="favicon.png" />
</head>
<body>
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#otb-navbar-collapse">
					<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php"><img src="images/logo.svg" style="position: relative; top: -14px; width: 48px; height: 48px; background-color: #ffffff;"
					data-toggle="tooltip" data-placement="bottom"
					title="OpenTweetBar" /> </a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="otb-navbar-collapse">
				<ul class="nav navbar-nav">
					<li <?php if ($page == "timelines") echo 'class="active"'; ?>><a href="timelines.php"><?php echo lang("menu_timelines"); ?><?php if ($page == "timelines") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<li <?php if ($page == "index") echo 'class="active"'; ?>><a href="index.php"><?php echo lang("menu_tweet"); ?><?php if ($page == "index") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
					<li id="validationMenuItem" <?php if ($page == "validation") echo 'class="active"'; ?>><a href="validation.php"><?php echo lang("menu_validation"); ?><?php if ($page == "validation") echo ' <span class="sr-only">(current)</span>'; ?>
							<span class="badge <?php if (!count($tweets)) { echo "otbHidden"; } ?>"><?php echo count($tweets); ?> </span>
					</a></li>
					<li <?php if ($page == "history") echo 'class="active"'; ?>><a href="history.php"><?php echo lang("menu_history"); ?><?php if ($page == "history") echo ' <span class="sr-only">(current)</span>'; ?></a></li>
				</ul>
				<!--
				<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Search">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
				-->
				<ul class="nav navbar-nav navbar-right">

					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo str_replace("{language}", lang("language_$language"), lang("menu_language")); ?> <span
							class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="do_changeLanguage.php?lang=en"><span class="flag en" title="<?php echo lang("language_en"); ?>"></span> <?php echo lang("language_en"); ?></a></li>
							<li><a href="do_changeLanguage.php?lang=fr"><span class="flag fr" title="<?php echo lang("language_fr"); ?>"></span> <?php echo lang("language_fr"); ?></a></li>
						</ul>
					</li>

					<?php 	if ($user) {?>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $user; ?> <span
							class="caret"></span> </a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="myrights.php"><?php echo lang("menu_myrights"); ?></a></li>
							<li><a href="myaccounts.php"><?php echo lang("menu_myaccounts"); ?></a></li>
							<li><a href="mypreferences.php"><?php echo lang("menu_mypreferences"); ?></a></li>
							<li class="divider"></li>
							<li><a class="logoutLink" href="do_logout.php"><?php echo lang("menu_logout"); ?></a></li>
						</ul>
					</li>
					<li><a class="logoutLink" href="do_logout.php"><span class="glyphicon glyphicon-log-out"></span><span class="sr-only">Logout</span> </a></li>
					<?php 	} else { ?>
					<li><a id="loginLink" href="do_login.php"><span class="glyphicon glyphicon-log-in"></span><span class="sr-only">Login</span> </a></li>
					<?php 	}?>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container otbHidden" id="loginForm">
		<form class="form-signin" role="form">
			<h2 class="form-signin-heading text-center"><?php echo lang("login_title"); ?></h2>
			<label for="inputLogin" class="sr-only"><?php echo lang("login_loginInput"); ?></label> <input type="text" id="loginInput" class="form-control" placeholder="<?php echo lang("login_loginInput"); ?>" required
				autofocus> <label for="inputPassword" class="sr-only"><?php echo lang("login_passwordInput"); ?></label> <input type="password" id="passwordInput" class="form-control"
				placeholder="<?php echo lang("login_passwordInput"); ?>" required>

			<input type="checkbox" name="rememberMe" id="rememberMe" value="1">
			<label for="rememberMe"><?php echo lang("login_rememberMe"); ?></label>

			<br />
			<button id="loginButton" class="btn btn-lg btn-primary btn-block" type="submit">
				<?php echo lang("login_button"); ?> <span class="glyphicon glyphicon-log-in"></span>
			</button>
			<p class="text-center"><a href="register.php" class="colorInherit"><?php echo lang("register_link"); ?></a></p>
			<p class="text-center"><a href="forgotten.php" class="colorInherit"><?php echo lang("forgotten_link"); ?></a></p>
		</form>
	</div>

	<div class="container otbHidden">
		<?php echo addAlertDialog("error_login_banAlert", lang("error_login_ban"), "danger"); ?>
		<?php echo addAlertDialog("error_login_badAlert", lang("error_login_bad"), "warning"); ?>
	</div>
