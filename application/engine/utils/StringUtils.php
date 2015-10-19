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
?>