<?

require("./vendor/autoload.php");
require("core/constants.php");
require("core/core.php");

use Underscore\Underscore as _;

$core = new Core();

$p = new Portal();
$portal = $p->getPortalBySubdomain();

$assets = $p->portalAssets($portal);

unset($_COOKIE['user']);
unset($_COOKIE['photographer']);

setcookie('user', '', time() - 3600);
setcookie('photographer', '', time() - 3600);
setcookie('TimezoneOffset', '', time() - 3600);
setcookie('Timezone', '', time() - 3600);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Client Studio</title>
	<meta name="description" content="The HTML5 Herald">
	<meta name="author" content="SitePoint">
	<link rel="stylesheet" href="https://use.typekit.net/qju2ojt.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP?>/assets/css/build.css?v=<?=VERSION?>" />
	<? include("include-tracking.php"); ?>
</head>

<body id="client-portal">
<div id="client-border" style="background-color: #<?=$assets->primaryColor?>"></div>

<? if(SUBDOMAIN !== false){ ?>

<div class="row">
	<div id="main-engagement" style="<?=$assets->background?>">
		<section id="auth">

			<div class="container">
				<div id="auth-wrapper">
					<form name="loginForm" method="post">
					<?
					if(_::isNull($portal->logo) || $portal->logo === 'no-logo.png'){
						echo "<h1 style='text-align:center; line-height: 1em;'>".$assets->studioName."</h1>";
					} else {
						echo "<div id='logo'><img src='".AWS."/profiles/".$portal->logo."' alt='".$assets->studioName." Logo' /></div>";
					}
					?>
					</form>
					<div id="page-alert" class="row">
						<div class="item success" style="text-align: center; background-color: #<?=$assets->primaryColor?>; border-color: #<?=$assets->primaryColor?>;">
							<h3 style="margin-bottom: 1em;">You have logged out successfully.</h3>
							<p>Logged out accidentally? To sign into your account, <a href="/">click here &rsaquo;</a></p>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

</div>

<? } else { ?>
<div class="row">
	<div class="module">
		<div class="none">
			<h1 class="error-404">This is embarrassing!</h1>
			<p>We couldn't find this page. Please contact your photographer for the correct link.</p>
		</div>
	</div>
</div>
<? } ?>

</body>
</html>
