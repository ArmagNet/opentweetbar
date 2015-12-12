$(function() {
	$("body").on("click", ".fork-button", function() {
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

	$("body").on("click", ".pagination li a", function(e) {
		e.preventDefault();

		var text = $(this).text();
		var page = -1;
		var currentPage = $(this).parents("nav").find("li.active").text();
		var length = $(this).parents("nav").find("li").length;

		if ($.isNumeric(text)) {
			page = text;
		}
		else if (text.indexOf("Previous") != -1) {
			page = currentPage - 1;
		}

		else if (text.indexOf("Next") != -1) {
			page = currentPage - (-1);
		}

		if (page < 1) page = 1;
		if (page > length - 2) page = length -2;

		var accountId = $(this).parents(".account").data("account-id");

//		console.log("Get page " + page + " for " + accountId);

		$.get("history.php", {page: page, accountId: accountId, numberPerPage: tweetPerPage}, function(data) {
			var newAccount = $(data).find(".account");
			var previous = $("#account-" + accountId);
			previous.before(newAccount);
			previous.remove();
			$.scrollTo(newAccount, 400);
		}, "html");
	});
});