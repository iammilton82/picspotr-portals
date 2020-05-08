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
	<? include("include-tracking.php"); ?>
</head>

<? require("include-header.php") ?>

	<div class="row dash-wrapper">
		<div class="portal-container dash-container">

			<header id="main-header" class="row">
				<div class="back-button"><a href="/dashboard">Back</a></div>
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
