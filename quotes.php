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

# $core->console($portal);
$core->console($customer->quotes);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Quotes</title>
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
				<div class="title">Quotes</div>
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
						if($customer->quotes->number > 0){
						?>
						<div id="invoices">
							<div class="table-container">
					            <table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
						            <thead>
							            <tr>
								            <th>Name</th>
								            <th>Expiration Date</th>
								            <th>Status</th>
							            </tr>
						            </thead>
						            <tbody>
									<?
									foreach($customer->quotes->list as $quote){
									$currency = $core->currencies($quote->currency);
									?>
							            <tr class="unpaid">
								            <td class="main" data-column="Invoice">
												<strong><a target="_blank" href="<?=QUOTES_URL?>/index/<?=$quote->id?>"><?=$quote->name?> &rsaquo;</a></strong>
											</td>
								            <td data-column="Due Date"><?=date('l, F d, Y', $quote->expirationDate)?></td>
								            <td data-column="Status">
									            <?=($quote->accepted == 0 ? 'Not accepted' : 'Accepted')?>
								            </td>
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
					                <p>You have no quotes available to view.</p>
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
