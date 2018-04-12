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

class FacebookApiClient {
	var $apiUrl;
	var $version;
	var $token;

	function __construct($token, $apiUrl = "https://graph.facebook.com", $version = "2.6") {
		$this->apiUrl = $apiUrl;
		$this->version = $version;
		$this->token = $token;
	}

	function oauthAccessToken($clientId, $clientSecret) {
		$fields = array();
		$fields["grant_type"] = "fb_exchange_token";
		$fields["client_id"] = $clientId;
		$fields["client_secret"] = $clientSecret;
		$fields["fb_exchange_token"] = $this->token;

		$response = $this->_get("/oauth/access_token", $fields);

		return $response;
	}

	function meAccounts() {
		$response = $this->_get("/me/accounts", array("access_token" => $this->token));

		return $response;
	}

	function getMessages($pageId) {
		$messages = $this->_get("/$pageId/feed", array("access_token" => $this->token));

		return $messages;
	}

	function postMessage($pageId, $message, $imageIds = null) {

		$request = array("access_token" => $this->token, "message" => $message);

		if ($imageIds) {
			if (count($imageIds) == 1) {
//				$request["object_attachment"] = $imageIds[0];

  				$request["link"] = $imageIds[0];
//  				$request["link"] = "";
  				$request["picture"] = $imageIds[0];
			}
// 			else if (count($imageIds) > 1) {
// 				$childAttachments = array();

// 				foreach($imageIds as $imageId) {
// 					$childAttachments[] = array("link" => $imageId, "picture" => $imageId);
// 				}

// 				$request["child_attachments"] = $childAttachments;
// 			}
		}

		$response = $this->_post("/$pageId/feed", $request);

		return $response;
	}

	function postImageByUrl($pageId, $url) {
//		$response = $this->_post("/$pageId/photos", array("access_token" => $this->token, "url" => $url, "published" => "false"));
		$response = $this->_post("/$pageId/photos", array("access_token" => $this->token, "url" => $url));

		return $response;
	}

	function _exec(&$ch) {
		// Execute request
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		// json decode the result, the api has json encoded result
		$result = json_decode($result, true);

		return $result;
	}

	function _get($method, $fields) {
		$url = $this->apiUrl;
		$url .= "/v" . $this->version;
		$url .= $method;
		$url .= "?";

		//url-ify the data for the GET
		$fieldsString = http_build_query($fields);

		$url .= $fieldsString;

		//open connection
		$ch = curl_init();

		//set the url and say that we want the result returnd not printed
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//execute get
		return $this->_exec($ch);
	}

	function _post($method, $fields) {

		$url = $this->apiUrl;
		$url .= "/v" . $this->version;
		$url .= $method;

//		error_log("FB API Url call : " . $url);

		//url-ify the data for the POST
		$fieldsString = http_build_query($fields);

		error_log("Post Request");
		error_log($fieldsString);

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data, and say that we want the result returnd not printed
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//execute post
		return $this->_exec($ch);
	}
}
?>