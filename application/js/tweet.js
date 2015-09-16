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

	// TODO handle hashtags
	// TODO handle mentions

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