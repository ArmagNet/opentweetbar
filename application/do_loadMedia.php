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
include_once("config/database.php");
require_once("engine/utils/SessionUtils.php");
require_once("engine/bo/MediaBo.php");
require_once("engine/bo/UserBo.php");
include_once("language/language.php");

$connection = openConnection();

$mediaBo = MediaBo::newInstance($connection);

$media = array();
$media["med_id"] = $_REQUEST["med_id"];
$media["med_hash"] = UserBo::computePassword($media["med_id"]);

if ($media["med_hash"] != $_REQUEST["med_hash"]) {
	exit();
}

$media = $mediaBo->getMedia($_REQUEST["med_id"]);

header('Content-Type: ' . $media["med_mimetype"]);

echo $media["med_content"];

?>