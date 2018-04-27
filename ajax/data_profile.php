<?php
if(isset($_REQUEST['action']) && trim($_REQUEST['action']) <>'')
{
    $config = array(
        "db_name" => "sentimentanalysis",
        "db_user" => "root",
        "db_password" => "",
        "db_host" => "localhost"
    );
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $mysqli = mysqli_init();
    $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']);


    switch (trim($_REQUEST['action']))
    {
        case 'load':
            require_once('EditableGrid.php');
            /**
             * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
             * It will be used to generate possible values for some columns.
             */
            function fetch_pairs($mysqli,$query){
                if (!($res = $mysqli->query($query)))return FALSE;
                $rows = array();
                while ($row = $res->fetch_assoc()) {
                    $first = true;
                    $key = $value = null;
                    foreach ($row as $val) {
                        if ($first) { $key = $val; $first = false; }
                        else { $value = $val; break; }
                    }
                    $rows[$key] = $value;
                }
                return $rows;
            }



            $grid = new EditableGrid();

            //$grid->addColumn('id', 'ID', 'int', NULL, false);
            //$grid->addColumn('Keyword', 'Keyword', 'string');

            $grid->addColumn('user_name', 'user_name', 'string');
            //$grid->addColumn('Lingua', 'Lingua', 'string', fetch_pairs($mysqli,'SELECT lingua_id, lingua_name FROM language'),true );
            $grid->addColumn('user_nome', 'Nome', 'string');
            $grid->addColumn('user_cognome', 'Cognome', 'string');

            $grid->addColumn('user_email', 'user_email', 'string');
            //$grid->addColumn('action', 'Action', 'html', NULL, false, 'IdTweet');


            $result = $mysqli->query('SELECT *,user_id as id  FROM users WHERE user_id = 227');
            $mysqli->close();

            $grid->renderJSON($result);
         break;

        case 'insert':
            $Keyword = $mysqli->real_escape_string(strip_tags($_POST['Keyword']));
            $ScreenName = $mysqli->real_escape_string(strip_tags($_POST['ScreenName']));
            $StatusesCount = $mysqli->real_escape_string(strip_tags($_POST['StatusesCount']));

            $FollowersCount = $mysqli->real_escape_string(strip_tags($_POST['FollowersCount']));
            $FriendsCount = $mysqli->real_escape_string(strip_tags($_POST['FriendsCount']));
            $Description = $mysqli->real_escape_string(strip_tags($_POST['Description']));

            $TweetText = $mysqli->real_escape_string(strip_tags($_POST['TweetText']));
            $Sentiment = $mysqli->real_escape_string(strip_tags($_POST['Sentiment']));
            $Confidence = $mysqli->real_escape_string(strip_tags($_POST['Confidence']));

            $location = $mysqli->real_escape_string(strip_tags($_POST['location']));
            $ReTweetCount = $mysqli->real_escape_string(strip_tags($_POST['ReTweetCount']));
            $Lingua = $mysqli->real_escape_string(strip_tags($_POST['Lingua']));

            $IdRicerca = $mysqli->real_escape_string(strip_tags($_POST['IdRicerca']));
            $TweetTimeStampStringa = date("Y-m-d H:i:s");
            $DataRegistrazioneUser = date("Y-m-d H:i:s");

            $return=false;
            if ( $stmt = $mysqli->prepare("INSERT INTO analisitweets  (
Keyword, ScreenName,StatusesCount,
FollowersCount,FriendsCount,Description,
TweetText,Sentiment,
Confidence,location,TweetTimeStampStringa,
ReTweetCount,Lingua,IdRicerca,
DataRegistrazioneUser) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")) {

                $stmt->bind_param("sssssssssssssss",
                    $Keyword, $ScreenName,$StatusesCount,
                    $FollowersCount,$FriendsCount,
                    $Description,$TweetText,$Sentiment,
                    $Confidence,$location,$TweetTimeStampStringa,
                    $ReTweetCount,$Lingua,$IdRicerca,
                    $DataRegistrazioneUser);
                $return = $stmt->execute();
                $stmt->close();
            }
            $mysqli->close();

            echo $return ? "ok" : "error "+$stmt->error;
            break;

        case 'update':

            // Get all parameters provided by the javascript
            $colname = $mysqli->real_escape_string(strip_tags($_POST['colname']));
//$id = $mysqli->real_escape_string(strip_tags($_POST['id']));

            $id = $mysqli->real_escape_string(strip_tags($_REQUEST['id']));
            $coltype = $mysqli->real_escape_string(strip_tags($_POST['coltype']));
            $value = $mysqli->real_escape_string(strip_tags($_POST['newvalue']));


// Here, this is a little tips to manage date format before update the table
            if ($coltype == 'date') {
                if ($value === "")
                    $value = NULL;
                else {
                    $date_info = date_parse_from_format('d/m/Y', $value);
                    $value = "{$date_info['year']}-{$date_info['month']}-{$date_info['day']}";
                }
            }

// This very generic. So this script can be used to update several tables.
            $return=false;
            if ( $stmt = $mysqli->prepare("UPDATE users SET ".$colname." = ? WHERE user_id = ?")) {
                $stmt->bind_param("si",$value, $id);
                $return = $stmt->execute();
                $stmt->close();

            }
            $mysqli->close();

            echo $return ? "ok" : "error UPDATE users SET ".$colname." = ".$value." WHERE user_id = ".$id;
            
            break;

        case 'delete':

            $id = $mysqli->real_escape_string(strip_tags($_REQUEST['id']));
            $return=false;
            if ( $stmt = $mysqli->prepare("DELETE FROM analisitweets  WHERE IdTweet = ?")) {
                $stmt->bind_param("i", $id);
                $return = $stmt->execute();
                $stmt->close();
            }
            $mysqli->close();

            echo $return ? "ok" : "error DELETE FROM analisitweets  WHERE IdTweet"+$id;

            break;

    }









}