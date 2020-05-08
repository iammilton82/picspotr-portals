<?
use Underscore\Underscore as _;
?>
<body id="client-portal">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MHS8SBK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

	<div id="client-border" style="background-color: #<?=$assets->primaryColor?>"></div>
	<div class="row">
		<div id="logo-container">
		<? 
		if(_::isNull($portal->logo) || $portal->logo === 'no-logo.png'){
			echo "<h1 style='text-align:center; line-height: 1em;'>".$assets->studioName."</h1>";
		} else {
			echo "<div id='logo'>";
			if($_COOKIE['user']){
				echo "<a href='/dashboard'>";
				echo "<img src='".AWS."/profiles/".$portal->logo."' alt='".$assets->studioName." Logo' />";
				echo "</a>";
			} else {
				echo "<img src='".AWS."/profiles/".$portal->logo."' alt='".$assets->studioName." Logo' />";
			}
			echo "</div>";
		}
		?>
		</div>
	</div>