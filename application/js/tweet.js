function getHtmlTweet(tweet) {
	var date = tweet.created_at;
	date = new Date(date);
	date = date.toLocaleString();

	var source = tweet;
	if (tweet.retweeted_status) {
		source = tweet.retweeted_status;
	}

	var text = source.text;
	text = text.replace(/\n/g, "<br>");

	// handle hashtags
	for(var index = 0; index < source.entities.hashtags.length; ++index) {
		var hashtag = source.entities.hashtags[index];
		var re = new RegExp("#" + hashtag.text, "g");
		var url = "https://twitter.com/hashtag/"+hashtag.text+"?src=hash";
		text = text.replace(re, "<a href=\""+url+"\" target=\"_blank\">#"+hashtag.text+"</a>");
	}

	// handle urls
	for(var index = 0; index < source.entities.urls.length; ++index) {
		var turl = source.entities.urls[index];
		var re = new RegExp(turl.url, "g");
		var url = turl.expanded_url;
		text = text.replace(re, "<a href=\""+url+"\" target=\"_blank\">"+turl.display_url+"</a>");
	}

	// handle mentions
	for(var index = 0; index < source.entities.user_mentions.length; ++index) {
		var userMention = source.entities.user_mentions[index];
		var re = new RegExp("@" + userMention.screen_name, "g");
		var url = "https://twitter.com/"+userMention.screen_name;
		text = text.replace(re, "<a href=\""+url+"\" target=\"_blank\">@"+userMention.screen_name+"</a>");
	}

	// TODO handle medias

	var data = {
			"tweet_user_screen_name" : tweet.user.screen_name,
			"tweet_user_name" : tweet.user.name,
			"source_user_screen_name" : source.user.screen_name,
			"source_user_name" : source.user.name,
			"source_text" : text,
			"source_created_at" : date,
			"source_id_str" : source.id_str
	};

	if (tweet.retweeted_status) {
		html = $("*[aria-template-id=template-retweet]").template("use", { "data": data });
	}
	else {
		html = $("*[aria-template-id=template-tweet]").template("use", { "data": data });
	}

	return html;
}