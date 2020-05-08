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
# $core->console($customer);

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
	<? include("include-tracking.php"); ?>
</head>

<? require("include-header.php") ?>

	<div class="row dash-wrapper">
		<div class="portal-container dash-container">

			<header id="main-header" class="row">
				<div class="title">Welcome, <?=$customerName?>.</div>
				<div class="actions">
					<div class="action-button">
						<a style="border-color: <?=$assets->primaryColor?>; color: <?=$assets->primaryColor?>;" href="/logout">Logout</a>
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
					<p>You have an upcoming event on your calendar: <strong><?=$event->title?></strong>, <a href="/events-details?eventId=<?=$event->id?>">click here for details &rsaquo;</a></p>
				</div>
				<? 		}
					}
				}
				?>

				<?
				if($customer->questionnaires->number > 0){

				?>
				<div class="item info">
					<p>You have a <strong>questionnaire</strong> that has not been completed, <a href="/questionnaires">click here for details &rsaquo;</a></p>
				</div>
				<? } ?>

			</div>
			<div class="row">
				<div class="columns">


					<a href="/questionnaires" class="column thirds card">
						<div class="callout"><?=$customer->questionnaires->number?></div>
						<div class="title">Questionnaire(s)</div>
						<div class="subtitle">Details about your upcoming shoot</div>
					</a>
					<a href="/contracts" class="column thirds card">
						<div class="callout"><?=$customer->contracts->number?></div>
						<div class="title">Contracts</div>
						<div class="subtitle">Contracts, agreements &amp; releases</div>
					</a>

					<a href="/quotes" class="column thirds card">
						<div class="callout"><?=$customer->quotes->number?></div>
						<div class="title">Quotes</div>
						<div class="subtitle">Your Quotes</div>
					</a>

					<a href="/invoices" class="column thirds card">
						<div class="callout"><?=$customer->invoices->number?></div>
						<div class="title">Invoices</div>
						<div class="subtitle">Your Billing History</div>
					</a>
					<a href="/galleries" class="column thirds card">
						<div class="callout"><?=$customer->galleries->number?></div>
						<div class="title">Photo Galleries</div>
						<div class="subtitle">Your Photos</div>
					</a>

					<a href="/events" class="column thirds card">
						<div class="callout"><?=$customer->events->number?></div>
						<div class="title">Events</div>
						<div class="subtitle">Upcoming meetings, shoots &amp; events for you</div>
					</a>
					<a href="/payments" class="column thirds card">
						<div class="callout"><?=$customer->payments->number?></div>
						<div class="title">Payments</div>
						<div class="subtitle">Your Payment History</div>
					</a>
					<a href="/documents" class="column thirds card">
						<div class="callout"><?=$customer->documents !== false && sizeof($customer->documents) > 0 ?  sizeof($customer->documents) : 0?></div>
						<div class="title">Documents</div>
						<div class="subtitle">Available documents &amp; uploads</div>
					</a>

				</div>
			</div>
		</div>
	</div>

<? require("include-footer.php") ?>
