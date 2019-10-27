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
	<!-- Begin Inspectlet Asynchronous Code -->
	<script type="text/javascript">
	(function() {
	var insp_ab_loader = true; // set to false to disable A/B optimized loader
	window.__insp = window.__insp || [];
	__insp.push(['wid', 1270111355]);
	var ldinsp = function(){
	if(typeof window.__inspld != "undefined") return; window.__inspld = 1; var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js?wid=1270111355&r=' + Math.floor(new Date().getTime()/3600000); var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x);if(typeof insp_ab_loader != "undefined" && insp_ab_loader){ var adlt = function(){ var e = document.getElementById('insp_abl'); if(e){ e.parentNode.removeChild(e); __insp.push(['ab_timeout']); }}; var adlc = "body{ visibility: hidden !important; }"; var adln = typeof insp_ab_loader_t != "undefined" ? insp_ab_loader_t : 800; insp.onerror = adlt; var abti = setTimeout(adlt, adln); window.__insp_abt = abti; var abl = document.createElement('style'); abl.id = "insp_abl"; abl.type = "text/css"; if(abl.styleSheet) abl.styleSheet.cssText = adlc; else abl.appendChild(document.createTextNode(adlc)); document.head.appendChild(abl); } };
	setTimeout(ldinsp, 0);
	})();
	</script>
	<!-- End Inspectlet Asynchronous Code -->
	<!-- Hotjar Tracking Code for www.picspotr.com -->
		<script>
		    (function(h,o,t,j,a,r){
		        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
		        h._hjSettings={hjid:<?=HOTJAR_ID?>,hjsv:<?=HOTJAR_VERSION?>};
		        a=o.getElementsByTagName('head')[0];
		        r=o.createElement('script');r.async=1;
		        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
		        a.appendChild(r);
		    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
		</script>
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
							<p>Logged out accidentally? To sign into your account, <a href="index.php">click here &rsaquo;</a></p>
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
	<div id="powered-by"><img src="tmp/powered-by-picspotr.png" alt="Powered by PicSpotr" /></div>
</div>
<? } ?>

</body>
</html>
