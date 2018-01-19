/*
	Copyright 2014-2018 Cédric Levieux, ArmagNet

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
		
/* global $ */

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
			newAccount.find('[data-toggle="tooltip"]').tooltip();
			$.scrollTo(newAccount, 400);
		}, "html");
	});
});

function loadGroup(group, div, from) {
//    console.log(group);
    
    $.get("", {id: group}, function(data) {
        var previousChildren = div.children();
        var children = $(data).find("#" + group).children();
        
        div.append(children);
        previousChildren.remove();
        
    	$("table,.table").each(function() {
    		showPage($(this), 1);
    	});
    }, "html");
}

function loadActive() {
    $('li.active a[data-toggle="tab"]').each(function () {
        var newlyGroup = $(this).attr("href");
        var newlyDiv = $(newlyGroup);

        loadGroup(newlyGroup.replace("#", ""), newlyDiv, 0);
    });
}

$(function() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // console.log(e.target); // newly activated tab
        // console.log(e.relatedTarget); // previous active tab
        
        var newlyGroup = $(e.target).attr("href");
        
        var newlyDiv = $(newlyGroup);
        var previousDiv = $($(e.relatedTarget).attr("href"));
        
        loadGroup(newlyGroup.replace("#", ""), newlyDiv, 0);
    });

    loadActive();
});