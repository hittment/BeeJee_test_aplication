<?php
$stime = microtime();
$stime = explode(" ",$stime);
$stime = $stime[1] + $stime[0];

error_reporting(-1);
ini_set('display_errors',false);
ini_set('upload_max_filesize', '25M');
ini_set('max_file_uploads', 7);

define('_#FWLone', true);
define('C', 'UTF-8');
define('TIME', time());
define('H', $_SERVER['DOCUMENT_ROOT'].'/');
define('URL', $_SERVER['REQUEST_URI']);
setlocale(LC_ALL, 'ru_RU', 'ru_RU.UTF-8', 'ru', 'russian');

session_start();
ob_start();

require('system/lib/fw/autoloader.php');
fw\autoloader :: register();

try{

	require(H.'system/common.php');
	
	$router = new fw\router();

	require(H.'app/modules/'.$router -> template.'.php');
	
	$tpl->display('header');
	$tpl->display((isset($template)?$template:$router -> template));
	$tpl->display('footer');
	
} catch (Exception $e) {
	
	die ('Error ' . $e->getMessage());
	
}

$etime = microtime();
$etime = explode(" ", $etime);
$etime = $etime[1] + $etime[0];

echo '<!-- Gen. '.number_format($etime - $stime, 5).' sec-->';
