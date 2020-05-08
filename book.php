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

$recurringId = $_GET['id'];

if($recurringId){
    $appointments = $p->getAvailableTimeSlots($recurringId);
    if($appointments && sizeof($appointments)>0){
        $data = new stdClass();
        $data->info = $appointments[0];
        $data->dates = _::groupBy($appointments, 'startDate');
        $data->hasAddress = $p->hasLocationAddress($data->info);
        $data->portal = $portal;
        if($data->portal->country !== 'USA'){
            $data->dateFormat = 'l, d F Y';
        } else {
            $data->dateFormat = 'l, F d, Y';
        }
        $showAppointments = true;
        $core->console($data);
    } else {
        $showAppointments = false;
    }
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
	<title>Book an Appointment</title>
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
            <div class="back-button"><a href="/appointments">Back</a></div>
            <div class="title">Schedule an Appointment</div>
            <div class="actions">
                <div class="action-button">
                    
                </div>
            </div>
        </header>
        <? } ?>

        <div class="module full section-container">
        
        <? if($showAppointments === true){ ?>
        <div id="events">
            <div id="event-detail" class="module">
                <div class="row">
                    <div class="columns">
                        <div class="column halfs">
                            <div class="padded">
                                <div class="big-card">
                                    <h1 class="title"><?=$data->info->title?></h1>
                                    <ul>
                                        <li id="duration" class="row">
                                            <i class="far fa-stopwatch"></i> <strong><?=$data->info->duration." ".$data->info->durationType?></strong>
                                        </li>

                                        <? if($data->info->description){ ?>
                                        <li class="description">
                                            <?=$data->info->description?>
                                        </li>
                                        <? } ?>

                                        <? if($data->hasAddress){ ?>
                                        <li>
                                            <? if(strlen($data->info->location)>0){?>
                                            <div><strong><?=$data->info->location?></strong></div>
                                            <? } ?>
                                            <? if(strlen($data->info->address1)>0){?>
                                            <div><?=$data->info->address1?></div>
                                            <? } ?>
                                            <? if(strlen($data->info->address2)>0){?>
                                            <div><?=$data->info->address2?></div>
                                            <? } ?>
                                            <? if(strlen($data->info->city)>0 || strlen($data->info->state)>0){?>
                                            <div><?=strlen($data->info->city)>0 ? $data->info->city : ""?>
                                            <?=strlen($data->info->state)>0 ? $data->info->state : ""?>
                                            <?=strlen($data->info->zipCode)>0 ? $data->info->zipCode : ""?>
                                            </div>
                                            <? } ?>
                                        </li>
                                        <? } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="column halfs">
                            <div class="padded">
                                <? if(sizeof($data->dates)>0){ ?>
                                <p>Select an available date and time</p>
                                
                                <? 
                                foreach($data->dates as $date){
                                ?>
                                <div class="row">
                                    <h2><?=date($data->dateFormat, strtotime($date[0]->startDate))?></h2>
                                    <ul class="ps-card-layout">
                                        <? foreach($date as $d){ 
                                            if($d->status === 'free'){ ?>
                                        <li>
                                            <div class="padded">
                                                <h3 class="summary"><a href="/reserve?i=<?=$d->id?>&r=<?=$d->recurringId?>"><?=$d->startTime?></a></h3>
                                            </div>
                                        </li>
                                        <? } } ?>
                                    </ul>
                                </div>
                                <?
                                } 
                                ?>
                                
                                <? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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