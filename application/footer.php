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
?>
<nav class="navbar navbar-inverse navbar-bottom" role="navigation">

	<ul class="nav navbar-nav">
		<li <?php if ($page == "about") echo 'class="active"'; ?>><a href="about.php"><?php echo lang("about_footer"); ?></a></li>
		<li><a href="https://flattr.com/submit/auto?user_id=armagnet_fai&url=https%3A%2F%2Fwww.opentweetbar.net%2F" target="_blank"><img src="//api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0"></a></li>
	</ul>
	<p class="navbar-text pull-right"><?php echo lang("opentweetbar_footer"); ?></p>
</nav>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/bootstrap-datetimepicker.js"></script>
<script src="js/ekko-lightbox.min.js"></script>
<script type="text/javascript">
$(function() {
	$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
	    event.preventDefault();
	    $(this).ekkoLightbox();
	});
});
</script>
<script src="js/user.js"></script>
<script src="js/window.js"></script>
<script src="js/pagination.js"></script>
<?php
if (is_file("js/perpage/" . $page . ".js")) {
	echo "<script src=\"js/perpage/" . $page . ".js\"></script>\n";
}
?>
</body>
</html>