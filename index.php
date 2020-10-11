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
	<meta name="description" content="PicSpotr Customer Portals">
	<meta name="author" content="SitePoint">
	<link rel="stylesheet" href="https://use.typekit.net/qju2ojt.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP?>/assets/css/build.css?v=<?=VERSION?>" />
	<? include("include-tracking.php"); ?>
</head>

<body id="client-portal">
<div id="client-border" style="background-color: #<?=$assets->primaryColor?>"></div>
<?=ENVIRONMENT === 'development' ? "<div class='environment'>DEVELOPMENT</div>" : "" ?>
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

					// This is an example script - don't forget to change it!
					FS.identify(response.data.client.id, {
						displayName: response.data.client.fullName,
						email: response.data.client.emailAddress,
						userType: 'client'
					});

					window.location.href = "/dashboard";
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

<div id="ssl-certificate-image">
	<img src="https://www.trustlogo.com/images/install/positivessl_trust_seal_md_167x42.png" alt="Site is Protected Logo">
</div>

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
