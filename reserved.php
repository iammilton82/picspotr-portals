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


$recurringId = $_GET['r'];
$id = $_GET['i'];

if($recurringId){
    $appointments = $p->getAnySlotById($id);
    if($appointments && sizeof($appointments)>0){
        $data = new stdClass();
        $data->info = $appointments[0];
        $data->hasAddress = $p->hasLocationAddress($data->info);
        $data->portal = $portal;
        $data->hasCustomer = $_COOKIE['user'] ? true : false;
        if($data->portal->country !== 'USA'){
            $data->dateFormat = 'l, d F Y';
        } else {
            $data->dateFormat = 'l, F d, Y';
        }

        if($_COOKIE['user']){
            $data->customer = $p->getCustomerOverviewByID($_COOKIE['user']);
            $data->customerName = $p->customerName($customer);
        } else {
            $data->customer = false;
            $data->customerName = false;
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
                    <div>
                        <div>
                            <div class="padded">
                                <div class="big-card confirmation centered">
                                    <p style="font-size: 3em; padding-top: 1em; margin-bottom: .4em; color: #<?=$assets->primaryColor?>;"><i class="fas fa-check-circle"></i></p>
                                    <h2 class="summary" style="margin-bottom: .3em;">Your event is booked!</h2>
                                    <div><strong><?=$data->info->title?></strong></div>
                                    <div><?=date( $data->dateFormat, strtotime($data->info->startDate) )?></div>
                                    <div><?=$data->info->startTime." &mdash; ",$data->info->duration." ".$data->info->durationType?></div>
                                    <p>&nbsp;</p>
                                    <ul>
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
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
        <script src="assets/globals.js"></script>

        <script type="text/javascript">
            
        </script>



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