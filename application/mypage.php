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
include_once("header.php");
require_once("engine/bo/UserBo.php");

$userBo = UserBo::newInstance(openConnection());

if ($userId) {
	$stats = $userBo->getStats($userId);
	$timeStats = $userBo->getTimeStats($userId);

//	print_r($timeStats);
}
// $dbuser = $userBo->get(SessionUtils::getUserId($_SESSION));

?>
<div class="container theme-showcase" role="main">
	<ol class="breadcrumb">
		<li><a href="index.php"><?php echo lang("breadcrumb_index"); ?> </a></li>
		<?php 	if ($user) {?>
		<li class="active"><?php echo $user; ?></li>
		<?php 	} else {?>
		<li class="active"><?php echo lang("breadcrumb_mypage"); ?></li>
		<?php 	}?>
	</ol>

	<div class="well well-sm">
		<p>
			<?php echo lang("mypage_guide"); ?>
		</p>
	</div>

	<?php 	if ($user) {?>

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo lang("mypage_tweets_legend"); ?>
			</div>
			<?php 	if (!count($stats)) {?>
			<div class="panel-body">
				<p>
					<?php echo lang("mypage_tweets_none"); ?>
				</p>
			</div>
			<?php 	}?>

			<?php 	if (count($stats)) { ?>
			<ul class="list-group">
				<?php
						foreach($stats as $stat) {?>
				<li class="list-group-item"><?php echo $stat["sna_name"] ?>
					<span class="badge"><?php echo $stat["sna_tweets"]?></span>
				</li>
				<?php 	}
				?>
			</ul>
			<?php	}	?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo lang("mypage_validations_legend"); ?>
			</div>
			<?php 	if (!count($stats)) {?>
			<div class="panel-body">
				<p>
					<?php echo lang("mypage_validations_none"); ?>
				</p>
			</div>
			<?php 	}?>

			<?php 	if (count($stats)) { ?>
			<ul class="list-group">
				<?php
						foreach($stats as $stat) { ?>

				<li class="list-group-item"><?php echo $stat["sna_name"] ?>
					<span class="badge"><?php echo $stat["sna_validations"]?>
						<span class="glyphicon glyphicon-arrow-right"></span>
					<?php echo $stat["sna_scores"]?></span>
				</li>
				<?php 	}
				?>
			</ul>
			<?php	}	?>
		</div>
	</div>

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo lang("mypage_tweet_and_validations_chart_legend"); ?>
			</div>
			<div class="panel-body" id="chartContainer" style="height: 300px; width: 100%; padding: 0px;">
			</div>
		</div>
	</div>

	<?php 	} else {
		include("connectButton.php");
	}?>

</div>

<div class="lastDiv"></div>

<?php include("footer.php");?>
<script type="text/javascript" src="js/canvasjs.min.js"></script>
<script>
$(function() {
    var chart = new CanvasJS.Chart("chartContainer",
    {
      title:{
/*        text: "Tweets et Validation dans le temps"*/
      },
      toolTip: {
        shared: true,
        content: function(e){
          var body;
          var head;
          var date = e.entries[0].dataPoint.x;
		  var printedDate = <?php echo lang("mypage_tweet_and_validations_chart_jsFormatDate"); ?>;

          head = "<span style = 'color:DodgerBlue; '><strong>@ "+ printedDate  + "</strong></span><br/>";

          body = "<span style= 'color:"+e.entries[0].dataSeries.color + "'> " + e.entries[0].dataSeries.name + "</span>: <strong>"+  e.entries[0].dataPoint.y + "</strong>";
          body +="<br/>";
          body +="<span style= 'color:"+e.entries[1].dataSeries.color + "'> " + e.entries[1].dataSeries.name + "</span>: <strong>"+  e.entries[1].dataPoint.y + "</strong>";
//          body +="<br/>";
//          body +="<span style= 'color:"+e.entries[2].dataSeries.color + "'> " + e.entries[2].dataSeries.name + "</span>: <strong>"+  e.entries[2].dataPoint.y + "</strong>";

          return (head.concat(body));
        }
      },
      axisY:{
        title: "<?php echo lang("mypage_tweet_and_validations_chart_axisY", false); ?>",
        includeZero: false,
        lineColor: "#369EAD"
      },
/*
      axisY2:{
          title: "<?php echo lang("mypage_score_chart_axisY", false); ?>",
          includeZero: false,
          lineColor: "#C24642"
        },
*/
      axisX: {
          title: "<?php echo lang("mypage_tweet_and_validations_chart_axisX"); ?>",
          valueFormatString: "<?php echo lang("mypage_tweet_and_validations_chart_formatDate"); ?>"
        },
      data: [
      {
        type: "spline",
        showInLegend: true,
        name: "<?php echo lang("mypage_tweets_legend"); ?>",
        dataPoints: [
<?php 	$separator = "";
		foreach($timeStats as $timeStat) {
			echo $separator;
			echo '{x: new Date(' . $timeStat['stat_timestamp'] . '000) , y: ' . $timeStat['twe_tweets'] . '}';
			$separator = ",\n";
		}?>
        ]
      },
      {
        type: "spline",
        showInLegend: true,
        name: "<?php echo lang("mypage_validations_legend"); ?>",
        dataPoints: [
<?php 	$separator = "";
		foreach($timeStats as $timeStat) {
			echo $separator;
			echo '{x: new Date(' . $timeStat['stat_timestamp'] . '000) , y: ' . $timeStat['tva_validations'] . '}';
			$separator = ",\n";
		}?>
        ]
      }
/*      ,
      {
          type: "spline",
          axisYType: "secondary"  ,
          showInLegend: true,
          name: "<?php echo lang("mypage_scores_legend"); ?>",
          dataPoints: [
<?php 	$separator = "";
  		foreach($timeStats as $timeStat) {
  			echo $separator;
  			echo '{x: new Date(' . $timeStat['stat_timestamp'] . '000) , y: ' . $timeStat['tva_scores'] . '}';
  			$separator = ",\n";
  		}?>
        ]
      }
      */
      ]
    });

	chart.render();
});
</script>
</body>
</html>
