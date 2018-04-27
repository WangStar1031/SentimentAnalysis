<?php
require_once('../config/config.php');
if(isset($_REQUEST['action']) && trim($_REQUEST['action']) <>'')
{

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $mysqli = mysqli_init();
    $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $mysqli->real_connect(CONFIG_SERVER,CONFIG_DB_USER,CONFIG_DB_PWD,CONFIG_DB);


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
            
            $grid->addColumn('CodiceLingua', 'Language', 'string', fetch_pairs($mysqli,'SELECT CodiceLingua, Lingua FROM Lingue ORDER BY CodiceLingua ASC'),true );
            $grid->addColumn('StopWord', 'Stop Word', 'string');
            $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');


            $result = $mysqli->query('SELECT *,StopWord as id FROM StopWords WHERE StopWord IS NOT NULL ORDER BY StopWord ASC');
            //date_format(DataRegistrazioneUser, "%d/%m/%Y") as
            $mysqli->close();

            $grid->renderJSON($result);
         break;

        case 'insert':
        
            $CodiceLingua = $mysqli->real_escape_string(strip_tags($_REQUEST['CodiceLingua']));
            $StopWord = $mysqli->real_escape_string(strip_tags($_REQUEST['StopWord']));
            

            $return=false;
            if ( $stmt = $mysqli->prepare("INSERT INTO StopWords (CodiceLingua, StopWord) VALUES (?,?)")) {

                $stmt->bind_param("ss",
                    $CodiceLingua, $StopWord);
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
            

// This very generic. So this script can be used to update several tables.
            $return=false;
            if ( $stmt = $mysqli->prepare("UPDATE StopWords SET ".$colname." = ? WHERE StopWord = ?")) {
                $stmt->bind_param("ss",$value, $id);
                $return = $stmt->execute();
                $stmt->close();

            }
            $mysqli->close();

            echo $return ? "ok" : "error UPDATE StopWords SET ".$colname." = ".$value." WHERE StopWord = ".$id;

            break;

        case 'delete':

            $id = $mysqli->real_escape_string(strip_tags($_REQUEST['id']));
            $return=false;
            if ( $stmt = $mysqli->prepare("DELETE FROM StopWords  WHERE StopWord = ?")) {
                $stmt->bind_param("s", $id);
                $return = $stmt->execute();
                $stmt->close();
            }
            $mysqli->close();

            echo $return ? "ok" : "error DELETE FROM StopWords  WHERE CodiceLingua"+$id;

            break;

    }









}