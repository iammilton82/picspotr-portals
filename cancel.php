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
$eventId = $_GET['e'];
$customerId = $_GET['c'];

if($recurringId){
    $appointments = $p->getSlotById($id);

    if($appointments){
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
	<title>Cancel an Appointment</title>
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
            <div class="title">Cancel an Appointment</div>
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
                                    <h2 class="summary" style="margin-bottom: .3em;">Would you like to cancel?</h2>
                                    <div><strong><?=$data->info->title?></strong></div>
                                    <div><?=date( $data->dateFormat, strtotime($data->info->block->startDate) )?></div>
                                    <div><?=$data->info->block->startTime." &mdash; ",$data->info->duration." ".$data->info->durationType?></div>
                                    <p>&nbsp;</p>
                                    <p>If you would like to cancel your reserved appointment, click the "Cancel Appointment" button below:</p>
                                    <form name="cancelAppointment">
                                        <fieldset>
                                            <div class="row">
                                                <div class="action-button">
                                                    <button class="enableOnInput" type="submit">Cancel Appointment</button>
                                                    <div role="alert">
                                                        <div rel="system" class="message hide" message="required">A system error occurred and we could not cancel your appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
                                                        <div rel="saving" class="message hide" message="required">An error occurred and we could not cancel this appointment. Send an email to <?= $data->portal->emailAddress ?> for assistance.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
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
            $("[name='cancelAppointment']").on("submit", function(e) {

                e.preventDefault();

                $('.enableOnInput').prop('disabled', true);

                $.ajax({
                    url: "<?= API ?>/appointments/cancel/<?=$portal->photographerId?>/<?=$customerId?>/<?=$eventId?>/<?=$id?>",
                    method: "GET",
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
                        window.location.replace("/cancelled");
                    } else {
                        $("[rel='saving']").removeClass("hide");
                        $('.enableOnInput').prop('disabled', false);
                    }
                }).fail(function() {
                    $("[rel='system']").removeClass("hide");
                    $('.enableOnInput').prop('disabled', false);
                });

                

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