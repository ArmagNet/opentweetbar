function forgottenResponseHandler(data) {
	$("#formPanel").hide();
	$("#successPanel").show();
}

$(function() {
	$('#forgottenButton').click(function (e) {
		e.preventDefault();

		var myform = 	{
							mail: $("#mailInput").val()
						};

		$.post("do_forgotten.php", myform, forgottenResponseHandler, "json");
	});
});
