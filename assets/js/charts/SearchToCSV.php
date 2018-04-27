<?php

require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
require(DOCUMENT_ROOT . '/class/utility.class.php');

$objSql = new Sql();

// output headers so that the file is downloaded rather than displayed


if (isset($_GET["IdRicerca"]) AND trim($_GET["IdRicerca"]) <> '') {
    $IdRicerca = $_GET["IdRicerca"];

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=SearchID_' . $IdRicerca . '.csv');


    $Columns = "Keyword,IdTweet,ScreenName,Name,DataRegistrazioneUser,ProfileImageURLHTTPS,ProfileBannerURL,StatusesCount,"
            . "FollowersCount,FriendsCount,FavouritesCount,ListedCount,Description,TweetText,Confidence,location,TweetTime,"
            . "ReTweetCount,Lingua,PlaceName,PlaceFullName,PlaceCountry";

    $SqlRicerca = ""
            . " SELECT 
                    Keyword,
                    IdTweet,
                    ScreenName,
                    Name,
                    DataRegistrazioneUser,
                    ProfileImageURLHTTPS,
                    ProfileBannerURL,
                    StatusesCount,
                    FollowersCount,
                    FriendsCount,
                    FavouritesCount,
                    ListedCount,
                    REPLACE(REPLACE(Description, '\r', ''), '\n', '') As Description,
                    REPLACE(REPLACE(TweetText, '\r', ''), '\n', '') As TweetText,
                    Confidence,
                    location,
                    TweetTimeStampStringa as TweetTime,
                    ReTweetCount,
                    Lingua,
                    PlaceName,
                    PlaceFullName,
                    PlaceCountry 
                FROM 
                    AnalisiTweets 
                WHERE 
                    idRicerca = '" . $IdRicerca . "'";

    $output = fopen('php://output', 'w');
    fputcsv($output, explode(",", $Columns));
    $RecordsRicerca = $objSql->SelectArray($objSql->Query('', $SqlRicerca));
    if (isset($RecordsRicerca)) {
        foreach ($RecordsRicerca as $row) {
            fputcsv($output, $row);
        }
    }
}
