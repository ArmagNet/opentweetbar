$(function() {
	$(".fork-button").click(function() {
		var tweetId = $(this).data("tweet-id");
		var tweetAccount = $(this).data("account");

		var message = "<div>Pour les comptes ";

		for(var accountId in accountIdLabels) {
			var accountLabel = accountIdLabels[accountId];
			var checkbox = "<label><input type='checkbox' name=\"fork_account_ids\" ";
			if (accountLabel == tweetAccount) {
				checkbox += " disabled='disabled' ";
			}
			checkbox += " value='"+accountId+"' />" + accountLabel + "</label> ";

			message += checkbox;
		}
		message += "</div>";
		message = $(message);

		message.find("input[type=checkbox]").click(function(event) {
			if ($(this).attr("checked")) {
				$(this).removeAttr("checked");
			}
			else {
				$(this).attr("checked", "checked");
			}
		});

		bootbox.dialog({
            title: "Proposer ce tweet aux autres comptes ?",
            message: message,
            buttons: {
                success: {
                    label: "Fork",
                    className: "btn-primary",
                    callback: function () {
        				var forkForm = {"tweetId": tweetId, "secondaryAccounts[]": []};

        				forkForm["account"] = null;

        				$("input[name=fork_account_ids]").each(function() {
        					if ($(this).attr("checked")) {
        						if (!forkForm["account"]) {
        							forkForm["account"] = accountIdLabels[$(this).val()];
        						}
        						else {
        							forkForm["secondaryAccounts[]"][forkForm["secondaryAccounts[]"].length] = accountIdLabels[$(this).val()];
        						}
        					}
        				});

        				if (!forkForm["account"]) return;

        				$.post("do_forkTweet.php", forkForm, function(data) {
        					if (data.ok) {
        						$("#validationMenuItem .badge").text($("#validationMenuItem .badge").text() - (-1 - forkForm["secondaryAccounts[]"].length)).show();

        						// reload page
        					}
        				}, "json");
                    }
                }
            }
        });
	});

	$(".tweet-content").click(function() {
		var tweetContentSpan = $(this);
		var input = $("<textarea></textarea");
		input.text(tweetContentSpan.text().trim());

		tweetContentSpan.before(input);
		input.focus();

		var buttons = "<div class=\"text-right\">";
		buttons += "<button class=\"btn btn-primary modify-button\" type=\"button\">Modifier <span class=\"glyphicon glyphicon-ok\"></span></button>";
		buttons += " <button class=\"btn btn-default cancel-button\" type=\"button\">Annuler <span class=\"glyphicon glyphicon-remove\"></span></button>";
		buttons += "</div>";
		buttons = $(buttons);
		tweetContentSpan.before(buttons);

		tweetContentSpan.hide();

		var modifyButton = buttons.find(".modify-button");
		modifyButton.click(function() {
			// TODO modify content

			var tr = $(this).parents("tr");

			var id = getElementId(tr);

			if (input.val() != input.text()) {
				var myform = {	"tweetId" : id,
							"content" : input.val(),
							"userId" : $("#user_" + id).val(),
							"hash" : $("#hash_" + id).val()};

				$.post("do_modifyTweet.php", myform, function(data) {
					tweetContentSpan.text(myform.content);

					tr.find(".ask-for-modification").prev().remove();
					tr.find(".ask-for-modification").remove();
					tr.find(".reject-information").prev().remove();
					tr.find(".reject-information").remove();

					tr.find(".progress-bar-success, .progress-bar-info").css({"width" : "0%"});

					$("#hash_" + id).val(data.hash);

					tweetContentSpan.show();
					input.remove();
					buttons.remove();
				}, "json");
			}
			else {
				tweetContentSpan.show();
				input.remove();
				buttons.remove();
			}
		});

		var cancelButton = buttons.find(".cancel-button");
		cancelButton.click(function() {
			tweetContentSpan.show();
			input.remove();
			buttons.remove();
		});
	});
});