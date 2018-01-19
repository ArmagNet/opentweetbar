/*
    Copyright 2014-2017 Cédric Levieux, Jérémy Collot, ArmagNet

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

function humanFileSize(bytes, si) {
    var thresh = si ? 1000 : 1024;
    if(Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }
    var units = si
        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    }
    while(Math.abs(bytes) >= thresh && u < units.length - 1);
    return bytes.toFixed(1)+' '+units[u];
}

function changeStatus(data, field) {h
	if (data.ok && !data.exist) {
		$("#" + field).addClass("glyphicon-ok");
		$("#" + field).removeClass("glyphicon-remove");
		$("#" + field).parents(".has-feedback").addClass("has-success");
		$("#" + field).parents(".has-feedback").removeClass("has-error");
	}
	else {
		$("#" + field).removeClass("glyphicon-ok");
		$("#" + field).addClass("glyphicon-remove");
		$("#" + field).parents(".has-feedback").removeClass("has-success");
		$("#" + field).parents(".has-feedback").addClass("has-error");
	}
	$("#" + field).show();

	verifyAll();
}

function verify_nickname() {
	$("#nicknameHelp").hide();
	var value = $("#nicknameInput").val().trim();

	if (!value) {
		changeStatus({ko: "ko"}, "nicknameStatus");
		$("#nicknameHelp").html(register_validation_user_empty);
		$("#nicknameHelp").show();
	}
	else {
		changeStatus({ok: "ok", exist: false}, "nicknameStatus");
	}
}

function verify_password() {
	$("#apasswordHelp").hide();
	var value = $("#apasswordInput").val().trim();

	if (!value) {
		changeStatus({ko: "ko"}, "apasswordStatus");
		$("#apasswordHelp").html(register_validation_password_empty);
		$("#apasswordHelp").show();
	}
	else {
		changeStatus({ok: "ok", exist: false}, "apasswordStatus");
	}
}

function verify_mail() {
	var value = $("#xxxInput").val().trim();
    var mailRegExp = new RegExp("^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$");
	$("#mailHelp").hide();

	if (!value) {
		changeStatus({ko: "ko"}, "mailStatus");
		$("#mailHelp").html(register_validation_mail_empty);
		$("#mailHelp").show();
	}
	else if (mailRegExp.test(value.toUpperCase()) === false) {
		changeStatus({ko: "ko"}, "mailStatus");
		$("#mailHelp").html(register_validation_mail_not_valid);
		$("#mailHelp").show();
	}
	else {
		changeStatus({ok: "ok", exist: false}, "mailStatus");
	}
}

function verifyAll() {

	var numberOfKos =  $(".glyphicon-remove:visible").length;
	var visibileInput = $("#tweet:visible,#tweet-big:visible");

	if ($("#nicknameInput").length > 0 && !$("#nicknameInput").val()) numberOfKos++;
	if ($("#xxxInput").length > 0 && !$("#xxxInput").val()) numberOfKos++;
	if ($("#apasswordInput").length > 0 && !$("#apasswordInput").val()) numberOfKos++;
	if (!visibileInput.val()) numberOfKos++;

	if (numberOfKos) {
		$('#tweetButton').attr("disabled", "disabled");
	}
	else {
		$('#tweetButton').removeAttr("disabled");
	}
}

function computeTweetLength(text, maxLength) {
//	return 140 - text.length - ($(".mediaImage").length > 0 ? 24 : 0);
	return maxLength - text.length;
}

function urlized(tweetContent) {
	var urlRegExp = /((https?):\/\/([a-z.\/0-9\-\_%#]*))/mig;
	var m;
	var urls = [];
	var finalContent = tweetContent;

	while ((m = urlRegExp.exec(tweetContent)) !== null) {

	    if (m.index === urlRegExp.lastIndex) {
	        re.lastIndex++;
	    }
	    // View your result using the m-variable.
	    // eg m[0] etc.

	    if (m[3].length > 15) {
		    finalContent = finalContent.replace(m[0], m[2] + "://################" + urls.length);
		    urls[urls.length] = m[0];
	    }
	}

	var returned = {urls: urls, content: finalContent};

	return returned;
}

function cutTweet(text, tweets, urls, hasImage, maxLength, type) {
//	var maxLength = 140 - 7 - (hasImage ? 22 : 0);

	if (text.length > maxLength) {
		var cutLength = text.regexLastIndexOf(/[ ,;]/, maxLength);

		var tweet = text.substring(0, cutLength).trim();
		tweets[tweets.length] = tweet;

		text = text.substring(cutLength + 1).trim();

		cutTweet(text, tweets, urls, false, maxLength, type);

		return;
	}

	tweets[tweets.length] = text;

	var cutTweets = $("#cutTweets ul");

	for(var index = 0; index < tweets.length; ++index) {
		var cutTweetElement = $("<li class='list-group-item "+type+"'></li>");
		var text = tweets[index];

		for(var jndex = 0; jndex < urls.length; ++jndex) {
			text = text.replace("http://################" + jndex, urls[jndex]);
			text = text.replace("https://################" + jndex, urls[jndex]);
		}

		text = text.replace(/\n/g, "<br>");

		cutTweetElement.html(text);

		var position = (index + 1) + "/" + tweets.length;

		cutTweetElement.append($("<span class='badge'>" + position + "</span>"));

		cutTweets.append(cutTweetElement);
	}
}

function mediaProgressHandlingFunction(e) {
    if (e.lengthComputable){
        var percent = Math.floor(e.loaded / e.total * 100);

        $("#mediaProgress .progress-bar").attr("aria-valuenow", percent);
        $("#mediaProgress .progress-bar").css({width: percent + "%"});
    }
}

$(function() {
	$("#mediaInput").change(function() {
		// Check size limit
	    if (window.FileReader) {
	    	var file = $("#mediaInput").get(0).files[0];
	    	var maxSize = $(this).data("max-size");
	    	var types = $(this).data("authorized-types");

	    	if (maxSize && maxSize < file.size) {
    			$("#mediaInput").val("");
    			$("#mediaProgress").hide();
    			$("#mediaInput").show();

				$("#error_media_sizeErrorAlert #maxSize").text(humanFileSize(maxSize, false));
    			$("#error_media_sizeErrorAlert").show().delay(2000).fadeOut(1000);

    			return;
	    	}

	    	if (types && file.type) {
	    		types = types.split(",");

	    		var authorized = false;

	    		for(var index = 0; index < types.length; ++index) {
	    			if (file.type == types[index]) {
	    				authorized = true;
	    				break;
	    			}
	    		}

	    		if (!authorized) {
	    			$("#mediaInput").val("");
	    			$("#mediaProgress").hide();
	    			$("#mediaInput").show();

	    			$("#error_media_typeErrorAlert").show().delay(2000).fadeOut(1000);

	    			return;
	    		}
	    	}
	    }

	    var formData = new FormData($('#optionForm')[0]);

        $("#mediaProgress .progress-bar").attr("aria-valuenow", 0);
        $("#mediaProgress .progress-bar").css({width: "0"});
		$("#mediaProgress").show();
		$("#mediaInput").hide();

	    $.ajax({
	        url: 'do_uploadMedia.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', mediaProgressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);

        		if (data.ok) {
        			var imageElement = "<img id=\"medId_" + data.media.med_id + "\" class=\"mediaImage\" src=\"";
        			imageElement += "do_loadMedia.php?med_id=" + data.media.med_id + "&med_hash=" + data.media.med_hash;
        			imageElement += "\" style=\"max-width: 100px; max-height: 100px; margin-right: 5px; margin-top: 5px; \"/>";

        			$("#mediaInput").parent().append(imageElement);
        			$("#mediaInput").val("");
        			$("#mediaProgress").hide();
        			$("#mediaInput").show();

        			$("#mediaIds").val($("#mediaIds").val() + "," + data.media.med_id);

        			if ($(".mediaImage").length >= 4) {
            			$("#mediaInput").hide();
        			}
        		}
        		else {
        			$("#mediaInput").val("");
        			$("#mediaProgress").hide();
        			$("#mediaInput").show();

        			if (data.maxSize) {
        				$("#" + data.message + "Alert #maxSize").text(humanFileSize(data.maxSize, false));
        			}
        			$("#" + data.message + "Alert").show().delay(2000).fadeOut(1000);
        		}
	        },
	        data: formData,
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	});

	$("#cgvInput").click(function(event) {
		if ($("#cgvInput").attr("checked")) {
			$("#cgvInput").removeAttr("checked");
		}
		else {
			$("#cgvInput").attr("checked", "checked");
		}
	});

	$("#nicknameInput, #xxxInput, #apasswordInput").keyup(function() {
		var field = $(this).attr("id");

		switch(field) {
			case "nicknameInput":
				field = "nickname";
				break;
			case "xxxInput":
				field = "mail";
				break;
			case "apasswordInput":
				field = "password";
				break;
			}

		eval("verify_" + field + "();");
	});

	$("input[name=supports]").click(function() {
		$("#tweet,#tweet-big").keyup();
	});

	$("#tweet,#tweet-big").keyup(function() {
		var visibileInput = $("#tweet:visible,#tweet-big:visible");

		var tweetContent = visibileInput.val();

		var computed = urlized(tweetContent);
		tweetContent = computed.content;

		$("#cutTweets").hide();
		$("#cutTweets ul").children().remove();

		if (tweetContent) {
			
			var hasTwitterCuts = false;
			
			{
				var tweetLength = computeTweetLength(tweetContent, 280);
				
				if (tweetLength < 0 && $("input[name=supports][value=twitter]:checked").length) {
					var cutTweetElement = $("<li class='list-group-item twitter'><span class='social grey twitter' style='height: 30px; margin-right: -5px; '></span> Twitter</li>");
					$("#cutTweets ul").append(cutTweetElement);

					cutTweet(tweetContent, [], computed.urls, $(".mediaImage").length > 0 ? true : false, 273, "twitter");
					$("#cutTweets").show();
					hasTwitterCuts = true;
				}
	
				verifyAll();
	
				$(".twitter .tweeter-count").text(tweetLength);
			}
			{
				var tweetLength = computeTweetLength(tweetContent, 500);
	
				if (tweetLength < 0 && $("input[name=supports][value=mastodon]:checked").length) {
					if (hasTwitterCuts) {
						var cutTweetElement = $("<li class='list-group-item twitter mastodon'></li>");
						$("#cutTweets ul").append(cutTweetElement);
					}

					var cutTweetElement = $("<li class='list-group-item mastodon'><img src='images/mastodon.svg' style='height: 24px; position: relative; top: -3px;'> Mastodon</li>");
					$("#cutTweets ul").append(cutTweetElement);

					cutTweet(tweetContent, [], computed.urls, $(".mediaImage").length > 0 ? true : false, 493, "mastodon");
					$("#cutTweets").show();
					
				}
	
				verifyAll();
	
				$(".mastodon .tweeter-count").text(tweetLength);
			}

			$("#cutTweets .twitter").show();
			$(".twitter .tweeter-count").parent().show();
			$("#cutTweets .mastodon").show();
			$(".mastodon .tweeter-count").parent().show();

			if (!$("input[name=supports][value=twitter]:checked").length) {
				$("#cutTweets .twitter").hide();
				$(".twitter .tweeter-count").parent().hide();
			}

			if (!$("input[name=supports][value=mastodon]:checked").length) {
				$("#cutTweets .mastodon").hide();
				$(".mastodon .tweeter-count").parent().hide();
			}
		}
		else {
			$(".twitter .tweeter-count").parent().hide();
			$(".mastodon .tweeter-count").parent().hide();

			$(".tweeter-count").text("");
			$("#tweetButton").attr("disabled", "disabled");
		}
	});

	$("#tweetButton").click(function() {
		$("#tweetButton").attr("disabled", "disabled");

		var supportInputs = $("#supportDiv input:checked");
		var supports = [];

		supportInputs.each(function() {
			supports[supports.length] = $(this).val();
		});

		// If there is no supports, only do nothing
		if (supports.length == 0) return;

		var tweetContent = $("#tweet:visible,#tweet-big:visible").val();

		var myform = 	{
							supports: JSON.stringify(supports),
							account: $("#account").val(),
							tweet: tweetContent,
							mediaIds: $("#mediaIds").val(),
							mail: $("#mail").val(),
							cgv: $("#cgvInput").attr("checked") ? "badboy" : "okgirls",
							validationDuration: $("#validationDurationInput").val(),
							cronDate: $("#cronDateInput").val(),
							password: ""
						};

		myform["secondaryAccounts[]"] = [];

		$(".secondaryAccounts:checked").each(function() {
			if (!$(this).attr("disabled")) {
				myform["secondaryAccounts[]"][myform["secondaryAccounts[]"].length] = $(this).val();
			}
		});

		if ($("#nicknameInput").val()) {
			myform.nickname = $("#nicknameInput").val().trim();
			myform.xxx = $("#xxxInput").val().trim();
		}

		if ($("#apasswordInput").val()) {
			myform.password = $("#apasswordInput").val().trim();
		}

		$.post("do_addTweet.php", myform, function(data) {
			$("#tweetButton").attr("disabled", "disabled");

			if (data.ok) {
				$("#okTweetAlert").show().delay(2000).fadeOut(1000);
				$("#tweet").val("");
				$("#tweet-big").val("");
				$("#cronDateInput").val("");
				$("#validationDurationButtons button[value=0]").click();
				$("#tweet").keyup();
				$("#validationMenuItem .badge").text($("#validationMenuItem .badge").text() - (-1 - myform["secondaryAccounts[]"].length)).show();

				// We clean medias
    			$(".mediaImage").remove();
        		$("#mediaIds").val("-1");
        		$("#mediaInput").show();
			}
			else {
				$("#koTweetAlert").show().delay(2000).fadeOut(1000);
			}
		}, "json");
	});

	$('#cronDateInput').parent("div").datetimepicker({
    	language: userLanguage
	});

	$(".changeAccountLink").click(function(event) {
		if (event) event.preventDefault();
		var accountText = $(this).text().trim();

		var account = accounts[accountText];

		$("#supportDiv label input").attr("disabled", "disabled");
		$("#supportDiv label input").removeAttr("checked");

		if (account.hasFacebookPage) {
			$("#supportDiv label#facebookLabel input").removeAttr("disabled");
			$("#supportDiv label#facebookLabel input").click();
		}
		if (account.hasTwitter) {
			$("#supportDiv label#tweetLabel input").removeAttr("disabled");
			$("#supportDiv label#tweetLabel input").click();
		}
		if (account.hasMastodon) {
			$("#supportDiv label#mastodonLabel input").removeAttr("disabled");
			$("#supportDiv label#mastodonLabel input").click();
		}

		$("#supportDiv label#facebookLabel input").change();
		$("#supportDiv label#mastodonLabel input").change();

		$("#account").val(accountText);
		$("#account2").val(accountText);
		$("#accountButton #text").text(accountText);

		$(".secondaryAccounts").each(function() {
			if ($(this).val() == accountText) {
				$(this).attr("disabled", "disabled");
			}
			else {
				$(this).removeAttr("disabled");
			}
		});
	});

	$("#validationDurationButtons button").click(function(e) {
		$("#validationDurationButtons button").removeClass("active");
		$(this).addClass("active");
		$("#validationDurationInput").val($(this).val());
	});

	$("#supportDiv label input").click(function() {
		if ($(this).attr("checked")) {
			$(this).removeAttr("checked");
		}
		else {
			$(this).attr("checked", "checked");
		}
		$(this).change();
	});

	$("#supportDiv label#facebookLabel input").change(function() {
//		if ($(this).attr("checked")) {
//			if ($("#tweet").is(":visible")) {
				$("#tweet").hide();
				$("#tweet-big").show();
//				$("#tweet-big").val($("#tweet").val());

				$("#accountButton").css({height: $("#tweet-big").css("height")});
				$("#tweetButton").css({height: $("#tweet-big").css("height")});
//			}
//		}
//		else {
//			if (!$("#tweet").is(":visible")) {
//				$("#tweet").show();
//				$("#tweet-big").hide();
//				$("#tweet").val($("#tweet-big").val());
//
//				$("#accountButton").css({height: ""});
//				$("#tweetButton").css({height: ""});
//			}
//		}
	});

	$("#tweet").keyup();
	$(".changeAccountLink").click();
});
