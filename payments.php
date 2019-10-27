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

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Payments</title>
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
				<div class="title">Payment History</div>
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
						if($customer->payments->number > 0){
						?>
						<div id="invoices">
							<div class="table-container">
					            <table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
						            <thead>
							            <tr>
                                            <th>Payment Date</th>
                                            <th>Summary</th>
                                            <!--<th>Invoice</th>-->

								            <th>Amount Paid</th>
							            </tr>
						            </thead>
						            <tbody>
									<?


									for($i=0;$i<sizeof($customer->payments->list);$i++){
										$payment = $customer->payments->list[$i];
										$core->console($payment);
                                    $currency = $core->currencies($payment->currency);
                                    // check if the associated invoice has been deleted
                                    if($payment->deleted === 0){
									?>
							            <tr class="unpaid">
                                            <td data-column="Payment Date"><?=date('l, F d, Y', $payment->paymentDate)?></td>
                                            <td data-column="Summary">
	                                            <?=strlen($payment->description) > 1 ? $payment->description : "No summary available"?>
                                            	<?=isset($payment->transactionId) ? "<em>(".$payment->transactionId.")</em>" : ""?>
                                            </td>
                                            <!--
                                            <td>
                                            <?
                                            if(isset($payment->invoice)){

                                                if(isset($payment->invoice->id) && $payment->invoice->id > 0){
	                                                if($payment->invoice->deleted === 0){
                                                    	echo "<a target='_blank' href='".INVOICES_URL."/index/".$payment->invoice->invoiceToken."'>Invoice #".$payment->invoice->id." &rsaquo;</a>";
                                                    } else {
	                                                    echo "Invoice #".$payment->invoice->id;                                                    }
                                                } else {
                                                    echo "";
                                                }
                                            } else {
                                                echo "&nbsp;";
                                            }
                                            ?>
                                            </td> -->

								            <td data-column="Amount"><?=$currency.number_format($payment->amountPaid, 2)?></td>
							            </tr>
                                    <?
                                    }
                                    }
                                    ?>
						            </tbody>
					            </table>
				            </div>
						</div>
						<? } else { ?>

						<div class="row">
							<div class="module">
					            <div class="none billing" style="margin-top:4em;">
					                <p>You have no payments available to view.</p>
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
