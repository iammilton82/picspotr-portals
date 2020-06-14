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
                                            <div><strong><?=date( $data->dateFormat, strtotime($data->info->block->startDate) )?></strong></div>
                                            <i class="far fa-stopwatch"></i> <?=$data->info->block->startTime." &mdash; ",$data->info->duration." ".$data->info->durationType?> 
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
                        <? if($data->details->totalHours >= $data->info->fewerThanLimit){ ?>
                        <div class="column halfs">
                            <div class="padded">
                                <p>Enter your details below:</p>
                                <form name="scheduleAppointment">
                                    <fieldset>
                                        <div class="row">
                                            <div class="field">
                                                <input class="has-data" type="text" name="fName" value="" required />
                                                <label for="fName">First Name <span class="required">*</span></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="field">
                                                <input class="has-data" type="text" name="lName" value="" required />
                                                <label for="lName">Last Name <span class="required">*</span></label>
                                                <div role="alert">
                                                    <div rel="lName" class="message hide" message="required">Your last name is required</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="field">
                                                <input class="has-data" type="text" name="email" value="" required />
                                                <label for="emailAddress">E-mail Address <span class="required">*</span></label>
                                                <div role="alert">
                                                    <div rel="email" class="message hide" message="required">Your email address is required</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="field">
                                                <input class="has-data" type="text" name="telephone" value="" />
                                                <label for="telephone">Telephone #</label>
                                            </div>
                                        </div>
                                        <div class="action-button">
                                            <button class="enableOnInput" type="submit">Schedule Event</button>
                                            <div role="alert">
                                                <div rel="system" class="message hide" message="required">A system error occurred and we could not save your appointment. Send an email to <?=$data->portal->emailAddress?> for assistance.</div>
                                                <div rel="saving" class="message hide" message="required">An error occurred and we could not reserve this appointment.  Send an email to <?=$data->portal->emailAddress?> for assistance.</div>
                                            </div>
                                        </div>
                                        
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <? } else { ?>
                        <div class="column halfs"
                            <section>
                                <div class="none appointments">
                                    <p>You cannot reserve this time slot.  Appointments must be booked greater than <?=$data->info->fewerThanLimit?> hours in advance.</p>
                                    <p><a href="/book?id=<?=$data->info->recurringId?>">&lsaquo; View other time slots</a></p>
                                </div>
                            </section>
                        </div>
                        <? } ?>
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
            $("[name='scheduleAppointment']").on("submit", function(e){
                
                e.preventDefault();

                $('.enableOnInput').prop('disabled', true);

                var form = $(this).serializeArray();
                var errors = 0;

                if(_.isUndefined(form[2].value) || _.isNull(form[2].value) || form[2].value.length < 5){
                    $("[rel='email']").removeClass("hide");
                    errors++;
                } else {
                    $("[rel='email']").addClass("hide");
                }

                if(_.isUndefined(form[1].value) || _.isNull(form[1].value) || form[1].value.length < 1){
                    $("[rel='lName']").removeClass("hide");
                    errors++;
                } else {
                    $("[rel='lName']").addClass("hide");
                }

                if(_.isUndefined(form[0].value) || _.isNull(form[0].value) || form[0].value.length < 1){
                    $("[rel='fName']").removeClass("hide");
                    errors++;
                } else {
                    $("[rel='fName']").addClass("hide");
                }

                if(errors === 0){
                    var appointment = {};
                    var startDateTime = moment('<?=$data->info->block->startDate?> <?=$data->info->block->startTime?>').unix();
                    var endDateTime = moment('<?=$data->info->block->startDate?> <?=$data->info->block->startTime?>').add(<?=$data->info->duration?>, '<?=$data->info->durationType?>').unix();
                    
                    appointment.emailAddress = form[2].value;
                    appointment.firstName = form[0].value;
                    appointment.lastName = form[1].value;
                    appointment.scheduleId = <?=$data->info->block->id?>;
                    appointment.photographerId = <?=$data->info->userId?>;
                    appointment.recurringId = '<?=$data->info->recurringId?>';
                    appointment.customerId = <?=$_COOKIE['user'] ? $_COOKIE['user'] : 0?>;
                    appointment.startDate = moment('<?=$data->info->block->startDate?> <?=$data->info->block->startTime?>').format("YYYY-MM-DD HH:mm:ss");
                    appointment.endDate = moment('<?=$data->info->block->startDate?> <?=$data->info->block->startTime?>').add(<?=$data->info->duration?>, '<?=$data->info->durationType?>').format("YYYY-MM-DD HH:mm:ss");
                    appointment.startDateTime = startDateTime;
                    appointment.endDateTime = endDateTime;
                    appointment.startDate1 = moment.unix(startDateTime).format("YYYY-MM-DD HH:mm:ss");
                    appointment.endDate1 = moment.unix(endDateTime).format("YYYY-MM-DD HH:mm:ss");
                    appointment.workflowTemplateId = <?=$data->info->workflowTemplateId?>;

                    $.ajax({
                        url: "<?=API?>/appointments/reserve",
                        method: "POST",
                        data: appointment,
                        dataType: 'json',
                        async: false,
                        crossDomain: true,
                        headers: {
                            'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-ACCESS_TOKEN' : '1403b8cc3cdaf3f01361daefeeb8c182adcb6286',
                            'TimezoneOffset' : new Date().getTimezoneOffset()
                        }
                    }).done(function(response){
                        
                        if(response.status === 1 || response.status === true){
                            window.location.replace("/reserved?i=<?=$data->info->block->id?>&r=<?=$data->info->recurringId?>");
                        } else {
                            $("[rel='saving']").removeClass("hide");
                            $('.enableOnInput').prop('disabled', false);
                        }
                    }).fail(function(){
                        $("[rel='system']").removeClass("hide");
                        $('.enableOnInput').prop('disabled', false);
                    });
                    
                } else {
                    $('.enableOnInput').prop('disabled', false);
                }

                return false;
            });
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