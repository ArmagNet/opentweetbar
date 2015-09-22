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
});