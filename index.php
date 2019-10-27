<?

require("./vendor/autoload.php");
require("core/constants.php");
require("core/core.php");

use Underscore\Underscore as _;

$core = new Core();

$p = new Portal();
$portal = $p->getPortalBySubdomain();

$assets = $p->portalAssets($portal);

#$core->console($portal);
#exit();

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

<? if($portal !== false  && SUBDOMAIN !== false){ ?>

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
						<div class="field">
							<input class="has-data" type="email" name="email" autocomplete="off" value="" required />
							<label for="email">Email Address<span class="required">*</span></label>
							<div role="alert">
								<div rel="email" class="message hide" message="required">Your email address is required</div>
							</div>
						</div>
						<div class="field">
							<input class="has-data" type="password" autocomplete="off" name="password" value="" ng-minlength="5" required />
							<label for="password">Password<span class="required">*</span></label>
							<div role="alert">
								<div rel="password" class="message hide" ng-message="required">A password is required</div>
							</div>
						</div>
						<div class="action-button">
							<button name="submit-portal-login" style="background-color: #<?=$assets->primaryColor?>">Sign In</button>
							<div role="alert">
								<div rel="system" class="message hide" message="required">A system error occurred and we could not login you. Try again later.</div>
								<div rel="credentials" class="message hide" message="required">The username and password combination you entered is incorrect. Try again.</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script src="assets/globals.js"></script>

<script type="text/javascript">
(function($){
	$("[name='loginForm']").on("submit", function(e){
		e.preventDefault();
		var form = $(this).serializeArray();
		var errors = 0;

		if(_.isUndefined(form[0].value) || _.isNull(form[0].value) || form[0].value.length < 5){
			$("[rel='email']").removeClass("hide");
			errors++;
		} else {
			$("[rel='email']").addClass("hide");
		}

		if(_.isUndefined(form[1].value) || _.isNull(form[1].value) || form[1].value.length < 5){
			$("[rel='password']").removeClass("hide");
			errors++;
		} else {
			$("[rel='password']").addClass("hide");
		}

		if(errors === 0){

			var credentials = {};
			credentials.emailAddress = form[0].value;
			credentials.password = form[1].value;
			credentials.photographerId = <?=$portal->photographerId?>;

			$.ajax({
				url: "<?=API?>/customer/login",
				method: "POST",
				data: credentials,
				dataType: 'json',
				async: false,
				crossDomain: true,
				headers: {
		            'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8',
		            'X-ACCESS_TOKEN' : '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
		            'TimezoneOffset' : new Date().getTimezoneOffset()
		        }
			}).done(function(response){
				if(response.status === 1 || response.status === true){
					console.log(response);

					Cookies.set('user', response.data.client.id);
					Cookies.set('photographer', response.data.photographer.id);
					Cookies.set('Timezone', response.data.photographer.timezone);
					Cookies.set('TimezoneOffset', new Date().getTimezoneOffset());

					__insp.push(['tagSession', { email: response.data.client.emailAddress, userid: response.data.client.id, fullName: response.data.client.fullName, type: 'client' }]);


					window.location.href = "dashboard.php";
				} else {
					$("[rel='credentials']").removeClass("hide");
				}
			}).fail(function(){
				$("[rel='system']").removeClass("hide");
			});


		}
	});

})(jQuery);
</script>


<? } else { ?>
<div class="row">
	<div class="module">
		<div class="none">
			<h1 class="error-404">This is embarassing!</h1>
			<p>We couldn't find this page. Please contact your photographer for the correct link.</p>
		</div>
	</div>
	<div id="powered-by"><img src="tmp/powered-by-picspotr.png" alt="Powered by PicSpotr" /></div>
</div>
<? } ?>


</body>
</html>
