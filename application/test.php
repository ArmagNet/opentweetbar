<?php

function cutTweet($text, &$tweets) {
	$maxLength = 140 - 7;

	if (strlen($text) > $maxLength) {
		$cutLength = regexLastIndexOf($text, '/[ ,;]/mi', $maxLength);

		$tweet = trim(substr($text, 0, $cutLength + 1));
		$tweets[] = $tweet;

		$text = trim(substr($text, $cutLength + 1));

		cutTweet($text, $tweets);

		return;
	}

	$tweets[] = $text;

	// add n/m
	foreach($tweets as $index => $tweet) {
		$tweets[$index] = $tweet . " " . ($index + 1) . "/" . count($tweets);
	}

	return;
}

function regexLastIndexOf($haystack, $regex, $startpos) {
	preg_match_all($regex, $haystack, $matches, PREG_OFFSET_CAPTURE);

//	print_r($matches);

	$indexOf = -1;
	foreach($matches[0] as $match) {
		if ($match[1] + strlen($match[0]) - 1 <= $startpos) {
			$indexOf = $match[1];
		}
		else {
			break;
		}
	}

	return $indexOf;
}

$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi sed vehicula diam, vitae faucibus est. Aenean sagittis tincidunt justo, vulputate pellentesque quam suscipit eget. Maecenas sit amet justo eget lorem eleifend accumsan ut a odio. Maecenas sodales ultrices eros ut commodo. Sed blandit ornare augue quis porta. Praesent condimentum ligula faucibus, hendrerit ante quis, laoreet urna. Maecenas sagittis consectetur scelerisque. Praesent euismod diam sit amet ligula congue, ut dictum sem mollis. Proin consequat justo a nibh dictum, in laoreet sem faucibus.

Quisque tristique lectus a sapien ultricies sollicitudin. Quisque dictum eros sed lorem facilisis, quis aliquet odio elementum. Nunc posuere risus purus, a bibendum urna vehicula ut. Integer auctor mi sem, faucibus volutpat tortor mattis eu. Donec et finibus est. Proin posuere rhoncus bibendum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In vel lacus dictum, bibendum elit ac, iaculis ipsum. Aenean interdum tortor a urna interdum ullamcorper. Praesent ac nunc et urna commodo mollis eget sed urna. Pellentesque et erat dui. Nam nec urna in arcu rhoncus varius eget vel nulla. Vivamus placerat pretium blandit. Donec fringilla leo vel orci vestibulum, fringilla sollicitudin odio maximus.

Aenean vitae placerat felis, ac laoreet justo. Etiam euismod nisl non lacinia bibendum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut metus nec enim laoreet efficitur. Quisque non consectetur risus. Phasellus tincidunt sem vel felis commodo, ut venenatis diam luctus. Duis nec magna ultrices, laoreet turpis vitae, cursus tortor. Cras nisi eros, pretium et cursus vitae, porta sit amet neque. Integer vestibulum orci mattis malesuada sagittis. Vestibulum volutpat nulla id elit aliquet posuere. Proin vel dolor facilisis, facilisis sem dignissim, pulvinar nisl. Sed non est hendrerit, convallis est et, consectetur diam. In lacus augue, egestas non ipsum porttitor, lobortis fermentum nulla. Vestibulum ac orci hendrerit, euismod lacus sit amet, dignissim est.

Vivamus congue sapien ac congue mollis. Praesent molestie odio id ligula auctor maximus. Maecenas nec tempor libero. In vel elit ultrices, dictum dolor at, dapibus justo. Curabitur vitae orci laoreet, sollicitudin nisi vitae, congue diam. Pellentesque dapibus, sapien aliquet vestibulum ornare, turpis urna iaculis ex, quis efficitur ligula nisi non lorem. Aliquam et vehicula erat. Donec convallis ligula libero, vel aliquet orci blandit sit amet. Nunc eu accumsan nisl, quis viverra odio. Curabitur laoreet, turpis quis accumsan faucibus, lacus nisi tempus mauris, ut vehicula nunc arcu ut mauris. Suspendisse vestibulum ultrices justo non dictum. Nam ornare aliquam pharetra. Nam sed risus ac ante consectetur faucibus.

Aliquam tincidunt erat egestas magna ultricies, vitae bibendum enim aliquam. Donec venenatis rhoncus orci, et viverra libero elementum sed. Phasellus tellus dolor, condimentum vitae purus non, tempor molestie lacus. Nam convallis a lorem ut sagittis. Vestibulum ultricies ullamcorper mauris eget elementum. Sed pulvinar dui sed urna porta rutrum. Etiam dapibus erat at tincidunt suscipit. Nulla bibendum purus id risus vehicula, vitae posuere neque mollis.";

//echo regexLastIndexOf($text, '/[ ,;]/mi',140);
//echo "\n";

$tweets = array();
cutTweet($text, $tweets);

foreach($tweets as $tweet) {
	echo $tweet;
	echo "\n\t" . strlen($tweet) . "\n";
}

?>