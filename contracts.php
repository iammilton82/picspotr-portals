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

# $core->console($customer->contracts->list);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Contracts</title>
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
				<div class="title">Contracts &amp; Agreements</div>
				<div class="actions">
					<div class="action-button">
						<!--<a href="schedule-event.php">Schedule an Event</a>-->
					</div>
				</div>
			</header>

			<div class="row">
				<div id="agreements" class="container" style="padding-top:0;">
			        <div class="row">
						<?
						if($customer->contracts->number > 0){
						?>
						<div class="table-container">
				            <table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
					            <thead>
						            <tr>
										<th>Title</th>
										<th>Effective Date</th>
							            <th>Status</th>
						            </tr>
					            </thead>
					            <tbody>
									<?
									foreach($customer->contracts->list as $contracts){
										$status = $core->agreementStatus($contracts);
									?>
						            <tr>
							            <td class="main" data-column="Title">
								            <a target="_blank" href="<?=CONTRACTS_URL."/?id=".$contracts->id."&cid=".$_COOKIE['user']."&x=62608e08adc29a8d6dbc9754e659f125"?>"><strong><?=strlen($contracts->title)>1 ? $contracts->title : "No contract title provided"?></strong></a>
										</td>
										<td data-column="Effective Date"><?=date('l, F d, Y', $contracts->effectiveDate)?></td>
							            <td data-column="Status">
								            <span class="status <?=$status->status?>"><span><?=$status->text?></span></span>
							            </td>
									</tr>
									<?
									}
									?>
					            </tbody>
				            </table>
			            </div>
						<?
						} else {
						?>
						<div class="row">
							<div class="module">
					            <div class="none agreements" style="margin-top: 4em;">
					                <p>You have no contracts available to complete.</p>
					            </div>
							</div>
						</div>
						<?
						}
						?>
			        </div>
			    </div>
			</div>
		</div>
	</div>

<? require("include-footer.php") ?>
