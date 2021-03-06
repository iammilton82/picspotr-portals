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

$recurringId = $_GET['r'];
$customerId = $_GET['c'];
$eventId = $_GET['e'];
$timeSlotId = $_GET['i'];

$showCancel = false;

$event = $p->getEventDetails($eventId);
$customer = $oldEvent->customers[0][0];


$today = date('Y-m-d', time());

if($recurringId){
    $appointments = $p->getAvailableTimeSlots($recurringId);
    
    if($appointments && sizeof($appointments)>0){
        $data = new stdClass();
        $data->info = $appointments[0];
        
        if(sizeof($data->info->availability)>0){
           
            $data->startDate = $data->info->availability[0]->startDate;

            $data->dates = _::groupBy($data->info->availability, 'startDate');
            $data->hasAddress = $p->hasLocationAddress($data->info);
            $data->portal = $portal;
            if($data->portal->country !== 'USA'){
                $data->dateFormat = 'l, d F Y';
            } else {
                $data->dateFormat = 'l, F d, Y';
            }

            $data->info->isRecurring = sizeof($data->dates) > 1 ? true : $data->info->isRecurring;

            $showAppointments = true;
        } else {
            $showAppointments = false;
        }

    } else {
        $showAppointments = false;
        $showCancel = true;
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
	<title>Re-schedule an Appointment</title>
	<meta name="description" content="The HTML5 Herald">
	<meta name="author" content="SitePoint">
    <link rel="stylesheet" href="https://use.typekit.net/qju2ojt.css">
    <link rel="stylesheet" type="text/css" media="all" href="/node_modules/pg-calendar/dist/css/pignose.calendar.min.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP?>/assets/css/build.css?v=<?=VERSION?>" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script type="text/javascript" src="/node_modules/pg-calendar/dist/js/pignose.calendar.full.js"></script>

	<? include("include-tracking.php"); ?>
    
</head>

<? require("include-header.php") ?>

<div class="row dash-wrapper">
    <div class="portal-container dash-container">

        <? if($_COOKIE['user']){ ?>
        <header id="main-header" class="row">
            <div class="back-button"><a href="/events-details?eventId=<?=$eventId?>">Back</a></div>
            <div class="title">Re-schedule an Appointment</div>
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
                                <div class="big-card has-header">
                                    
                                    <div class="alert-header">Re-schedule Appointment</div>

                                    <h1 class="title"><?=$event->title?></h1>
                                    <ul>
	                                    
	                                    <li>
	                                    	<h3><?=date($data->dateFormat, strtotime($event->startDate1))?> at <?=$event->appointment->startTime?></h3>
	                                    </li>
	                                    
                                        <li id="duration" class="row">
                                            <i class="far fa-stopwatch"></i> <strong><?=$data->info->duration." ".$data->info->durationType?></strong>
                                        </li>

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
                        <div id="appointments" class="column halfs">
                            <div class="padded">
                                <? if(sizeof($data->dates)>0){ ?>
                                
                                <? if($data->info->isRecurring === 0){ ?>
                                <p>Select an available time:</p>
                                <? } else { ?>
                                <p>Select a new available date and time:</p>
                                <? } ?>
                                
                                
                                <div class="calendar"></div>
                                
                                <? 
                                foreach($data->dates as $date){
                                    $show = 'hide';
                                    if($data->info->isRecurring === 0){
                                        $show = 'show';
                                    } else {
                                        $show = $date[0]->date === date('Y-m-d', time()) ? 'show' : 'hide';
                                    }
                                ?>
                                <div rel="<?=$date[0]->date?>" class="available-dates row <?=$date[0]->date === date('Y-m-d', time()) ? 'show' : 'hide' ?>">
                                    <? if($data->info->isRecurring === 1){ ?>
                                    <h2><?=date($data->dateFormat, strtotime($date[0]->startDate))?></h2>
                                    <? } ?>
                                    
                                    <ul class="ps-card-layout">
                                        <? $times = _::indexBy($date, 'startTime');
                                        foreach($times as $d){

                                            if($d->status === 'free'){ ?>
                                        <li>
                                            <div class="padded">
                                                <h3 class="summary"><a href="/reserve?i=<?=$d->id?>&r=<?=$d->recurringId?>&reschedule=1&customerId=<?=$customerId?>&o_eventId=<?=$eventId?>&o_timeSlotId=<?=$timeSlotId?>"><?=$d->startTime?></a></h3>
                                            </div>
                                        </li>
                                        <? } } ?>
                                    </ul>
                                </div>
                                <?
                                } 
                                ?>

                                <div id="none-to-book" class="hide">
                                    <section>
                                        <div class="none appointments">
                                            <p>There are no available times that you can book for this date.</p>
                                        </div>
                                    </section>
                                </div>
                                
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
                    <p>There are no available times that you can book.</p>
                    <? if($showCancel === true){ ?>
                        <p>If you would like to cancel this appointment, <a href="/cancel?i=<?=$timeSlotId?>&r=<?=$recurringId?>&c=<?=$customerId?>&e=<?=$eventId?>">click here</a></p>
                    <? } ?>
                </div>
            </section>
        </div>
        <? } ?>
            
        </div>


    </div>
</div>

<script type="text/javascript">
$(function() {
    $('.calendar').pignoseCalendar({
        init: function(context){
            if(context.current[0]._i == '<?=$data->startDate?>'){
                var selectedDate = '<?=date('Y-m-d', strtotime($data->startDate))?>';
                $("#none-to-book").removeClass("show").addClass("hide");
                $("[rel='"+selectedDate+"']").removeClass("hide").addClass("show");
            } else {
                $("#none-to-book").removeClass("hide").addClass("show");
            }
            
            if(<?=$data->info->isRecurring?> === 0){
	            $(".calendar > .pignose-calendar").addClass("hide");
            }
        },
        select: function(date, context){
            if(date[0] !== null){
                $(".available-dates").removeClass("show").addClass("hide");
                if(date[0]._i){
                    var selectedDate = date[0]._i;
                    var theDateList = $("[rel='"+selectedDate+"']");
                    if(theDateList.length > 0){
                        $("#none-to-book").removeClass("show").addClass("hide");
                        $("[rel='"+selectedDate+"']").removeClass("hide").addClass("show");
                    } else {
                        $("#none-to-book").removeClass("hide").addClass("show");
                    }
                }
            }
            
        },
        toggle: false,
        schedules: [
            <? foreach($data->dates as $date){ 
                foreach($date as $day){
                ?>
            {
                name: "<?=$data->info->title?>",
                date: '<?=$day->date?>'
            },
            <? } } ?>
        ],
        <? if($data->info->isRecurring === 0){ ?>
        date: moment('<?=$data->startDate?>'),    
        <? } ?>
    });
});
</script>

<? require("include-footer.php") ?>