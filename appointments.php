<?

require("./vendor/autoload.php");
require("core/constants.php");
require("core/core.php");

use Underscore\Underscore as _;

$core = new Core();
$p = new Portal();
$u = new User();

$portal = $p->getPortalBySubdomain();
$assets = $p->portalAssets($portal);
if($_COOKIE['user']){
    $customer = $p->getCustomerOverviewByID($_COOKIE['user']);
    $customerName = $p->customerName($customer);
}

$appointments = $p->getAvailableAppointments($portal->photographerId);
if($appointments && sizeof($appointments)>0){
    $byDates = _::groupBy($appointments, 'recurringId');
    $showAppointments = true;

} else {
    $showAppointments = false;
}





?>
<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 10]>
	<script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" data-slave="http://api.picspotr.com/proxy.html"></script>
	<![endif]-->
	<meta charset="utf-8">
	<title>Schedule an Appointment</title>
	<meta name="description" content="The HTML5 Herald">
	<meta name="author" content="SitePoint">
	<link rel="stylesheet" href="https://use.typekit.net/qju2ojt.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP?>/assets/css/build.css?v=<?=VERSION?>" />
	<? include("include-tracking.php"); ?>
</head>

<? require("include-header.php") ?>

<div class="row dash-wrapper">
    <div class="portal-container dash-container">

        <? if($_COOKIE['user']){ ?>
        <header id="main-header" class="row">
            <div class="back-button"><a href="/dashboard">Back</a></div>
            <div class="title">Schedule an Appointment</div>
            <div class="actions">
                <div class="action-button">
                    
                </div>
            </div>
        </header>
        <? } ?>

        <div class="module full section-container">
            <? if($showAppointments === true){ ?>
            <p class="centered">Welcome to my scheduling page. Select an event type below to view my availability.</p>
            <div>
                <ul class="ps-card-layout">
                    <? foreach($byDates as $date){ 
                        $numAvailable = sizeof(_::where($date[0]->availability, ["status" => "free", "bookable" => null]));

                        ?>
                    <li>
                        <div class="padded">
                            <? if($numAvailable > 0){ ?>
                                <h3 class="summary"><a href="/book?id=<?=$date[0]->recurringId?>"><?=$date[0]->title?></a></h3>
                                <div class="description"><strong><?=sizeof($date[0]->availability)?></strong> appointments available starting on <?=$date[0]->availability[0]->startDate?></div>
                            <? } else { ?>
                                <h3 class="summary"><?=$date[0]->title?></h3>
                                There are no appointments available.
                            <? } ?>
                        </div>
                    </li>
                    <? } ?>
                </ul>
            </div>
            <? } ?> 
            
            <? if($showAppointments === false){ ?>
            <div>
                <section>
                    <div class="none appointments">
                        <p>There are no available times that you can book at this time.</p>
                    </div>
                </section>
            </div>
            <? } ?>
        </div>


    </div>
</div>

<? require("include-footer.php") ?>