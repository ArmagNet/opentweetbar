<?php /*
	Copyright 2014 Cédric Levieux, Jérémy Collot, ArmagNet

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
$page = "about";
include_once("header.php");
?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><?php echo lang("breadcrumb_index"); ?></li>
		<li class="active"><?php echo lang("breadcrumb_about"); ?></li>
	</ol>

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo lang("about_what_s_opentweetbar_legend"); ?></div>
			<div class="panel-body"><?php echo lang("about_what_s_opentweetbar_content"); ?></div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading" id="helpus"><?php echo lang("about_help_us_legend"); ?></div>
			<div class="panel-body"><?php echo lang("about_help_us_content"); ?></div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo lang("about_need_help_legend"); ?></div>
			<div class="panel-body"><?php echo lang("about_need_help_content"); ?></div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading" id="contactus"><?php echo lang("about_contact_us_legend"); ?></div>
			<div class="panel-body"><?php echo lang("about_contact_us_content"); ?></div>
			<ul class="list-group">
				<li class="list-group-item"><a class="social grey twitter" href="https://www.twitter.com/@OpenTweetBar" target="_blank">@OpenTweetBar</a><span class="badge"><a class="color-inherit" href="https://www.twitter.com/@OpenTweetBar" target="_blank"><span class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a class="social grey e-mail" href="mailto://contact[@]opentweetbar[.]net" target="_blank">contact[@]opentweetbar[.]net</a><span class="badge"><a class="color-inherit" href="mailto://contact[@]opentweetbar[.]net" target="_blank"><span class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a href="https://flattr.com/submit/auto?user_id=armagnet_fai&url=https%3A%2F%2Fwww.opentweetbar.net%2F" target="_blank"><img src="//api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0"></a><span class="badge"><a class="color-inherit" href="https://flattr.com/submit/auto?user_id=armagnet_fai&url=https%3A%2F%2Fwww.opentweetbar.net%2F" target="_blank"><span class="glyphicon glyphicon-chevron-right"></span></a></span></li>
			</ul>
		</div>
	</div>

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading" id="releases"><?php echo lang("about_releases_legend"); ?></div>
			<div class="panel-body"><?php echo lang("about_releases_content"); ?></div>
			<ul class="list-group">
				<li class="list-group-item"><a href="https://github.com/ArmagNet/opentweetbar"
						target="_blank">Github Repository</a><span class="badge"><a class="color-inherit"
						href="https://github.com/ArmagNet/opentweetbar" target="_blank"><span
						class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a href="releases/OpenTweetBar.1.0.0.tar.gz"
						target="_blank">OpenTweetBar.1.0.0.tar.gz</a><span class="badge"><a class="color-inherit"
						href="releases/OpenTweetBar.1.0.0.tar.gz" target="_blank">c4e211ba2715ebd4a5ffe2607c226808 <span
						class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a href="releases/OpenTweetBar.1.0.0.zip"
						target="_blank">OpenTweetBar.1.0.0.zip</a><span class="badge"><a class="color-inherit"
						href="releases/OpenTweetBar.1.0.0.zip" target="_blank">5c9e5f8ccb00e77ec18e4b24c4e5c435 <span
						class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a href="releases/OpenTweetBar.0.9.9.tar.gz"
						target="_blank">OpenTweetBar.0.9.9.tar.gz</a><span class="badge"><a class="color-inherit"
						href="releases/OpenTweetBar.0.9.9.tar.gz" target="_blank">a086ab41354fb5e4051960f8012a9487 <span
						class="glyphicon glyphicon-chevron-right"></span></a></span></li>
				<li class="list-group-item"><a href="releases/OpenTweetBar.0.9.9.zip"
						target="_blank">OpenTweetBar.0.9.9.zip</a><span class="badge"><a class="color-inherit"
						href="releases/OpenTweetBar.0.9.9.zip" target="_blank">3307774bc0b4317b542c94e803d02875 <span
						class="glyphicon glyphicon-chevron-right"></span></a></span></li>
			</ul>
		</div>
	</div>
</div>

<div class="lastDiv"></div>

<?php include("footer.php");?>
<script>
$(function() {
	$(".panel").hover(function() {
					$(this).removeClass("panel-default");
					$(this).addClass("panel-success");
					$(this).find(".panel-body").addClass("text-success");
				}, function() {
					$(this).addClass("panel-default");
					$(this).removeClass("panel-success");
					$(this).find(".panel-body").removeClass("text-success");
				});
});
</script>
</body>
</html>