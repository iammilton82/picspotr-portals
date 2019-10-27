<?
use Underscore\Underscore as _;
?>
<body id="client-portal">
	<div id="client-border" style="background-color: #<?=$assets->primaryColor?>"></div>
	<div class="row">
		<div id="logo-container">
		<? 
		if(_::isNull($portal->logo) || $portal->logo === 'no-logo.png'){
			echo "<h1 style='text-align:center; line-height: 1em;'>".$assets->studioName."</h1>";
		} else {
			echo "<div id='logo'><a href='dashboard.php'><img src='".AWS."/profiles/".$portal->logo."' alt='".$assets->studioName." Logo' /></a></div>";
		}
		?>
		</div>
	</div>