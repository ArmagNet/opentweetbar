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

$administratedAccounts = $accountBo->getAdministratedAccounts($userId);

// $userBo = UserBo::newInstance(openConnection());
// $dbuser = $userBo->get(SessionUtils::getUserId($_SESSION));

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?> </a></li>
		<?php 	if ($user) {?>
		<li><a href="mypage.php"><?php echo $user; ?></a></li>
		<?php 	}?>
		<li class="active"><?php echo lang("breadcrumb_myrights"); ?></li>
	</ol>

	<div class="well well-sm">
		<p>
			<?php echo lang("myrights_guide"); ?>
		</p>
	</div>

	<?php 	if ($user) {?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo lang("myrights_scores_legend"); ?>
		</div>
		<?php 	if (!count($accounts)) {?>
		<div class="panel-body">
			<p>
				<?php echo lang("myrights_scores_no_score"); ?>
			</p>
		</div>
		<?php 	}?>

		<?php 	if (count($accounts)) {
			?>
		<ul class="list-group">
			<?php
					foreach($accounts as $account) {?>
			<li class="list-group-item"><?php echo $account["sna_name"] ?>
				<span class="badge">
					<span data-toggle="tooltip" data-placement="bottom"
						title="<?php echo lang("myrights_scores_my_score"); ?>"><?php echo $account["vgr_score"]; ?> </span>
				/ 	<span data-toggle="tooltip" data-placement="bottom"
						title="<?php echo lang("myrights_scores_validation_score"); ?>"><?php echo $account["sco_validation_score"]; ?> </span>
				</span>
			</li>
			<?php 	}
			?>
		</ul>
		<?php	}	?>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo lang("myrights_administration_legend"); ?>
		</div>
		<?php 	if (!count($administratedAccounts)) {?>
		<div class="panel-body">
			<p>
				<?php echo lang("myrights_scores_no_adminstation"); ?>
			</p>
		</div>
		<?php 	}?>

		<?php 	if (count($administratedAccounts)) {?>
		<ul class="list-group">
			<?php	foreach($administratedAccounts as $account) {?>
			<li class="list-group-item"><?php echo $account["sna_name"] ?>
				<span class="badge">
					<a class="color-inherit" href="myaccounts.php#<?php echo $account["sna_name"] ?>"><span class="glyphicon glyphicon-chevron-right"></span></a>
				</span>
			</li>
			<?php 	}?>
		</ul>
		<?php	}	?>
	</div>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<?php include("footer.php");?>
</body>
</html>
