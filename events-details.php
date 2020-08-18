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

$event = $p->getEventDetails($_GET['eventId']);
$myEvent = $p->eventHasMyCustomerId($event, $_COOKIE['user']);
$hasAddress = $p->hasLocationAddress($event);

$core->console($event);

date_default_timezone_set($portal->timezone);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Event Details</title>
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
				<div class="back-button"><a href="/events">Back</a></div>
				<div class="title">Event Details</div>
				<div class="actions">
					<div class="action-button">
						<!-- <a href="schedule-event.php">Schedule an Event</a> -->
					</div>
				</div>
			</header>

			<div class="row">
				<div id="events" class="container" style="padding-top:0;">
			        <div id="event-detail" class="module full">
						<? if($event !== false && $myEvent === true){ ?>
		                <div id="event-container">

		                    <section id="event-description">
								<h1><?=strlen($event->title)>0 ? $event->title : "No title provided" ?></ h1>
		                    </section>

		                    <section id="event-info">

		                        <div class="row section">
			                        <div class="columns">
										<div class="column halfs">
				                            <div class="padded">
				                                <span class="content-label">Location</span>
												<? if($hasAddress === true){ ?>
												<? if(strlen($event->location)>0){?>
				                                <div><strong><?=$event->location?></strong></div>
												<? } ?>
												<? if(strlen($event->address1)>0){?>
												<div><?=$event->address1?></div>
												<? } ?>
												<? if(strlen($event->address2)>0){?>
												<div><?=$event->address2?></div>
												<? } ?>
												<? if(strlen($event->city)>0 || strlen($event->state)>0){?>
												<div><?=strlen($event->city)>0 ? $event->city : ""?>
												<?=strlen($event->state)>0 ? $event->state : ""?>
												<?=strlen($event->zipCode)>0 ? $event->zipCode : ""?>
												</div>
												<? } ?>
												<? } else { ?>
												<div>No address available for this event.</div>
												<? } ?>
				                            </div>
			                            </div>

			                            <div class="column halfs">
				                            <div class="padded">
				                                <span class="content-label">Date</span>
												<?=$p->returnEventTime($event)?>

												
				                            </div>
			                            </div>

			                        </div>

		                        </div>

								<!-- CONTRACTS -->
								<div class="row section">
									<div class="padded">
										<div class="row relative">
											<span class="content-label">Contracts &amp; Agreements</span>
										</div>
										<div class="row">
											<div id="agreements" class="container" style="padding-top:0;">
												<div class="row">
													<?
													if(sizeof($event->agreements) > 0){
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
																foreach($event->agreements as $contracts){
																?>
																<tr>
																	<td class="main" data-column="Title">
																		<a target="_blank" href="<?=CONTRACTS_URL."/?id=".$contracts->id."&cid=".$_COOKIE['user']."&x=62608e08adc29a8d6dbc9754e659f125"?>"><strong><?=strlen($contracts->title)>1 ? $contracts->title : "No contract title provided"?></strong></a>
																	</td>
																	<td data-column="Effective Date"><?=date('l, F d, Y', $contracts->effectiveDate)?></td>
																	<td data-column="Status">
																		<span class="status executed"><span>Signed</span></span>
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
																<p>You have no contracts for this event.</p>
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

								<!-- end contracts -->
								
								<!-- START INVOICES -->
								<div class="row section">
									<div class="padded">
										<div class="row relative">
											<span class="content-label">Invoices</span>
										</div>
										<div class="row">
											<div id="billing" class="container" style="padding-top:0;">
												<div class="row">
													<?
													if($event->invoices && sizeof($event->invoices) > 0){
													?>

													<div class="table-container">
														<table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
															<thead>
																<tr>
																	<th>Status</th>
																	<th>Name</th>
																	<th>Due Date</th>
																	<th>Balance</th>
																</tr>
															</thead>
															<tbody>
															<?
															foreach($event->invoices as $invoice){
															$currency = $core->currencies($invoice->currency);
															?>
																<tr class="unpaid">
																	<td class="has-status">
																		<span class="status <?=$core->invoiceStatus($invoice)?>"><span><?=$core->invoiceStatus($invoice)?></span></span>
																	</td>
																	<td class="main" data-column="Invoice">
																		<strong><a target="_blank" href="<?=INVOICES_URL?>/index/<?=$invoice->invoiceToken?>"><?=$invoice->name?></a> </strong>
																	</td>
																	<td data-column="Due Date"><?=date('F d, Y', $invoice->dueDate)?></td>
																	<td data-column="Balance"><?=$currency.number_format($invoice->balance, 2)?></td>
																</tr>
															<? } ?>
															</tbody>
														</table>
													</div>

													<? } else { ?>

													<div class="row">
														<div class="module">
															<div class="none events">
																<p>You have no invoices available for this event.</p>
															</div>
														</div>
													</div>

													<? } ?>
												</div>
											</div>
										</div>


									</div>
								</div>
								<!-- end invoices -->

		                        <!-- questionnaires -->
		                        <div class="row section">
			                        <div class="padded">
					                        <div class="padded">
						                        <div class="row relative">
							                        <span class="content-label">Questionnaires</span>
						                        </div>

												<?
												if(sizeof($event->questionnaires) > 0){
												?>
												<div>
													<table class="ps-table ps-table-clean" width="100%" cellpadding="0" cellspacing="0">
											            <tbody>
															<? foreach($event->questionnaires as $questionnaire){ 
																$link = APP."/#/portal/questionnaires/respond/".$questionnaire->token;
																?>
												            <tr>
													            <td class="main" style="padding-left:0;">
														            <a href="<?=$link?>">
																		<?=strlen($questionnaire->title)>0 ? $questionnaire->title : "Questionnaire #".$questionnaire->id?>
																	</a>
																</td>
													            <td>
																	<?=_::isNull($questionnaire->dateCompleted) ? "<span class='status unpaid'>Incomplete</span>" : "<span class='executed'>Completed</span>";?>
													            </td>
												            </tr>
															<? } ?>
											            </tbody>
										            </table>
												</div>
												<? } else { ?>
						                        <div class="module">
							                        <section style="margin-top: 4em;">
								                        <div class="none questionnaires">
															<p>You have no questionnaires for this event.</p>
								                        </div>
							                        </section>
						                        </div>
												<? } ?>

					                        </div>
			                        </div>
		                        </div>

		                        <!-- end questionnaires -->


		                    </section>
		                </div>

						<? } else { ?>
						<div class="row">
							<div class="module">
					            <div class="none events" style="margin-top: 4em;">
					                <p>The event you are looking for cannot be found.  Please contact your photographer for details.</p>
					            </div>
							</div>
						</div>
						<? } ?>
		                <!-- show if there are no events -->

		            </div>
			    </div>
			</div>
		</div>
	</div>

<? require("include-footer.php") ?>
