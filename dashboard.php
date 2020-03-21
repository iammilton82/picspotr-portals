<?

require("./vendor/autoload.php");
require("core/constants.php");
require("core/core.php");

use Underscore\Underscore as _;

$core = new Core();
$p = new Portal();
$u = new User();



$portal = $p->getPortalBySubdomain();
$customer = $p->getCustomerOverviewByID($_COOKIE['user']);
$assets = $p->portalAssets($portal);
$customerName = $p->customerName($customer);

#$core->console($portal);
$core->console($customer);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Dashboard</title>
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

<? require("include-header.php") ?>

	<div class="row dash-wrapper">
		<div class="portal-container dash-container">

			<header id="main-header" class="row">
				<div class="title">Welcome, <?=$customerName?>.</div>
				<div class="actions">
					<div class="action-button">
						<a style="border-color: <?=$assets->primaryColor?>; color: <?=$assets->primaryColor?>;" href="logout.php">Logout</a>
					</div>
				</div>
			</header>

			<div id="page-alert" class="row">
				<?
				if($customer->invoices->number > 0){
					foreach($customer->invoices->list as $invoice){
						if($invoice->balance > 0 || $invoice->paid === 0){
				?>
				<div class="item error">
					<p>You have an unpaid balance for <strong><?=$invoice->name?></strong>. <a target="_blank" href="<?=INVOICES_URL."/index/".$invoice->invoiceToken?>">Make a payment &rsaquo;</a></p>
				</div>
				<?
						}
					}
				}
				?>

				<?
				if($customer->quotes->number > 0){
					foreach($customer->quotes->list as $quote){
						if($quote->accepted == 0){
				?>
				<div class="item error">
					<p>You have a quote that has not been accepted for <strong><?=$quote->name?></strong>. <a target="_blank" href="<?=QUOTES_URL."/index/".$quote->id?>">View the quote &rsaquo;</a></p>
				</div>
				<?
						}
					}
				}
				?>

				<?
				if($customer->events->number > 0){
					foreach($customer->events->list as $event){
						if($event->startDateTime > time()){
				?>
				<div class="item info">
					<p>You have an upcoming event on your calendar: <strong><?=$event->title?></strong>, <a href="events-details.php?eventId=<?=$event->id?>">click here for details &rsaquo;</a></p>
				</div>
				<? 		}
					}
				}
				?>

				<?
				if($customer->questionnaires->number > 0){

				?>
				<div class="item info">
					<p>You have a <strong>questionnaire</strong> that has not been completed, <a href="questionnaires.php">click here for details &rsaquo;</a></p>
				</div>
				<? } ?>

			</div>
			<div class="row">
				<div class="columns">


					<a href="questionnaires.php" class="column thirds card">
						<div class="callout"><?=$customer->questionnaires->number?></div>
						<div class="title">Questionnaire(s)</div>
						<div class="subtitle">Details about your upcoming shoot</div>
					</a>
					<a href="contracts.php" class="column thirds card">
						<div class="callout"><?=$customer->contracts->number?></div>
						<div class="title">Contracts</div>
						<div class="subtitle">Contracts, agreements &amp; releases</div>
					</a>

					<a href="quotes.php" class="column thirds card">
						<div class="callout"><?=$customer->quotes->number?></div>
						<div class="title">Quotes</div>
						<div class="subtitle">Your Quotes</div>
					</a>

					<a href="invoices.php" class="column thirds card">
						<div class="callout"><?=$customer->invoices->number?></div>
						<div class="title">Invoices</div>
						<div class="subtitle">Your Billing History</div>
					</a>
					<a href="galleries.php" class="column thirds card">
						<div class="callout"><?=$customer->galleries->number?></div>
						<div class="title">Photo Galleries</div>
						<div class="subtitle">Your Photos</div>
					</a>

					<a href="events.php" class="column thirds card">
						<div class="callout"><?=$customer->events->number?></div>
						<div class="title">Events</div>
						<div class="subtitle">Upcoming meetings, shoots &amp; events for you</div>
					</a>
					<a href="payments.php" class="column thirds card">
						<div class="callout"><?=$customer->payments->number?></div>
						<div class="title">Payments</div>
						<div class="subtitle">Your Payment History</div>
					</a>
					<a href="documents.php" class="column thirds card">
						<div class="callout"><?=$customer->documents !== false && sizeof($customer->documents) > 0 ?  sizeof($customer->documents) : 0?></div>
						<div class="title">Documents</div>
						<div class="subtitle">Available documents &amp; uploads</div>
					</a>

				</div>
			</div>
		</div>
	</div>

<? require("include-footer.php") ?>
