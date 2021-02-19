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

$appointments = $p->getAvailableAppointments($_COOKIE['photographer']);

$u->checkAuth($customer);

?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Events</title>
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
				<div class="title">Calendar</div>
				<div class="actions">
					<div class="action-button">
						<? if($portal->calendly !== false){ ?>
						<a target="_blank" href="<?=$portal->calendly->calendlyURL?>">Schedule on Calendly</a>
						<? } ?>
						<? if($appointments && sizeof($appointments)>0){ ?>
						<a href="/appointments">Schedule an Appointment</a>
						<? } ?>
					</div>
				</div>
			</header>

			<div class="row">
				<div id="events" class="container" style="padding-top:0;">
			        <div class="row">
						<?
						if($customer->events->number > 0){
						?>
						<div id="card-carousel" class="event-cards">
							<?
							foreach($customer->events->list as $event){
								$month = date('M', strtotime($event->startDate1));
								$day = date('d', strtotime($event->startDate1));
								if($event->allDay === 1 || $event->allDay === true){
									$time = "All Day";
								} else {
									$time = date("g:i A", $event->startDateTime);
								}
							?>
							<!-- repeat event -->
							<div class="row alt">
								<div class="row alt">
									<div class="item row">
										<a href="/events-details?eventId=<?=$event->id?>" class="card-container" ng-class="{today : todaysDate === event.pickerStart}">
											<div class="pre-date">
												<div class="md">
													<div class="month"><?=$month?></div>
													<div class="day"><?=$day?></div>
												</div>
											</div>
											<div class="columns">
												<div class="column fours event-name">
													<div class="event-title"><?=$event->title?></div>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
							<!-- // repeat event -->
							<?
							}
							?>
						</div>
						<? } else { ?>
						<div class="row">
							<div class="module">
					            <div class="none events" style="margin-top: 4em;">
					                <p>You do not have any scheduled shoots or events.</p>
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
