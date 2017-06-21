<?php

require_once 'engine/mastodon/Mastodon-api.php';

$mastodon_api = new Mastodon_api();
$mastodon_api->set_url('https://mastodon.partipirate.org/');

$response = $mastodon_api->create_app('Test Farli 3',null,null,'https://www.opentweetbar.org/');
print_r($response);

//$clientId = $response["html"]["client_id"];
//$clientSecret = $response["html"]["client_secret"];
$clientId = $response["html"]["client_id"];
$clientSecret = $response["html"]["client_secret"];
//$bearer = "7d350b6694d38e0bcb561be7977bcdfcc0825e5669eea8d5e030678286c5a527";
//echo "Client id : $clientId\n";
//echo "Client secret : $clientSecret\n";

//echo "Client id : " . $mastodon_api->client_id . "\n";
//echo "Client secret : ". $mastodon_api->client_secret . "\n";

$mastodon_api->set_client($clientId, $clientSecret);

$response = $mastodon_api->login('contact@levieuxcedric.com','archange');

$userToken = $response["html"]["access_token"];

echo "Client id : $clientId\n";
echo "Client secret : $clientSecret\n";
echo "User token : $userToken\n";


//$mastodon_api->set_token($bearer,'bearer');

//$response= $mastodon_api->timelines_home();
//print_r($response);

$path = "/usr/share/nginx/html/favicon.png";

echo "File exist : #" . file_exists($path) . "#\n";

$response = $mastodon_api->media($path);
print_r($response);

$status = array();

/*
*          string      $parameters['status']               The text of the status
*          int         $parameters['in_reply_to_id']       (optional): local ID of the status you want to reply to
*          int         $parameters['media_ids']            (optional): array of media IDs to attach to the status (maximum 4)
*          string      $parameters['sensitive']            (optional): set this to mark the media of the status as NSFW
*          string      $parameters['spoiler_text']         (optional): text to be shown as a warning before the actual content
*          string      $parameters['visibility']           (optional): either "direct", "private", "unlisted" or "public"
*/

//$status['status'] = "This is test toot !";
//$response = $mastodon_api->post_statuses($status);
//print_r($response);

?>