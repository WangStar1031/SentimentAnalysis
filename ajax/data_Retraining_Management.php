<?php
require_once('../config/config.php');

//            print_r($_REQUEST);

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
            
            //$grid->addColumn('CodiceLingua', 'CodiceLingua', 'string', fetch_pairs($mysqli,'SELECT idCodiceLingua, Lingua FROM lingue ORDER BY idCodiceLingua ASC'),true );
//            $grid->addColumn('idCodiceLingua', 'Language', 'string', NULL, false, NULL, true, true);
//            $grid->addColumn('TipoRiga', 'Type', 'string', NULL, false, NULL, true, true );
            //$Pesi = array("+1" => "+1", "0" => "0","-1" => "-1");
            $Pesi = array("1" => "1", "-1" => "-1");
            $grid->addColumn('Tweet', 'Tweet', 'string');
            $grid->addColumn('Peso', 'Weight', 'string', $Pesi, true );
            $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

            $CodiceLingua = $_REQUEST["idLanguage"];
//            $TipoSet = $mysqli->real_escape_string(strip_tags($_REQUEST['TipoSet']));
            $TipoRiga = $_REQUEST["TipRiga"];
            
// And idCodiceLingua = ' . $CodiceLingua . '
            $result = $mysqli->query(''
                    . 'SELECT *, idRiga as id '
                    . 'FROM TrainingSet '
                    . 'WHERE '
                    . '         TipoSet = "I" And '
                    . '         TipoRiga = "'. $TipoRiga .'" And '
                    . '         idCodiceLingua = '. $CodiceLingua .' '
                    . 'ORDER BY Tweet ASC'
                    );
            //date_format(DataRegistrazioneUser, "%d/%m/%Y") as
            $mysqli->close();

            $grid->renderJSON($result);
         break;

        case 'insert':
        
            $CodiceLingua = $mysqli->real_escape_string(strip_tags($_REQUEST['CodiceLingua']));
            $TipoSet = $mysqli->real_escape_string(strip_tags($_REQUEST['TipoSet']));
            $TipoRiga = $mysqli->real_escape_string(strip_tags($_REQUEST['TipoRiga']));
            $Tweet = stripslashes($mysqli->real_escape_string(strip_tags($_REQUEST['Tweet'])));
            $Peso = $mysqli->real_escape_string(strip_tags($_REQUEST['Peso']));
            

            $return=false;
            if ( $stmt = $mysqli->prepare("INSERT INTO TrainingSet (idCodiceLingua, TipoSet, TipoRiga, Tweet, Peso) VALUES (?,?,?,?,?)")) {
                $stmt->bind_param("sssss", $CodiceLingua, $TipoSet, $TipoRiga, $Tweet, $Peso);
                $return = $stmt->execute();
                $stmt->close();
            }
            $mysqli->close();

            echo $return ? "ok" : "error "+$stmt->error;
            break;
        case 'totals':
        
            $CodiceLingua = $_REQUEST['CodiceLingua'];
            $TipoRiga = $_REQUEST['TipoRiga'];
            
            require(DOCUMENT_ROOT . '/class/connection.class.php');
            require(DOCUMENT_ROOT . '/class/common.class.php');
            require(DOCUMENT_ROOT . '/class/sql.class.php');
            $objSql = new Sql();
            $SqlTotali = 'Select '
            . "(SELECT COUNT(*) FROM TrainingSet WHERE idCodiceLingua = " . $CodiceLingua . " AND TipoSet = 'I' AND TipoRiga = '" . $TipoRiga . "' ) AS Totale, "
            . "(SELECT COUNT(*) FROM TrainingSet WHERE idCodiceLingua = " . $CodiceLingua . " AND TipoSet = 'I' AND TipoRiga = '" . $TipoRiga . "' AND Peso = -1) AS Negativi, "
            . "(SELECT COUNT(*) FROM TrainingSet WHERE idCodiceLingua = " . $CodiceLingua . " AND TipoSet = 'I' AND TipoRiga = '" . $TipoRiga . "' AND Peso = +1) AS Positivi ";
            $Totali = $objSql->SelectArray($objSql->Query('', $SqlTotali));
            
            $return=true;

            echo $return ? json_encode($Totali) : "error "+$stmt->error;
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
            if ( $stmt = $mysqli->prepare("UPDATE TrainingSet SET ".$colname." = ? WHERE idRiga = ?")) {
                $stmt->bind_param("ss",$value, $id);
                $return = $stmt->execute();
                $stmt->close();

            }
            $mysqli->close();

            echo $return ? "ok" : "error UPDATE TrainingSet SET ".$colname." = ".$value." WHERE idRiga = ".$id;

            break;

        case 'delete':

            $id = $mysqli->real_escape_string(strip_tags($_REQUEST['id']));
            $return=false;
            if ( $stmt = $mysqli->prepare("DELETE FROM TrainingSet  WHERE idRiga = ?")) {
                $stmt->bind_param("s", $id);
                $return = $stmt->execute();
                $stmt->close();
            }
            $mysqli->close();

            echo $return ? "ok" : "error DELETE FROM TrainingSet  WHERE idRiga = "+$id;

            break;

    }









}