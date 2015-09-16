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

					$("#account-panel-"+data.accountId+" .panel-body").prepend(html);
				}
				else {
					addInStack(data.accountId, data.timeline[index]);

					var numberOfTweetsDiv = null;

					if (stacks[accountId].length > 1) {
						numberOfTweetsDiv = $("*[aria-template-id=template-waiting-tweets]").template("use", { "data": {"numberOfTweets" : stacks[accountId].length} });
					}
					else {
						numberOfTweetsDiv = $("*[aria-template-id=template-one-waiting-tweet]").template("use", { "data": {"numberOfTweets" : stacks[accountId].length} });
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

		var wait = $("*[aria-template-id=template-waiting]").template("use");

		$("#account-panel-"+accountId+" .panel-body").prepend(wait);

		getNewTweets(accountId, sinceId, 10, true);
	});

	$("#searchTweetForm #searchButton").click(function() {
		var tweetId = $("#searchTweetForm #searchInput").val();

		if (tweetId.lastIndexOf("/") != -1) {
			tweetId = tweetId.substring(tweetId.lastIndexOf("/") + 1);
		}

		$.post("do_searchTweet.php", {"tweetId": tweetId}, function(data) {
			$("#found-tweet-div").html(data);
		}, "html");
	});

	setTimeout(updateTweets, 120000);
});