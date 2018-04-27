<?php
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();

if ((isset($_REQUEST['action']) AND trim($_REQUEST['action']) == 'search' ) Or $IdRicerca > -1) {

    ini_set('max_execution_time', 300);
    $sqltcWord = "
                                AND (AT = FALSE) 
                                AND (Hashtag = FALSE) 
                                AND (URL = FALSE) 
                                AND Word NOT IN(SELECT StopWord FROM StopWords WHERE CodiceLingua IN(" . $language . ")) 
                        ";
    $sqltcHashtag = " AND (Hashtag = TRUE) ";
    $sqltcScreenName = " AND (AT = TRUE) ";



    $SqlChordIni = "
                SELECT
                        '1',
                        REPLACE(ScreenName, '''', '_') As Source,
                        REPLACE(Word, '''', '_') As Target,
                        SUM(WordCount) AS Conteggio
                FROM
                        AnalisiTweetsWordCount
                WHERE
                        ScreenName IS NOT NULL 
                        ";

    if ($IdRicerca == -1) {
        $SqlChordData = "";
        if ($StartDate != '') {
            $SqlChordData .= " AND DATE(TweetTimeStampStringa) >= '$StartDate' ";
        }
        if ($EndDate != '') {
            $SqlChordData .= " AND DATE(TweetTimeStampStringa) <= '$EndDate' ";
        }
        $SqlChordLingua = " AND (CodiceLingua IN(" . $language . ") or CodiceLingua is null) ";

        $SqlChordIni .= " AND Keyword IN(" . $keyword . ") ";
        $SqlChordIni .= $SqlChordData;
        $SqlChordIni .= $SqlChordLingua;
    } else {
        $SqlChordIni .= " AND IdRicerca = '" . $IdRicerca . "' ";
    }

    $SqlChordMiddle = "
                GROUP BY
                        ScreenName,
                        Word
                HAVING
                        (SUM(WordCount) > 3)
            
                UNION ALL
                
                SELECT
                        '2',
                        REPLACE(Word, '''', '_') As Source,
                        REPLACE(ScreenName, '''', '_') As Target,
                        SUM(WordCount) AS Conteggio
                FROM
                        AnalisiTweetsWordCount
                WHERE
                        ScreenName IS NOT NULL 
                        ";
    if ($IdRicerca == -1) {
        $SqlChordMiddle .= " AND Keyword IN(" . $keyword . ") ";
        $SqlChordMiddle .= $SqlChordData;
        $SqlChordMiddle .= $SqlChordLingua;
    } else {
        $SqlChordMiddle .= " AND IdRicerca = '" . $IdRicerca . "' ";
    }


    $SqlChordLast = "
                GROUP BY
                        Word,
                        ScreenName
                HAVING
                        (SUM(WordCount) > 3)
                    ";

    $rowsWordChord = $objSql->SelectArray($objSql->Query('', $SqlChordIni . $sqltcWord . $SqlChordMiddle . $sqltcWord . $SqlChordLast));
    $rowsHashChord = $objSql->SelectArray($objSql->Query('', $SqlChordIni . $sqltcHashtag . $SqlChordMiddle . $sqltcHashtag . $SqlChordLast));
    $rowsNameChord = $objSql->SelectArray($objSql->Query('', $SqlChordIni . $sqltcScreenName . $SqlChordMiddle . $sqltcScreenName . $SqlChordLast));
    //print_r($SqlChordIni . $sqltcWord . $SqlChordMiddle . $sqltcWord . $SqlChordLast);
}
?>

<style>
    #tooltip {
        color: white;
        opacity: .9;
        background: #333;
        padding: 5px;
        border: 1px solid lightgrey;
        border-radius: 5px;
        position: absolute;
        z-index: 10;
        visibility: hidden;
        white-space: nowrap;
        pointer-events: none;
    }
    #chordDett {
        color: white;
        opacity: .9;
        background: #333;
        padding: 5px;
        border: 1px solid lightgrey;
        border-radius: 5px;
        margin-top: 0; 
        width: 100%;
        height: 100%;
        z-index: 10;
        visibility: hidden;
        white-space: nowrap;
        pointer-events: none;
    }
</style>


<div id="tooltip"></div>
<div class="row">
    <div class="col-md-4">  
        <label class="form-label">Graph Type</label>&nbsp;
        <select class="" id="cbGraphType" name="cbGraphType">
            <option selected value="tcWord">Word</option>
            <option value="tcHashtag">Hashtag</option>
            <option value="tcScreenName">ScreenName</option>
        </select>
    </div>
    <div class="col-md-8">  
        &nbsp;
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col-md-3">
        <div id="chordDett"></div>
    </div>
    <div class="col-md-9">
        <div id="graphHolder" class="WordCount"></div>
    </div>
</div>


<script type="text/javascript" src="../assets/js/charts/d3.v2.js"></script>

<script type="text/javascript">
    var CurrentChordNodes = [];
    var CurrentChordLinks = [];

    $(document).ready(function () {

        $("#cbGraphType").change(function () {
            FocusComboEvent(this.value);
        });

        $(window).on('resize', function () {
            FocusComboEvent($("#cbGraphType").val());
            loadChord();
        });

        function FocusComboEvent(str)
        {
            if (str === 'tcWord')
            {
                CurrentChordNodes = NodeWordChord;
                CurrentChordLinks = LinkWordChord;
                CurrentNodeHash = nodesHashWord;
            } else if (str === 'tcHashtag')
            {
                CurrentChordNodes = NodeHashChord;
                CurrentChordLinks = LinkHashChord;
                CurrentNodeHash = nodesHashHash;
            } else if (str === 'tcScreenName')
            {
                CurrentChordNodes = NodeNameChord;
                CurrentChordLinks = LinkNameChord;
                CurrentNodeHash = nodesHashName;
            }
            loadChord();
        }
    });

    $(document).ready(function () {
        loadChord();
    });

</script>

<script src="../assets/js/charts/chord.js"></script>


<script type="text/javascript">

    var nodesHashWord = [];

<?php
if (isset($rowsWordChord)) {
    $HashGraphWord = [];
    $RowIndex = 0;
    foreach ($rowsWordChord as $row) {
        if (!array_key_exists($row["Source"], $HashGraphWord)) {
            $HashGraphWord[$row["Source"]] = $RowIndex;
            echo 'nodesHashWord["' . $row["Source"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
        if (!array_key_exists($row["Target"], $HashGraphWord)) {
            $HashGraphWord[$row["Target"]] = $RowIndex;
            echo 'nodesHashWord["' . $row["Target"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
    }
}
?>


    var NodeWordChord = [
<?php
if (isset($HashGraphWord)) {

    $RowIndex = 0;
    foreach ($HashGraphWord as $key => $value) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        echo '{'
        . ' "label": "' . $key . '", '
        . ' "id": ' . $value . ', '
        . ' "color": "' . trim("#111") . '", '
        . ' "textcolor": "' . trim("#111") . '", '
        . ' "size": 20, '
        . ' "desc": "' . $key . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

    var LinkWordChord = [
<?php
if (isset($rowsWordChord)) {

    $RowIndex = 0;
    foreach ($rowsWordChord as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo $row["SommaSentiment"];
        echo '{'
        . ' "desc": "' . $row["Source"] . " --> " . trim($row['Target']) . '", '
        . ' "source": ' . $HashGraphWord[$row["Source"]] . ', '
        . ' "target": ' . $HashGraphWord[$row["Target"]] . ', '
        . ' "weight": ' . $row["Conteggio"] . ', '
        . ' "color": "' . trim("#cfcfff") . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

</script>

<script type="text/javascript">

    var nodesHashHash = [];

<?php
if (isset($rowsHashChord)) {
    $HashGraphHash = [];
    $RowIndex = 0;
    foreach ($rowsHashChord as $row) {
        if (!array_key_exists($row["Source"], $HashGraphHash)) {
            $HashGraphHash[$row["Source"]] = $RowIndex;
            echo 'nodesHashHash["' . $row["Source"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
        if (!array_key_exists($row["Target"], $HashGraphHash)) {
            $HashGraphHash[$row["Target"]] = $RowIndex;
            echo 'nodesHashHash["' . $row["Target"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
    }
}
?>


    var NodeHashChord = [
<?php
if (isset($HashGraphHash)) {

    $RowIndex = 0;
    foreach ($HashGraphHash as $key => $value) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        echo '{'
        . ' "label": "' . $key . '", '
        . ' "id": ' . $value . ', '
        . ' "color": "' . trim("#111") . '", '
        . ' "textcolor": "' . trim("#111") . '", '
        . ' "size": 20, '
        . ' "desc": "' . $key . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

    var LinkHashChord = [
<?php
if (isset($rowsHashChord)) {

    $RowIndex = 0;
    foreach ($rowsHashChord as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo $row["SommaSentiment"];
        echo '{'
        . ' "desc": "' . $row["Source"] . " → " . trim($row['Target']) . '", '
        . ' "source": ' . $HashGraphHash[$row["Source"]] . ', '
        . ' "target": ' . $HashGraphHash[$row["Target"]] . ', '
        . ' "weight": ' . $row["Conteggio"] . ', '
        . ' "color": "' . trim("#cfcfff") . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

</script>

<script type="text/javascript">

    var nodesHashName = [];

<?php
if (isset($rowsNameChord)) {
    $HashGraphName = [];
    $RowIndex = 0;
    foreach ($rowsNameChord as $row) {
        if (!array_key_exists($row["Source"], $HashGraphName)) {
            $HashGraphName[$row["Source"]] = $RowIndex;
            echo 'nodesHashName["' . $row["Source"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
        if (!array_key_exists($row["Target"], $HashGraphName)) {
            $HashGraphName[$row["Target"]] = $RowIndex;
            echo 'nodesHashName["' . $row["Target"] . '"] = ' . $RowIndex . ";\r\n";
            $RowIndex++;
        }
    }
}
?>


    var NodeNameChord = [
<?php
if (isset($HashGraphName)) {

    $RowIndex = 0;
    foreach ($HashGraphName as $key => $value) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        echo '{'
        . ' "label": "' . $key . '", '
        . ' "id": ' . $value . ', '
        . ' "color": "' . trim("#111") . '", '
        . ' "textcolor": "' . trim("#111") . '", '
        . ' "size": 20, '
        . ' "desc": "' . $key . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

    var LinkNameChord = [
<?php
if (isset($rowsNameChord)) {

    $RowIndex = 0;
    foreach ($rowsNameChord as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo $row["SommaSentiment"];
        echo '{'
        . ' "desc": "' . $row["Source"] . " --> " . trim($row['Target']) . '", '
        . ' "source": ' . $HashGraphName[$row["Source"]] . ', '
        . ' "target": ' . $HashGraphName[$row["Target"]] . ', '
        . ' "weight": ' . $row["Conteggio"] . ', '
        . ' "color": "' . trim("#cfcfff") . '" '
        . '}';
        $RowIndex++;
    }
}
?>
    ];

</script>

<script type="text/javascript">
    var fill = d3.scale.category20();
    CurrentChordNodes = NodeWordChord;
    CurrentChordLinks = LinkWordChord;
    CurrentNodeHash = nodesHashWord;
</script>
