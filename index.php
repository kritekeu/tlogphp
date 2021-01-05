<?php
/**
 * Require the library
 */
require 'conf.php';
require 'TLog.php';
/**
 * @var string $devCurDir
 * @var string $curDir
 * @var string $extToIgnore
 *
 */
$curDir = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? $devCurDir : getcwd();
$dirMenu = array();


$tLogDir = strrchr($curDir, '/');

$curDirLog = str_replace($tLogDir, '', $curDir);


$projectLog = strrchr($curDirLog, '/');


$scanDirApacheDir = $curDirLog . '/logs';
if (file_exists($scanDirApacheDir)) {
    $scanDirApacheLog = scandir($scanDirApacheDir);

    foreach ($scanDirApacheLog as $value) {

        if ($value != '.' & $value != '..' & like_match($extToIgnore, $value) != true) {
            $dirMenu['Apache ' . $value] = $curDirLog . '/logs/' . $value;
        }
    }
}
$scanDirSFVarDir = $curDirLog . $projectLog . '/var/log';
if (file_exists($scanDirSFVarDir)) {
    $scanDirSFVarLog = scandir($scanDirSFVarDir);

    foreach ($scanDirSFVarLog as $value) {

        if ($value != '.' & $value != '..' & like_match($extToIgnore, $value) != true) {
            $dirMenu['/var/log/' . $value] = $curDirLog . $projectLog . '/var/log/' . $value;
        }
    }
}
$scanDirPSVarDir = $curDirLog . $projectLog . '/var/logs';
if (file_exists($scanDirPSVarDir)) {
    $scanDirPSVarLog = scandir($scanDirPSVarDir);


    foreach ($scanDirPSVarLog as $value) {
        $dirMenu['/var/logs/' . $value] = $curDirLog . $projectLog . '/var/logs/' . $value;
    }
}



function like_match($extToIgnore, $subject)
{
    foreach ($extToIgnore as $ext) {
        if (stripos($subject,$ext) !== false) {
            return true;
        }
    }

}

/**
 * Initilize a new instance of PHPTail
 * @var TLog
 */
$tail = new TLog($dirMenu);

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
