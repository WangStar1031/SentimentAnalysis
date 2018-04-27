<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
require(DOCUMENT_ROOT . '/class/utility.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();
print_r($_POST);

$TipoCloudSelezionato = 'Init';

if (isset($_REQUEST['action']) AND trim($_REQUEST['action']) <> '') {

    switch ($_REQUEST['action']) {
        case 'search':

            $language = '';
            if (isset($_POST['language'])) {

                foreach ($_POST['language'] as $key => $value) {

                    $language .= "'" . $value . "',";
                }
                $language = substr($language, 0, -1);
            }

            $keyword = '';
            if (isset($_POST['keyword'])) {
                foreach ($_POST['keyword'] as $key => $value) {
                    $keyword .= "'" . $value . "',";
                }
                $keyword = substr($keyword, 0, -1);
            }



            if (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') {


                $date = str_replace('/', '-', $_POST['schedulaStart']);


                $from = date("Y-m-d", strtotime($date));
                $queryDate = "AND CAST(TweetTimeStampStringa AS DATE) >= " . $from;
            } else {


                $from = date("Y-m") . '-01';
                $queryDate = "AND CAST(TweetTimeStampStringa AS DATE) >= " . $from;
            }


            //Query Per Word Cloud
            {

                $sql = "
                                    SELECT 
                                        Word, 
                                        SUM(WordCount) AS Conteggio 
                                    FROM 
                                        AnalisiTweetsWordCount 
                                    WHERE 
                                        Keyword IN(" . $keyword . ") 
                                ";


                if (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') {

                    $date = str_replace('/', '-', $_POST['schedulaStart']);
                    $from = date("Y-m-d", strtotime($date));
                    $filter .= "AND DATE(TweetTimeStampStringa) BETWEEN '$from' ";

                    if (isset($_POST['schedulaEnd']) && $_POST['schedulaEnd'] <> '') {
                        $date = str_replace('/', '-', $_POST['schedulaEnd']);
                        $to = date("Y-m-d", strtotime($date));
                        $sql .= " AND  '$to' ";
                    } else {
                        $to = date("Y-m-d");
                        $sql .= " AND  '$to' ";
                    }
                }

                $sql .= " AND CodiceLingua IN(" . $language . ") ";

                $sqltcWord = "
                                AND (AT = FALSE) 
                                AND (Hashtag = FALSE) 
                                AND (URL = FALSE) 
                                AND Word NOT IN(SELECT StopWord FROM StopWords WHERE CodiceLingua IN(" . $language . ")) 
                        ";
                $sqltcHashtag = " AND (Hashtag = FALSE) ";
                $sqltcScreenName = " AND (AT = TRUE) ";

                $sqlFineQuery = "
                                    GROUP BY 
                                        Word 
                                    ORDER BY 
                                        Conteggio DESC 
                                    LIMIT 50
                                ";

                $rowsWordCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcWord . $sqlFineQuery));
                $rowsHashCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcHashtag . $sqlFineQuery));
                $rowsNameCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcScreenName . $sqlFineQuery));
            }
            //Query Per Code Flower
            {
                $JsonCodeFlower = [];
                foreach (explode(",", $keyword) as $row) {
                    $SqlFlower = "
                                SELECT
                                    ScreenName,
                                    SUM(Confidence) AS SommaSentiment,
                                    COUNT(*) AS ConteggioTweet,
                                    ROUND(AVG(FollowersCount),0) AS MediaFollowers,
                                    SUM(ReTweetCount) AS TotaleRetweet,
                                    ROUND(sqrt ((AVG(FollowersCount) * SUM(ReTweetCount)) + COUNT(*)),0) as Influencer
                                FROM
                                    AnalisiTweets
                                WHERE
                                    Keyword = " . trim($row) . " ";

                    if (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') {

                        $date = str_replace('/', '-', $_POST['schedulaStart']);
                        $from = date("Y-m-d", strtotime($date));
                        $filter .= " AND DATE(TweetTimeStampStringa) BETWEEN '$from' ";

                        if (isset($_POST['schedulaEnd']) && $_POST['schedulaEnd'] <> '') {
                            $date = str_replace('/', '-', $_POST['schedulaEnd']);
                            $to = date("Y-m-d", strtotime($date));
                            $SqlFlower .= " AND  '$to' ";
                        } else {
                            $to = date("Y-m-d");
                            $SqlFlower .= " AND  '$to' ";
                        }
                    }

                    $SqlFlower .= " AND Lingua IN(" . $language . ") ";
                    $SqlFlower .= "
                                GROUP BY
                                    ScreenName
                                Having COUNT(*) > 1
                            ";
                    //print_r($SqlFlower);
                    $JsonCodeFlower[trim($row, "'")] = $objSql->SelectArray($objSql->Query('', $SqlFlower));
                }
            }

            break;
    }
}

$rowskeyword = $objSql->SelectArray($objSql->Query('tipo_keyword', 'GROUP BY Keyword ORDER BY Keyword'));
$rowslingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));

$addScriptHead = '
        <link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- CALENDAR -->
        <link href="../assets/plugins/timepicker/css/bootstrap-timepicker.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- SELECT -->
        <link href="../assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
    ';

echo $objCommon->head($addScriptHead);
echo '</head>';
echo '<body class="">';
echo $objCommon->topBar();
echo '<div class="page-container row-fluid container-fluid">';
echo $objCommon->menuLeft();
echo '<section id="main-content" class=" ">';
echo '<section class="wrapper main-wrapper row" style=\'\'>';
echo $objCommon->locationBar();
//OTHER SCRIPTS INCLUDED ON THIS PAGE
?>


<form name="search" id="search" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" >

    <div class="col-xs-12">
        <section class="box ">
            <header class="panel_header">       
                <div class="actions panel_actions pull-right">
                    <a class="box_toggle fa fa-chevron-down"></a>

                </div>
            </header>
            <div class="content-body">    
                <div class="row">

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Keywords</label>
                            <span class="desc">required</span>


                            <select required class="required" id="s2example-1" name="keyword[]" multiple required placeholder="Choose your favorite keywords">
                                <option></option>
                                <optgroup label="keyword">
                                    <?php
                                    if (count($rowskeyword) > 0) {
                                        foreach ($rowskeyword as $row) {
                                            if ($row['Keyword'])
                                                echo '<option value"' . trim($row['Keyword']) . '" >' . trim($row['Keyword']) . '</option>';
                                        }
                                    }
                                    ?>
                                </optgroup>
                            </select>

                        </div>
                    </div>


                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Seleziona Lingua</label>

                            <select class="required" id="s2example-2" name="language[]" multiple required placeholder="Choose your favorite language">
                                <option></option>
                                <optgroup label="Lingue">


                                    <?php
                                    if (count($rowslingue) > 0) {
                                        $cont = 0;
                                        foreach ($rowslingue as $row) {
                                            if ($row['CodiceLingua']) {
                                                echo '<option ' . (($cont == 0) ? "selected" : "") . ' value"' . trim($row['CodiceLingua']) . '" >' . trim($row['CodiceLingua']) . '</option>';
                                                $cont++;
                                            }
                                        }
                                    }
                                    ?>

                                </optgroup>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Start date:</label>
                            <div class="controls">
                                <input type="text" name="schedulaStart" id="schedulaStart" class="form-control datepicker" placeholder="Choose your favorite date" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Start Time:</label>
                            <div class="controls">
                                <input type="text" name="oraStart" id="oraStart" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-default-time="24:00 PM" data-show-meridian="false" data-minute-step="5">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">End date:</label>
                            <div class="controls">
                                <input type="text" name="schedulaEnd" id="schedulaEnd" class="form-control datepicker" placeholder="Choose your favorite date" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">End Time:</label>
                            <div class="controls">
                                <input type="text" name="oraEnd" id="oraEnd" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-default-time="24:00 PM" data-show-meridian="false" data-minute-step="5">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <br/>
                        <button type="submit" name="action" value="search" id="submit" class="btn btn-success btn-block"><i class="fa fa-check"></i> search</button>
                    </div>
                    <div class="col-lg-2">
                        <br/>
                        <button type="button" id="reset" class="btn btn-default btn-block"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <div class="col-xs-12">
        <section class="box ">
            <header class="panel_header">       
                <div class="actions panel_actions pull-right">
                    <a class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"> Showme</a>
                    <a class="box_toggle fa fa-chevron-down"></a>
                </div>
            </header>
            <div class="content-body">    
                <div class="row">
                    <div class="col-md-4">  
                        <label class="form-label">Focus Cloud</label>&nbsp;
                        <select class="" id="cbTipoCloud" name="cbTipoCloud">
                            <option selected value="tcWord">Word</option>
                            <option value="tcHashtag">Hashtag</option>
                            <option value="tcScreenName">ScreenName</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-block" id="download-svg">Download SVG</button>
                    </div>
                    <div class="col-md-6">  
                        &nbsp;
                    </div>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-6">  
                        <div id="vis" class="WordCount"></div>


                        <style>
                            .WordCount
                            {
                                min-width: 100%;
                                border-style: solid;
                                border-color: gray;
                                border-width: 1px;
                            }
                            /*g {
                                margin-left: 50px;
                                margin-top: 50px;
                                transform: scale(1.5, 1.5);
                              }

                            rect{ width: 300px; height: 300px}*/
                        </style>

                    </div>

                    <div class="col-md-6">
                        <div id="graphHolder" class="WordCount"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <p style="position: absolute; right: 0; top: 0" id="status"></p>
</form>


<?php
echo '</section>';
echo '</section>';
echo '<div class="chatapi-windows "></div>';
echo '</div>';
$addScriptPage = '
    <script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> 
    <!-- SELECT -->
    <script src="../assets/plugins/select2/select2.min.js" type="text/javascript"></script> 
    <!-- DATAPIKER -->
    <script src="../assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>
    <script src="../assets/plugins/timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>';
echo $objCommon->jsInclude($addScriptPage);
?>


<script type="text/javascript" src="http://d3js.org/d3.v2.js"></script>


<script type="text/javascript">
    var currentCodeFlower;
    var CurrentWordFormatList = [];

    $(document).ready(function () {

        $('#schedulaStart').datepicker({
            locale: 'it',
            format: 'dd/mm/yyyy',

            //endDate: '+10d',
            autoclose: true,
            useCurrent: true
        });

        $('#schedulaEnd').datepicker({
            locale: 'it',
            format: 'dd/mm/yyyy',

            //endDate: '+10d',
            autoclose: true,
            useCurrent: true
        });

        $("#reset").click(function () {
            $('#search').find('input, select, textarea').not("#maxtwitter").not("#ora").not("#users").val('');
        });


        $("#submit").click(function () {
            //alert ('save');
            $('#search').submit();
        });

        $("#cbTipoCloud").change(function () {
            FocusComboEvent(this.value);
        });

        window.onresize = function () {
            FocusComboEvent($("#cbTipoCloud").val());
        };

        function FocusComboEvent(str)
        {
            if (str == 'tcWord')
            {
                CurrentWordFormatList = ListWordCloud;
            } else if (str == 'tcHashtag')
            {
                CurrentWordFormatList = ListHashCloud;
            } else if (str == 'tcScreenName')
            {
                CurrentWordFormatList = ListNameCloud;
            }
            parseText();
        }

    });

</script>

<script src="../assets/js/cotrino.js"></script>


<script type="text/javascript">
    var nodesHash = [];
<?php
if (isset($JsonCodeFlower)) {
    $HashGraph = [];
    $RowIndex = 0;
    //$Keywords = $objUtils->GroupBy($JsonCodeFlower, "Keyword");
//    $ScreenName = $objUtils->GroupBy($JsonCodeFlower, "ScreenName");
//    $NodeHash = array_merge($ScreenName);
    //print_r($JsonCodeFlower);
    foreach ($JsonCodeFlower as $key => $Recordset) {
        //print_r($Recordset);
        $HashGraph[$key] = $RowIndex;
        echo 'nodesHash["' . $key . '"] = ' . $RowIndex . ";\r\n";
        $RowIndex++;
        if (isset($Recordset)) {
            foreach ($Recordset as $row) {
                //print_r($row);
                if ($row["ScreenName"] != "") {
                    if (!isset($HashGraph[$row["ScreenName"]])) {
                        $HashGraph[$row["ScreenName"]] = $RowIndex;
                        echo 'nodesHash["' . $row["ScreenName"] . '"] = ' . $RowIndex . ";\r\n";
                        $RowIndex++;
                    }
                }
            }
        }
    }
}
?>


    var nodesArray = [
<?php
if (isset($JsonCodeFlower)) {

    $RowIndex = 0;
    foreach ($JsonCodeFlower as $key => $Recordset) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        echo '{'
        . ' "label": "' . $key . '", '
        . ' "id": ' . $HashGraph[$key] . ', '
        . ' "color": "' . trim("#cfcfff") . '", '
        . ' "textcolor": "' . trim("#000") . '", '
        . ' "size": 20, '
        . ' "desc": "' . $key . '" '
        . '}';
        if (isset($Recordset)) {
            foreach ($Recordset as $row) {
                if ($row["ScreenName"] != "") {
                    if ($HashGraph[$row["ScreenName"]] > $RowIndex) {
                        echo ",\r\n{"
                        . ' "label": "' . trim($row['ScreenName']) . '", '
                        . ' "id": ' . $HashGraph[$row["ScreenName"]] . ', '
                        . ' "color": "' . $objUtils->ColorTable(floatval($row["SommaSentiment"])) . '", '
                        . ' "textcolor": "' . trim("#000") . '", '
                        //. ' "size": ' . ($row['Influencer'] / $MaxInfluencer) * 50 . ', '
                        . ' "size": ' . log($row['Influencer']) . ', '
                        . ' "desc": "' . trim($row['ScreenName']) . '" '
                        . '}';

                        $RowIndex = $HashGraph[$row["ScreenName"]];
                    }
                }
            }
        }
    }
}
?>
    ];

    var linksArray = [
<?php
if (isset($JsonCodeFlower)) {

    $RowIndex = 0;
    $KeyIndex = 0;
    foreach ($JsonCodeFlower as $key => $Recordset) {
        $KeyIndex = $HashGraph[$key];
        $RowIndex++;
        if (isset($Recordset)) {
            foreach ($Recordset as $row) {
                if ($row["ScreenName"] != "") {
                    if ($RowIndex > 1) {
                        echo ",\r\n";
                    }
                    //echo $row["SommaSentiment"];
                    echo '{'
                    . ' "desc": "' . $key . " --> " . trim($row['ScreenName']) . '", '
                    . ' "source": ' . $KeyIndex . ', '
                    . ' "target": ' . $HashGraph[$row["ScreenName"]] . ', '
                    . ' "weight": ' . (floatval($row["SommaSentiment"]) + 2.0) / 10.0 . ', '
                    . ' "color": "' . trim("#cfcfff") . '" '
                    . '}';
                    $RowIndex++;
                }
            }
        }
    }
}
?>
    ];


    restart();
</script>


<script type="text/javascript">
    var ListWordCloud = [
<?php
if (isset($rowsWordCloud)) {// && count($rowsWordCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsWordCloud as $row) {
        //echo '{"text":"study","size":40},';
        if (trim($row['Word']) != "" && $row['Conteggio'] != "") {
            if ($RowIndex == 0) {
                echo '{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            } else {
                echo ',{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            }
        }

        $RowIndex += 1;
    }
}
?>
    ];

    var ListHashCloud = [
<?php
if (isset($rowsHashCloud)) {// && count($rowsHashCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsHashCloud as $row) {
        //echo '{"text":"study","size":40},';
        if (trim($row['Word']) != "" && $row['Conteggio'] != "") {
            if ($RowIndex == 0) {
                echo '{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            } else {
                echo ',{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            }
        }

        $RowIndex += 1;
    }
}
?>
    ];

    var ListNameCloud = [
<?php
if (isset($rowsNameCloud)) { // && count($rowsNameCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsNameCloud as $row) {
        //echo '{"text":"study","size":40},';
        if (trim($row['Word']) != "" && $row['Conteggio'] != "") {
            if ($RowIndex == 0) {
                echo '{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            } else {
                echo ',{"key": "' . trim($row['Word']) . '", "value": ' . $row['Conteggio'] . '}';
            }
        }

        $RowIndex += 1;
    }
}
?>
    ];

    var fill = d3.scale.category20();
    CurrentWordFormatList = ListWordCloud;
</script>

<script src="../assets/js/cloud2.min.js"></script>


<!-- General section box modal start -->
<div class="modal" id="section-settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog animated bounceInDown">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Section Settings</h4>
            </div>
            <div class="modal-body">

                GRAFICI<br>
                E' possibile premere un bottone "showme" che mi presenterà ulteriuori grafici (8.3,8.4,8.5,8.6) <br>

                3. (6.0 documento) dettaglio pagina twitter<br>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>

            </div>
        </div>
    </div>
</div>
<!-- modal end -->

<?php
echo '</body>';
echo '</html>';
?>



