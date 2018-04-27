<?php
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();

if ((isset($_REQUEST['action']) AND trim($_REQUEST['action']) == 'search' ) Or $IdRicerca > -1) {


    $sql = "
                                    SELECT 
                                        Word, 
                                        SUM(WordCount) AS Conteggio 
                                    FROM 
                                        AnalisiTweetsWordCount 
                                    WHERE 
                                ";

    if ($IdRicerca == -1) {
        ini_set('max_execution_time', 30);
        $sql .= " Keyword IN(" . $keyword . ") ";
        if ($StartDate != '') {
            $sql .= " AND DATE(TweetTimeStampStringa) >= '$StartDate' ";
        }
        if ($EndDate != '') {
            $sql .= " AND DATE(TweetTimeStampStringa) <= '$EndDate' ";
        }
        $sql .= " AND (CodiceLingua IN(" . $language . ") or CodiceLingua is null)";
    } else {
        ini_set('max_execution_time', 300);
        $sql .= " IdRicerca = '" . $IdRicerca . "' ";
    }



    $sqltcWord = "
                                AND (AT = FALSE) 
                                AND (Hashtag = FALSE) 
                                AND (URL = FALSE) 
                                AND Word NOT IN(SELECT StopWord FROM StopWords WHERE CodiceLingua IN(" . $language . ")) 
                        ";
    $sqltcHashtag = " AND (Hashtag = TRUE) ";
    $sqltcScreenName = " AND (AT = TRUE) ";

    $sqlFineQuery = "
                                    GROUP BY 
                                        Word 
                                    ORDER BY 
                                        Conteggio DESC 
                                    LIMIT 50
                                ";

    //print_r($sql . $sqltcWord . $sqlFineQuery);

    $rowsWordCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcWord . $sqlFineQuery));
    $rowsHashCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcHashtag . $sqlFineQuery));
    $rowsNameCloud = $objSql->SelectArray($objSql->Query('', $sql . $sqltcScreenName . $sqlFineQuery));
}
?>



<div class="content-body">    
    <div class="row">
        <div class="col-md-8">  
            <label class="form-label">Focus Cloud</label>&nbsp;
            <select class="" id="cbTipoCloud" name="cbTipoCloud">
                <option selected value="tcWord">Word</option>
                <option value="tcHashtag">Hashtag</option>
                <option value="tcScreenName">ScreenName</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-success btn-block" id="download-svg">Download Image</button>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col-md-12">  
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
    </div>
</div>



<script type="text/javascript" src="../assets/js/d3.v2.js"></script>


<script type="text/javascript">
    var currentCodeFlower;
    var CurrentWordFormatList = [];

    $(document).ready(function () {
        $("#cbTipoCloud").change(function () {
            FocusComboEvent(this.value);
        });

        $(window).on('resize', function () {
            FocusComboEvent($("#cbTipoCloud").val());
            parseText();
        });

        function FocusComboEvent(str)
        {
            if (str === 'tcWord')
            {
                CurrentWordFormatList = ListWordCloud;
            } else if (str === 'tcHashtag')
            {
                CurrentWordFormatList = ListHashCloud;
            } else if (str === 'tcScreenName')
            {
                CurrentWordFormatList = ListNameCloud;
            }
            parseText();
        }

    });

    $(document).ready(function () {
        parseText();
    });

</script>

<script src="../assets/js/charts/chart.js"></script>
<script src="../assets/js/charts/cloud2.min.js"></script>


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
