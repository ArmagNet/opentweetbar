<?php /*
	Copyright 2014-2017 Cédric Levieux, Jérémy Collot, ArmagNet

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
require_once("engine/bo/AccountBo.php");
require_once("engine/utils/SessionUtils.php");

$user = SessionUtils::getUser($_SESSION);
$userId = SessionUtils::getUserId($_SESSION);
$accountBo = AccountBo::newInstance(openConnection());
$data = array();

$accountId = $_REQUEST["id"];

$account = array();
$account["sna_id"] = $_REQUEST["id"];
$account["sna_name"] = $_REQUEST["name"];
$account["sco_validation_score"] = $_REQUEST["validationScore"];
$account["sco_anonymous_permitted"] = $_REQUEST["anonymousPermitted"];
$account["sco_anonymous_password"] = $_REQUEST["anonymousPassword"];

$account["stc_api_key"] = trim($_REQUEST["apiKey"]);
$account["stc_api_secret"] = trim($_REQUEST["apiSecret"]);
$account["stc_access_token"] = trim($_REQUEST["accessToken"]);
$account["stc_access_token_secret"] = trim($_REQUEST["accessTokenSecret"]);

$account["smc_url"] = trim($_REQUEST["url"]);
$account["smc_client_id"] = trim($_REQUEST["clientId"]);
$account["smc_client_secret"] = trim($_REQUEST["clientSecret"]);
$account["smc_user_token"] = trim($_REQUEST["userToken"]);
$account["smc_token_type"] = trim($_REQUEST["tokenType"]);

$account["sfp_page_id"] = trim($_REQUEST["pageId"]);
$account["sfp_access_token"] = trim($_REQUEST["fpAccessToken"]);

$administratorIds = json_decode($_REQUEST["administratorIds"]);
$validatorGroups = json_decode($_REQUEST["validatorGroups"], true);

$account["administrators"] = array();
foreach($administratorIds as $administratorId) {
	$account["administrators"][] = array("use_id" => $administratorId, "use_login" => "");
}

$account["validatorGroups"] = $validatorGroups;

$accountBo->save($account);

// If the id is 0 then it's an add, so we return the id
if (!$accountId) {
	$data["id"] = $account["sna_id"];
}
$data["ok"] = "ok";

echo json_encode($data);
?>
