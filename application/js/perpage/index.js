function changeStatus(data, field) {
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

	if ($("#nicknameInput").length > 0 && !$("#nicknameInput").val()) numberOfKos++;
	if ($("#xxxInput").length > 0 && !$("#xxxInput").val()) numberOfKos++;
	if ($("#apasswordInput").length > 0 && !$("#apasswordInput").val()) numberOfKos++;
	if (!$("#tweet").val()) numberOfKos++;

	if (numberOfKos) {
		$('#tweetButton').attr("disabled", "disabled");
	}
	else {
		$('#tweetButton').removeAttr("disabled");
	}
}

function computeTweetLenght(text) {
	return 140 - text.length;
}

function progressHandlingFunction(e) {
    if (e.lengthComputable){
        $('progress').attr({value:e.loaded, max:e.total});
        console.log(e.loaded / e.total);
    }
}

$(function() {
	$("#mediaInput").change(function() {
	    var formData = new FormData($('#optionForm')[0]);
	    $.ajax({
	        url: 'do_uploadMedia.php',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest
	            var myXhr = $.ajaxSettings.xhr();
	            if(myXhr.upload){ // Check if upload property exists
	                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
	            }
	            return myXhr;
	        },
	        //Ajax events
	        success: function(data) {
        		data = JSON.parse(data);

        		if (data.ok) {
        			$("#mediaImage").attr("src", "do_loadMedia.php?med_id=" + data.media.med_id + "&med_hash=" + data.media.med_hash);
        			$("#mediaInput").hide();
        			$("#mediaImage").show();
        			$("#mediaIds").val($("#mediaIds").val() + "," + data.media.med_id);
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

	$("#tweet").keyup(function() {

		if ($("#tweet").val()) {
			var tweetLength = computeTweetLenght($("#tweet").val());

			verifyAll();

			$(".tweeter-count").text(tweetLength);
		}
		else {
			$(".tweeter-count").text("");
			$("#tweetButton").attr("disabled", "disabled");
		}
	});

	$("#tweetButton").click(function() {
		var myform = 	{
							account: $("#account").val(),
							tweet: $("#tweet").val(),
							mediaIds: $("#mediaIds").val(),
							mail: $("#mail").val(),
							cgv: $("#cgvInput").attr("checked") ? "badboy" : "okgirls",
							validationDuration: $("#validationDurationInput").val(),
							cronDate: $("#cronDateInput").val(),
							password: ""
						};

		if ($("#nicknameInput").val()) {
			myform.nickname = $("#nicknameInput").val().trim();
			myform.xxx = $("#xxxInput").val().trim();
		}

		if ($("#apasswordInput").val()) {
			myform.password = $("#apasswordInput").val().trim();
		}

		$.post("do_addTweet.php", myform, function(data) {
			if (data.ok) {
				$("#okTweetAlert").show().delay(2000).fadeOut(1000);
				$("#tweet").val("");
				$("#cronDateInput").val("");
				$("#validationDurationButtons button[value=0]").click();
				$("#tweet").keyup();
				$("#validationMenuItem .badge").text($("#validationMenuItem .badge").text() - (-1)).show();
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
		event.preventDefault();
		var accountText = $(this).text().trim();

		$("#account").val(accountText);
		$("#account2").val(accountText);
		$("#accountButton #text").text(accountText);
	});

	$("#validationDurationButtons button").click(function(e) {
		$("#validationDurationButtons button").removeClass("active");
		$(this).addClass("active");
		$("#validationDurationInput").val($(this).val());
	});

	$("#tweet").keyup();
});