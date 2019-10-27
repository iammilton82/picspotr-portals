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
#$core->console($customer->invoices);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Invoices</title>
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
				<div class="back-button"><a href="dashboard.php">Back</a></div>
				<div class="title">Invoices</div>
				<div class="actions">
					<div class="action-button">
						<!--<a href="schedule-event.php">Schedule an Event</a>-->
					</div>
				</div>
			</header>

			<div class="row">
				<div id="billing" class="container" style="padding-top:0;">
			        <div class="row">
						<?
						if($customer->invoices->number > 0){
						?>
						<div id="invoices">
							<div class="table-container">
					            <table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
						            <thead>
							            <tr>
								            <th>&nbsp;</th>
								            <th>Invoice #</th>
								            <th>Name</th>
								            <th>Due Date</th>
								            <th>Balance</th>
							            </tr>
						            </thead>
						            <tbody>
									<?
									foreach($customer->invoices->list as $invoice){
									$currency = $core->currencies($invoice->currency);
									?>
							            <tr class="unpaid">
								            <td class="has-status">
									            <span class="status <?=$core->invoiceStatus($invoice)?>"><span><?=$core->invoiceStatus($invoice)?></span></span>
								            </td>
								            <td data-column="Invoice #"><a target="_blank" href="<?=INVOICES_URL?>/index/<?=$invoice->invoiceToken?>"><?=$invoice->id?> &rsaquo;</a></td>
								            <td class="main" data-column="Invoice">
												<strong><?=$invoice->name?></strong>
											</td>
								            <td data-column="Due Date"><?=date('l, F d, Y', $invoice->dueDate)?></td>
								            <td data-column="Balance"><?=$currency.number_format($invoice->balance, 2)?></td>
							            </tr>
									<? } ?>
						            </tbody>
					            </table>
				            </div>
						</div>
						<? } else { ?>

						<div class="row">
							<div class="module">
					            <div class="none billing" style="margin-top:4em;">
					                <p>You have no invoices available to view.</p>
					            </div>
							</div>
						</div>

						<? } ?>
			        </div>
			    </div>
			</div>
		</div>
	</div>

<? require("include-footer.php") ?>
