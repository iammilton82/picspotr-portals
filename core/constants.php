<?
define('AWS', 'https://s3.amazonaws.com/PicSpotr-com');

if (strpos($_SERVER['HTTP_HOST'], 'clientstud-dev.io') !== false) {
    define('ENVIRONMENT', 'development');
    define('APP', 'https://dev-app.picspotr.com');
    define('API', 'https://dev-api.picspotr.com');
	define('INVOICES_URL', 'https://dev-api.picspotr.com/invoice');
	define('QUOTES_URL', 'https://dev-api.picspotr.com/quote');
	define('CONTRACTS_URL', 'https://dev-api.picspotr.com/agreement/index/');
    define('VERSION', time());
	define('ROOT', '/Users/designrockstar/Public/picspotr-portals');
	define('HOTJAR_ID', 1457942);
	define('HOTJAR_VERSION', 6);
} else {
	define('ENVIRONMENT', 'production');
    define('APP', 'https://app.picspotr.com');
    define('API', 'https://api.picspotr.com');
	define('INVOICES_URL', 'https://invoices.picspotr.com/invoice');
	define('QUOTES_URL', 'https://invoices.picspotr.com/quote');
	define('CONTRACTS_URL', 'https://agreements.picspotr.com/agreement/index/');
    define('VERSION', '1.0');
	define('ROOT', '/var/www/apps/picspotr-portals/current');
	define('HOTJAR_ID', 449356);
	define('HOTJAR_VERSION', 6);
}
