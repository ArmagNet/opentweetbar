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

session_start();
include_once("config/database.php");
include_once("language/language.php");
include_once("engine/utils/bootstrap_forms.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/TweetBo.php");

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);

$userId = SessionUtils::getUserId($_SESSION);
$accounts = $accountBo->getAdministratedAccounts($userId);

$account = null;
foreach($accounts as $currentAccount) {
	if ($currentAccount["sna_id"] == intval($_REQUEST["accountId"])) {
		$account = $currentAccount;
		break;
	}
}

if ($account == null) exit();

$friends = TweetBo::getTwitterFriends($account);

?>

<form class="form-horizontal">
	<fieldset>

		<input type="hidden" id="friendAccountIdInput" name="accountId" value="<?php echo $currentAccount["sna_id"]; ?>">

		<div class="form-group">
			<label class="col-md-5 control-label text-center">Abonnements</label>
			<label class="col-md-2 control-label"></label>
			<label class="col-md-5 control-label text-center">Ajouter des amis</label>
		</div>

		<!-- Textarea -->
		<div class="form-group">
			<div class="col-md-5">
				<select class="form-control" multiple="multiple" style="height: 234px;">
					<?php 	foreach($friends as $friend) {

						// $friend->statuses_count  // NB de tweets
						// $friend->friends_count   // NB de friends
						// $friend->followers_count // NB de follower

						?>
					<option value="<?php echo $friend->id; ?>">
					@<?php echo $friend->screen_name; ?> (<?php echo $friend->name; ?>)</option>
					<?php 	}?>
				</select>
			</div>
			<label class="col-md-2 control-label"></label>
			<div class="col-md-5 text-center">
				<textarea class="form-control" id="toAddFriendArea" style="height: 180px;"></textarea>
				<br>
				<button type="button" id="addFriendButton" class="btn btn-success"><?php echo lang("see_friends_add"); ?></button>
			</div>
		</div>

	</fieldset>
</form>

