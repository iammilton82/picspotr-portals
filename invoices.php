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
	<? include("include-tracking.php"); ?>
</head>

<? require("include-header.php") ?>

	<div class="row dash-wrapper">
		<div class="portal-container dash-container">

			<header id="main-header" class="row">
				<div class="back-button"><a href="/dashboard">Back</a></div>
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
