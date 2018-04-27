<?php
// print_r($_SERVER);
// echo $_SERVER["DOCUMENT_ROOT"];
 // echo $_SERVER["HTTP_HOST"];
switch($_SERVER["HTTP_HOST"])
{
    case '192.168.1.78':
    case '127.0.0.1':
	case 'localhost':
    case 'localhost'://sviluppo
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        define("CONFIG_AMBIENTE","sviluppo");
        define("CONFIG_SERVER","localhost");
        define("CONFIG_DB_USER","root");
        define("CONFIG_DB_PWD","");//root
        define("CONFIG_DB","SentimentAnalysis");
        define("CONFIG_PATH","http://localhost/SentimentAnalysis");
        define("DOCUMENT_ROOT", $_SERVER["DOCUMENT_ROOT"]."/SentimentAnalysis");
        break;
   
    case '212.129.45.177'://server di David
    case 'www.surftribe.it':
    case 'surftribe.it':
    ini_set('display_errors', '0');
    ini_set('session.gc_maxlifetime', 5);
    ini_set('session.save_path', '/tmp');
    define("CONFIG_AMBIENTE","produzione");
    define("CONFIG_SERVER","212.129.45.177");
    define("CONFIG_DB_USER","xxxx");
    define("CONFIG_DB_PWD","xxxx");
    define("CONFIG_DB","surftrib_sentimentanalysis");
    define("CONFIG_PATH","http://www.surftribe.it/SentimentAnalysis");
    define("DOCUMENT_ROOT", $_SERVER["DOCUMENT_ROOT"]."/SentimentAnalysis");
    define("CONFIG_ATTIVA_UTENTI","0");

    default:
        break;

}

define("CONFIG_COPYRIGHT", "Copyright © Sentiment Analysis.it 2017");
define("CONFIG_INDIRIZZO", "");
define("CONFIG_TELEFONO", "");
define('CONFIG_DB_CHARSET', 'utf8');
define("CONFIG_EMAIL", "xxxxxx@gmail.com");
define("CONFIG_NAME_WEBSITE", "Sentiment Analysis");
define("CONFIG_KEY_CRYPT", "test");

/*
$mysqli = new mysqli(CONFIG_SERVER, CONFIG_DB_USER, CONFIG_DB_PWD, CONFIG_DB);
if ($mysqli->connect_error) {
    die('Errore di connessione (' . $mysqli->connect_error . ') '
        . $mysqli->connect_error);
} else {
    //echo 'Connesso. ' . $mysqli->host_info . "\n";
}
//$mysqli->close();
*/
$pathPageVN = preg_match('~vn=(.*?)&~', $_SERVER['QUERY_STRING'], $outputPageVN);
$currentPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];

define("CONFIG_MAX_FILE_UPLOAD",3000);//la massima grandezza del file upload
define("CONFIG_MAX_FILE_TYPE","case 'jpg':case 'gif':case 'png':case 'gpx':'");//il tipo del file upload
?>