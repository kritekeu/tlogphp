<?php
/**
 * Require the library
 */
require 'conf.php';
require 'PHPTail.php';
/**
 * @var string $devCurDir
 * @var string $curDir
 */
$curDir = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? $devCurDir : getcwd();
$dirMenu = array();
$extToIgnore = ['.log','.gz', '.zip'];

$tLogDir = strrchr($curDir, '/');

$curDirLog = str_replace($tLogDir, '', $curDir);


$projectLog = strrchr($curDirLog, '/');

$scanDirApacheLog = scandir($curDirLog . '/logs');

foreach ($scanDirApacheLog as $value) {

    if ($value != '.' & $value != '..' &  like_match($extToIgnore,$value) !=true) {
        $dirMenu['Apache ' . $value] = $curDirLog . '/logs/' . $value;
    }
}


$scanDirSFVarLog = scandir($curDirLog . $projectLog . '/var/log');

foreach ($scanDirSFVarLog as $value) {

    if ($value != '.' & $value != '..' &  like_match($extToIgnore,$value) !=true) {
        $dirMenu['/var/log/' . $value] = $curDirLog . $projectLog . '/var/log/' . $value;
    }
}


$scanDirPSVarLog = scandir($curDirLog . $projectLog . '/var/logs');

foreach ($scanDirPSVarLog as $value) {
    $dirMenu['/var/logs/' . $value] = $curDirLog . $projectLog . '/var/logs/' . $value;
}



function like_match($extToIgnore, $subject)
{
    foreach ($extToIgnore as $ext) {
        if (stripos($subject,$ext) !== false) {
            return true;
        }
    }

}
die();
/**
 * Initilize a new instance of PHPTail
 * @var PHPTail
 */
$tail = new PHPTail(array(
    "Access_Log" => "/var/log/httpd/access_log",
    "Error_Log" => "/var/log/httpd/error_log",
));

/**
 * We're getting an AJAX call
 */
if(isset($_GET['ajax']))  {
    echo $tail->getNewLines($_GET['file'], $_GET['lastsize'], $_GET['grep'], $_GET['invert']);
    die();
}

/**
 * Regular GET/POST call, print out the GUI
 */
$tail->generateGUI();
