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

function getElementId(element) {
	var id = element.attr("id");

	if (id.lastIndexOf("_") != -1) {
		id = id.substring(id.lastIndexOf("_") + 1);
	}

	return id;
}

function deleteTweetUI(id) {
	$("#row_" + id).fadeOut(400, function() {
		var table = $("#row_" + id).parents("table");
		var nav = table.siblings("nav");
		var currentPage = nav.find("li.active").text();

		$("#row_" + id).remove();
		var badge = $("#validationMenuItem .badge");
		var value = badge.text() - 1;
		badge.text(value);
		if (value == 0) {
			badge.hide();
		}

		// TODO count last trs and remove the last page if needed
		if (currentPage) {
			showPage(table, currentPage);
		}

		if (table.find("tbody tr,.table .row.data").length == 0) {
			table.parents(".account").hide();
		}
	});
}

function updateTweetRow(tweetId) {
	$.get("validation.php", {}, function(data) {
		var newRow = $(data).find("#row_" + tweetId);

		var oldRow = $("#row_" + tweetId);
		oldRow.children().remove();
		oldRow.append(newRow.children());
		oldRow.find('[data-toggle="tooltip"]').tooltip();
		addListeners(oldRow);
	}, "html");
}

function addListeners() {
	$("body").on("click", ".test-button", function() {
		var tweetId = $(this).data("tweet-id");

		updateTweetRow(tweetId);
	});

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

	$("body").on("click", ".tweet-content", function() {
		var tweetContentSpan = $(this);
		var input = $("<textarea style='width: 100%;'></textarea");
		var numberOfLines = tweetContentSpan.html().match(/br/g);
		numberOfLines = numberOfLines ? numberOfLines.length + 1 : 1;
		input.text(tweetContentSpan.html().replace(/<br>/g, "\n").trim());

		input.attr("rows", numberOfLines)
		
		tweetContentSpan.before(input);
		input.focus();

		var buttons = "<div class=\"text-right\">";
		buttons += "<button class=\"btn btn-primary modify-button\" type=\"button\">Modifier <span class=\"glyphicon glyphicon-ok\"></span></button>";
		buttons += " <button class=\"btn btn-default cancel-button\" type=\"button\">Annuler <span class=\"glyphicon glyphicon-remove\"></span></button>";
		buttons += "</div>";
		buttons = $(buttons);
		tweetContentSpan.before(buttons);

		tweetContentSpan.hide();

		input.blur(function() {
			if (input.val() == input.text()) {
				tweetContentSpan.show();
				input.remove();
				buttons.remove();
			}
		});

		var modifyButton = buttons.find(".modify-button");
		modifyButton.click(function() {
			// modify content

			var tr = $(this).parents("tr,.row");

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

					updateTweetRow(id);
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

	$("body").on("click", ".validate-button", function() {
		var id = getElementId($(this));

		var myform = {	"tweetId" : id,
						"userId" : $("#user_" + id).val(),
						"hash" : $("#hash_" + id).val()};
		$.post("do_validateTweet.php", myform, function(data) {
			if (data.ok) {
				$("#row_" + id + " .progress-bar-success").css("width", data.score + "%");
				$("#row_" + id + " .progress").attr("title", data.total_score + " / " + data.validation_score);

				if (data.validated) {
					$("#okFinalValidateTweetAlert").show().delay(2000).fadeOut(1000);
					deleteTweetUI(id);
				}
				else {
					$("#okValidateTweetAlert").show().delay(2000).fadeOut(1000);
					$("#validate_" + id).fadeOut().remove();
					$("#reject_" + id).fadeOut().remove();
				}
			}
		}, "json");
	});

	$("body").on("click", ".delete-button", function() {
		var id = getElementId($(this));

		var myform = {	"tweetId" : id,
						"userId" : $("#user_" + id).val(),
						"hash" : $("#hash_" + id).val()};

		bootbox.setLocale("fr");
		bootbox.confirm("Voulez-vous vraiment supprimer ce tweet ?", function(result) {
			if (result) {
				$.post("do_deleteTweet.php", myform, function(data) {
					if (data.ok) {
						$("#okDeleteTweetAlert").show().delay(2000).fadeOut(1000);
						deleteTweetUI(id);
					}
				}, "json");
			}
		});
	});

	$("body").on("click", ".ask-for-modification-button", function() {
		var tr = $(this).parents("tr");

		var id = getElementId($(this));

		var myform = {	"tweetId" : id,
						"userId" : $("#user_" + id).val(),
						"hash" : $("#hash_" + id).val()};

		bootbox.setLocale("fr");
		bootbox.confirm("Voulez-vous demandez une modification sans la faire vous-même ?", function(result) {
			if (result) {

				$.post("do_askForModification.php", myform, function(data) {
					if (data.ok) {
						$("#okAskForModificationAlert").show().delay(2000).fadeOut(1000);

						var td = tr.find("td").eq(0);

						if (!td.find(".ask-for-modification").length) {
							td.append(
								$("*[data-template-id=template-ask-for-modification]").template("use", {data: {}}).children()
							);
						}
					}
				}, "json");
			}
		});
	});

	$("body").on("click", ".reject-button", function() {
		var id = getElementId($(this));

		bootbox.setLocale("fr");
		bootbox.prompt("Motivation du rejet (si modifier vous-même le tweet n'est pas suffisant) :", function(result) {
			if (result === null) {
			}
			else {
				var myform = {	"tweetId" : id,
						"userId" : $("#user_" + id).val(),
						"hash" : $("#hash_" + id).val(),
						"rejection" : true,
						"motivation" : result};

				$.post("do_validateTweet.php", myform, function(data) {
					if (data.ok) {
						$("#row_" + id + " .progress-bar-success").css("width", data.score + "%");
						$("#row_" + id + " .progress").attr("title", data.total_score + " / " + data.validation_score);

						var td = $("#row_" + id + " td").eq(0);

						td.append(
							$("*[data-template-id=template-reject-tweet]").template("use", {data: {
										"motivation" : myform["motivation"]
									}}).children()
						);

						$("#okRejectTweetAlert").show().delay(2000).fadeOut(1000);
						$("#validate_" + id).fadeOut().remove();
						$("#reject_" + id).fadeOut().remove();
						updateTweetRow(id);
					}
				}, "json");
			}
		});
	});
}

function updateValidations() {
	$.get("validation.php", {}, function(data) {

//		var newAccount = $(data).find(".account");
/*
		var previous = $(".account");
		previous.remove();

		$(".well").after(newAccount);

		newAccount.find('[data-toggle="tooltip"]').tooltip();
*/
		$(data).find(".nav-tabs li").each(function() {
			var accountId = $(this).data("account-id");
			var numberOfTweets = $(this).find("span.counter").text();
			
			$(".nav-tabs li a[href=#"+accountId+"] span.counter").text(numberOfTweets);
		});
		
		loadActive();
		
	}, "html");
}

$(function() {
	addListeners();
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