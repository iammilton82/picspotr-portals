<?

require("./vendor/autoload.php");
require("core/constants.php");
require("core/core.php");

use Underscore\Underscore as _;

$core = new Core();
$p = new Portal();
$u = new User();

$portal = $p->getPortalBySubdomain();
date_default_timezone_set($portal->timezone);

$assets = $p->portalAssets($portal);
$allowBooking = true;
$now = date('Y-m-d H:i:s', time());

$recurringId = $_GET['r'];
$id = $_GET['i'];

$reschedule = isset($_GET['reschedule']) ? $_GET['reschedule'] : 0;
$oldEventId = isset($_GET['o_eventId']) ? $_GET['o_eventId'] : false;
$oldCustomerId = isset($_GET['customerId']) ? $_GET['customerId'] : false;
$oldTimeSlotId = isset($_GET['o_timeSlotId']) ? $_GET['o_timeSlotId'] : false;

if($reschedule == 1){
    if($oldEventId){
        $oldEvent = $p->getEventDetails($oldEventId);
        $customer = $oldEvent->customers[0][0];

    }
}

if($recurringId){
    $appointments = $p->getSlotById($id);

    if($appointments && sizeof($appointments->availability)>0){
        $data = new stdClass();
        $data->info = $appointments;
        $data->hasAddress = $p->hasLocationAddress($data->info);
        $data->portal = $portal;
        $data->hasCustomer = $_COOKIE['user'] ? true : false;
        if($data->portal->country !== 'USA'){
            $data->dateFormat = 'l, d F Y';
        } else {
            $data->dateFormat = 'l, F d, Y';
        }

        $data->details = $core->calculateDateDiff($now, $data->info->block->time24);

        if($_COOKIE['user']){
            $data->customer = $p->getCustomerOverviewByID($_COOKIE['user']);
            $data->customerName = $p->customerName($customer);
        } else {
            $data->customer = false;
            $data->customerName = false;
        }

        $showAppointments = true;

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
    <link rel="stylesheet" type="text/css" media="all" href="<?= APP ?>/assets/css/build.css?v=<?= VERSION ?>" />

    <?
    #load the sdk's for each payment vendor
    if($data->info->paymentRequired === 1){
        if($data->info->paymentConfig !== false){
            switch($data->info->paymentConfig->providerTypeId){ 
                case 1:
                    echo '<script type="text/javascript" src="https://js.braintreegateway.com/v2/braintree.js"></script>';
                break;
                case 3:
                    echo '<script type="text/javascript" src="https://js.stripe.com/v2/"></script>';
                break;
                case 5:
                    // square
                    if(ENVIRONMENT == 'development'){
                        echo '<script type="text/javascript" src="https://js.squareupsandbox.com/v2/paymentform"></script>';
                    } else {
                        echo '<script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>';				
                    }
                break;
            }
        }
    }
    ?>

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
                                    <div class="big-card <?= $reschedule == 1 ? 'has-header' : '' ?>">

                                        <? if($reschedule == 1){ ?>
                                        <div class="alert-header">Re-schedule Appointment Details</div>
                                        <? } ?>

                                        <h1 class="title"><?= $data->info->title ?></h1>
                                        <ul>
                                            <li id="duration" class="row">
                                                <? if($reschedule == 1){ ?>
                                                <div>Your new appointment date and time will be</div>
                                                <? } else { ?>
                                                <div>Your appointment date and time will be</div>
                                                <? } ?>
                                                <div><strong><?= date($data->dateFormat, strtotime($data->info->block->startDate)) ?></strong></div>
                                                <i class="far fa-stopwatch"></i> <?= $data->info->block->startTime . " &mdash; ", $data->info->duration . " " . $data->info->durationType ?>
                                            </li>

                                            <? if($data->hasAddress){ ?>
                                            <li>
                                                <? if(strlen($data->info->location)>0){?>
                                                <div><strong><?= $data->info->location ?></strong></div>
                                                <? } ?>
                                                <? if(strlen($data->info->address1)>0){?>
                                                <div><?= $data->info->address1 ?></div>
                                                <? } ?>
                                                <? if(strlen($data->info->address2)>0){?>
                                                <div><?= $data->info->address2 ?></div>
                                                <? } ?>
                                                <? if(strlen($data->info->city)>0 || strlen($data->info->state)>0){?>
                                                <div><?= strlen($data->info->city) > 0 ? $data->info->city : "" ?>
                                                    <?= strlen($data->info->state) > 0 ? $data->info->state : "" ?>
                                                    <?= strlen($data->info->zipCode) > 0 ? $data->info->zipCode : "" ?>
                                                </div>
                                                <? } ?>
                                            </li>
                                            <? } ?>
                                        </ul>

                                        <p>&nbsp;</p>
                                        <? if($reschedule == 1){ ?>
                                        <ul class="cancelled-appointment">
                                            <li class="bottom-border">Former Appointment</li>
                                            <li id="duration" class="row">
                                                <div><strong><?= date($data->dateFormat, strtotime($oldEvent->startDate1)) ?></strong></div>
                                                <i class="far fa-stopwatch"></i> <?= $oldEvent->appointment->startTime . " &mdash; ", $data->info->duration . " " . $data->info->durationType ?>
                                            </li>
                                        </ul>
                                        <? } ?>

                                    </div>
                                </div>
                            </div>
                            <? if($data->details->totalHours >= $data->info->fewerThanLimit){ ?>
                            <div class="column halfs">
                                <div class="padded">
                                    
                                    <form name="scheduleAppointment">
                                        <fieldset>
                                            <? if($reschedule == 1){ ?>

                                            <input type="hidden" name="fName" value="<?=$customer->firstName?>" />
                                            <input type="hidden" name="lName" value="<?=$customer->lastName?>" />
                                            <input type="hidden" name="email" value="<?=$customer->emailAddress?>" />
                                            <input type="hidden" name="telephone" value="<?=$customer->telephone ? $customer->telephone : null ?>" />

                                            <div class="row">
                                                <p>Click the "Re-schedule Appointment" button below to confirm</p>
                                            </div>

                                            <div class="action-button">
                                                <button class="enableOnInput" id="sTSubmit" type="submit">Re-schedule Appointment</button>
                                                <div role="alert">
                                                    <div rel="system" class="message hide" message="required">A system error occurred and we could not save your appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
                                                    <div rel="saving" class="message hide" message="required">An error occurred and we could not reserve this appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                            $("form[name='scheduleAppointment']").on("submit", function(e){
                                                e.preventDefault();
                                                
                                                $('#sTSubmit').prop('disabled', true);
 
                                                 var firstName = $("[name='fName']").val();
                                                var lastName = $("[name='lName']").val();
                                                var email = $("[name='email']").val();
                                                var telephone = $("[name='telephone']").val();
                                                
                                               var appointment = {};
                                                var startDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').unix();
                                                var endDateTime = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').add(<?= $data->info->duration ?>, '<?= $data->info->durationType ?>').unix();
                                            
                                                appointment.emailAddress = email;
                                                appointment.firstName = firstName;
                                                appointment.lastName = lastName;
                                                appointment.scheduleId = <?= $data->info->block->id ?>;
                                                appointment.photographerId = <?= $data->info->userId ?>;
                                                appointment.recurringId = '<?= $data->info->recurringId ?>';
                                                appointment.customerId = <?= $_COOKIE['user'] ? $_COOKIE['user'] : 0 ?>;
                                                appointment.startDate = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').format("YYYY-MM-DD HH:mm:ss");
                                                appointment.endDate = moment('<?= $data->info->block->startDate ?> <?= $data->info->block->startTime ?>').add(<?= $data->info->duration ?>, '<?= $data->info->durationType ?>').format("YYYY-MM-DD HH:mm:ss");
                                                appointment.startDateTime = startDateTime;
                                                appointment.endDateTime = endDateTime;
                                                appointment.startDate1 = moment.unix(startDateTime).format("YYYY-MM-DD HH:mm:ss");
                                                appointment.endDate1 = moment.unix(endDateTime).format("YYYY-MM-DD HH:mm:ss");
                                                appointment.workflowTemplateId = <?= $data->info->workflowTemplateId ? $data->info->workflowTemplateId : 0 ?>;
                                            
                                                <? if($reschedule == 1){ ?>
                                                var postURL = "<?=API?>/appointments/reschedule/<?=$data->info->userId?>/<?=$oldCustomerId?>/<?=$oldEventId?>/<?=$oldTimeSlotId?>";
                                                <? } else { ?>
                                                var postURL = "<?=API?>/appointments/reserve";
                                                <? } ?>
                                            
                                                $.ajax({
                                                    url: postURL,
                                                    method: "POST",
                                                    data: appointment,
                                                    dataType: 'json',
                                                    async: false,
                                                    crossDomain: true,
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                                        'X-ACCESS_TOKEN': '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
                                                        'TimezoneOffset': new Date().getTimezoneOffset()
                                                    }
                                                }).done(function(response) {
                                            
                                                    if (response.status === 1 || response.status === true) {
                                                        
                                                        var theReservation = response.data;
                                                        
                                                        // **************  add to alert log
                                                        var alert = {};
                                                        alert.userId = appointment.photographerId;
                                                        alert.documentId = response.data.id;
                                                        alert.contentType = 'event';
                                                        alert.itemRead = 0;
                                                        alert.deleted = 0;
                                                        alert.type = 'action';
                                                        alert.content = appointment.firstName+" "+appointment.lastName+" has rescheduled <?=$data->info->title?> for "+appointment.startDate+".";
                                                        
                                                        $.ajax({
                                                            url: "<?=API?>/v2/public/alerts/save",
                                                            method: "POST",
                                                            data: alert,
                                                            dataType: 'json',
                                                            async: false,
                                                            crossDomain: true,
                                                            headers: {
                                                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                                                'X-ACCESS_TOKEN': '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
                                                                'TimezoneOffset': new Date().getTimezoneOffset()
                                                            }
                                                        }).done(function(response){
                                                            
                                                            <? if($reschedule == 1){ ?>
                                                            var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>&reschedule=1";
                                                            <? } else { ?>
                                                            var confirmationURL = "/reserved?i=<?= $data->info->block->id ?>&r=<?= $data->info->recurringId ?>";
                                                            <? } ?>
                                            
                                                            window.location.replace(confirmationURL);
                                                            
                                                        }).fail(function(){
                                                            
                                                            $(errorDiv).html("<div class='alert error'><p>Your appointment has been booked but a system error occurred.  Please contact technical support.</p></div>");
                                                            $('#sTSubmit').prop('disabled', false);
                                                            
                                                        });
                                                        
                                                        
                                                    } else {
                                                        $("[rel='saving']").removeClass("hide");
                                                        $('#sTSubmit').prop('disabled', false);
                                                    }
                                                }).fail(function() {
                                                    $("[rel='system']").removeClass("hide");
                                                    $('#sTSubmit').prop('disabled', false);
                                                });
                                            });
                                            </script>

                                            <? } else { ?>
                                                <? if($data->info->paymentRequired === 1){ ?>
                                                    <?
                                                    if($data->info->paymentConfig !== false){
                                                    ?>
                                                    <div class="module" style="margin-bottom: 4em;">
                                                        <div id="payment-module">
                                                            <section id="payment" class="row">
                                                            <? 
                                                                switch($data->info->paymentConfig->providerTypeId){ 
                                                                    /*
                                                                    case 1: 
                                                                        #braintree				                	
                                                                        include('snippets/payment/braintree.php');
                                                                    break; 
                                                                    case 2: 
                                                                       #paypal
                                                                        include('snippets/payment/paypal.php');
                                                                    break; 
                                                                    */
                                                                    case 3:
                                                                        #stripe				                	
                                                                        include('snippets/payment/stripe.php');
                                                                    break; 
                                                                    /*
                                                                    case 4:
                                                                        #authorizenet				                	
                                                                        include('snippets/payment/authorizenet.php');
                                                                    break; 
                                                                    */
                                                                    case 5:
                                                                        #square				                	
                                                                        include('snippets/payment/square.php');
                                                                    break; 
                                                                    /*
                                                                    case 6:
                                                                        #payfast				                	
                                                                        include('snippets/payment/payfast.php');
                                                                    break; 
                                                                    */
                                                                    default:
                                                                        include("snippets/payment/no-payment-reservation.php");
                                                                    break;
                                                                }				
                                                            
                                                            ?>
                                                            </section>
                                                        </div>
                                                    </div>
                                                    <? }?>
                                                <? } else { ?>
                                                <? include("snippets/payment/no-payment-reservation.php"); ?>
                                                <? } ?>
                                            <? } ?>

                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                            <? } else { ?>
                            <div class="column halfs"> 
                                <section>
                                    <div class="none appointments">
                                        <p>You cannot reserve this time slot. Appointments must be booked greater than <?= $data->info->fewerThanLimit ?> hours in advance.</p>
                                        <p><a href="/book?id=<?= $data->info->recurringId ?>">&lsaquo; View other time slots</a></p>
                                    </div>
                                </section>
                            </div>
                            <? } ?>
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