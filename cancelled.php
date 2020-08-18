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
            <div class="title">Cancel an Appointment</div>
            <div class="actions">
                <div class="action-button">
                    
                </div>
            </div>
        </header>
        <? } ?>

        <div class="module full section-container">
        
            <div>
                <section>
                    <div class="none appointments">
                        <p>Your appointment has been cancelled successfully.</p>
                    </div>
                </section>
            </div>
            
        </div>


    </div>
</div>

<? require("include-footer.php") ?>