/*
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

var stacks = {};

function addInStack(accountId, tweet) {
	if (!stacks[accountId]) {
		stacks[accountId] = [];
	}

	stacks[accountId][stacks[accountId].length] = tweet;
}

function unstackTweets(accountId) {
	while(stacks[accountId].length) {
		var tweet = stacks[accountId].shift();
		var html = getHtmlTweet(tweet);
		addTweetHandlers(html);

		$("#account-panel-"+accountId+" .panel-body").prepend(html);
		$("#account-panel-"+accountId+" .panel-body .number-of-tweets").remove();
	}
}

function getNewTweets(accountId, sinceId, numberOfTweets, showDirectly) {
	var myform = {"accountId" : accountId};
	if (sinceId) {
		myform["sinceId"] = sinceId;
	}
	if (numberOfTweets) {
		myform["numberOfTweets"] = numberOfTweets;
	}

	$.post("do_retrieveTweets.php", myform, function(data) {
		if (data.ok && data.timeline && !data.timeline.error) {
			for(var index = data.timeline.length - 1; index >= 0; --index) {
				if (index == 0) {
					var accountPanel = $("#account-panel-"+data.accountId);
					accountPanel.data("since-id", data.timeline[index].id_str);
				}

				if (showDirectly) {
					var html = getHtmlTweet(data.timeline[index]);
					addTweetHandlers(html);

					$("#account-panel-"+data.accountId+" .panel-body").prepend(html);
				}
				else {
					addInStack(data.accountId, data.timeline[index]);

					var numberOfTweetsDiv = null;

					if (stacks[accountId].length > 1) {
						numberOfTweetsDiv = $("*[data-template-id=template-waiting-tweets]").template("use", { "data": {"numberOfTweets" : stacks[accountId].length} });
					}
					else {
						numberOfTweetsDiv = $("*[data-template-id=template-one-waiting-tweet]").template("use", { "data": {"numberOfTweets" : stacks[accountId].length} });
					}

					numberOfTweetsDiv.click(function() {
						var accountId = $(this).parents("div.account-panel").data("account-id");
						unstackTweets(accountId);
					});

					$("#account-panel-"+data.accountId+" .panel-body .number-of-tweets").remove();
					$("#account-panel-"+data.accountId+" .panel-body").prepend(numberOfTweetsDiv);
				}
			}

			$("#account-panel-"+data.accountId+" .panel-body .wait").remove();
		}
	}, "json");
}

function updateTweets() {
	$(".account-panel").each(function() {
		var accountId = $(this).data("account-id");
		var sinceId = $(this).data("since-id");

		getNewTweets(accountId, sinceId, null, false);
	});

	setTimeout(updateTweets, 120000);
}

$(function() {
	$(".account-panel").each(function() {
		var accountId = $(this).data("account-id");
		var sinceId = $(this).data("since-id");

		var wait = $("*[data-template-id=template-waiting]").template("use");

		$("#account-panel-"+accountId+" .panel-body").prepend(wait);

		getNewTweets(accountId, sinceId, 10, true);
	});

	$("#searchTweetForm #searchButton").click(function() {
		var tweetId = $("#searchTweetForm #searchInput").val();

		if (tweetId.lastIndexOf("/") != -1) {
			tweetId = tweetId.substring(tweetId.lastIndexOf("/") + 1);
		}

		$("#found-tweet-div").children().remove();

		$.post("do_searchTweet.php", {"tweetId": tweetId}, function(data) {
			if (data.ok && data.tweet) {
				var tweet = data.tweet;
				var tweetHtml = getHtmlTweet(tweet);
				addTweetHandlers(tweetHtml);

				$("#found-tweet-div").append(tweetHtml);
			}
//			$("#found-tweet-div").html(data);
		}, "json");
	});

	setTimeout(updateTweets, 120000);
});