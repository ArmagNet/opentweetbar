<?php
if(!isset($config)) {
	$config = array();
}

$config["smtp"] = array();
$config["smtp"]["host"] = "smtp.myserver.com";
$config["smtp"]["port"] = "587";
$config["smtp"]["username"] = "webposter@myserver.com";
$config["smtp"]["password"] = "webposter_password";
$config["smtp"]["from.address"] = "contact@myserver.com";
$config["smtp"]["from.name"] = "MyOpenTweetBar";
?>