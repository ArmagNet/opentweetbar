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

require_once("engine/facebook/facebook.php");

$pageId = $_REQUEST["pageId"];
$applicationId = $_REQUEST["applicationId"];
$applicationSecretKey = $_REQUEST["applicationSecretKey"];
$shortLiveUserAccessToken = $_REQUEST["shortLiveUserAccessToken"];

$longLivefacebookClientApi = new FacebookApiClient($shortLiveUserAccessToken);
$response = $longLivefacebookClientApi->oauthAccessToken($applicationId, $applicationSecretKey);

error_log(print_r($response, true));

$longLiveUserAccessToken = $response["access_token"];

$pagefacebookClientApi = new FacebookApiClient($longLiveUserAccessToken);
$response = $pagefacebookClientApi->meAccounts();

$pageAccessToken = "";

error_log(print_r($response, true));

foreach($response["data"] as $datum) {
	if ($datum["id"] == $pageId) {
		$pageAccessToken = $datum["access_token"];
	}
}

echo json_encode(array("accessToken" => $pageAccessToken));

?>
