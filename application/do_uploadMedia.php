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

// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function file_upload_max_size() {
	static $max_size = -1;

	if ($max_size < 0) {
		// Start with post_max_size.
		$max_size = parse_size(ini_get('post_max_size'));

		// If upload_max_size is less, then reduce. Except if upload_max_size is
		// zero, which indicates no limit.
		$upload_max = parse_size(ini_get('upload_max_filesize'));
		if ($upload_max > 0 && $upload_max < $max_size) {
			$max_size = $upload_max;
		}
	}
	return $max_size;
}

function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
		return round($size);
	}
}

include_once("config/database.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/AccountBo.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

$connection = openConnection();

$accountBo = AccountBo::newInstance($connection);
$mediaBo = MediaBo::newInstance($connection);

if (!isset($_FILES["mediaInput"])) {
	echo json_encode(array("ko" => "no_file"));
	exit();
}

$account = $_REQUEST["account"];
$accountId = $accountBo->getAccountId($account);

$file = $_FILES["mediaInput"];

$media = array();
$media["med_name"] = $file["name"];
$media["med_mimetype"] = $file["type"];
$media["med_sna_id"] = $accountId;

if ($file["error"] != UPLOAD_ERR_OK) {
	$data = array("ko" => "ko");
	switch($file["error"]) {
		case UPLOAD_ERR_INI_SIZE :
			$data["message"] = "error_media_sizeError";
			$data["maxSize"] = file_upload_max_size();
			break;
		default:
			$data["message"] = "error_media_defaultError";
	}

	echo json_encode($data);
	exit();
}

$handle = fopen($file["tmp_name"], "r");
$media["med_content"] = fread($handle, filesize($file["tmp_name"]));
fclose($handle);

//echo $account . "\n";

//print_r($media);

$mediaBo->save($media);

$data = array();
$data["ok"] = "ok";
$data["media"]["med_id"] = $media["med_id"];
$data["media"]["med_mimetype"] = $media["med_mimetype"];

$data["media"]["med_hash"] = UserBo::computePassword($media["med_id"]);

echo json_encode($data);

?>