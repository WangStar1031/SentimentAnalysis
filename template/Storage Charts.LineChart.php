<?php
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();


if ((isset($_REQUEST['action']) AND trim($_REQUEST['action']) == 'search' ) Or $IdRicerca > -1) {

    if ($IdRicerca == -1) {
        $FilterFrom = " Keyword IN(" . $keyword . ") ";
    } else {
        $FilterFrom = " IdRicerca = '" . $IdRicerca . "' ";
    }


    $sqlMonth = "
                            SELECT
                                CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), '-01') as Periodo,
                                AVG(Confidence) AS Totale
                            FROM
                                AnalisiTweets
                            WHERE 
                                " . $FilterFrom . "
                                AND Lingua IN(" . $language . ")
                            GROUP BY
                                CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), '-01')
                            ORDER BY
                                CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), '-01')
                            ";

    $sqlHalfMonth = "
                            Select 
                                Periodo, 
                                AVG(Confidence) As Totale
                            FROM 
                                (
                                    SELECT
                                        CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), '-15') as Periodo,
                                        if(SUBSTRING(CAST(TweetTimeStampStringa AS CHAR), 9, 2) <16,Confidence, 0) AS Confidence
                                    FROM
                                        AnalisiTweets
                                    WHERE 
                                        " . $FilterFrom . "
                                        AND Lingua IN(" . $language . ")
                                        
                                    UNION all
                                    
                                    SELECT
                                        CONCAT(LEFT(CAST(TweetTimeStampStringa AS CHAR), 7), '-30') as Periodo,
                                        if(SUBSTRING(CAST(TweetTimeStampStringa AS CHAR), 9, 2) >15,Confidence, 0) AS Confidence
                                    FROM
                                        AnalisiTweets
                                    WHERE 
                                        ". $FilterFrom ."
                                        AND Lingua IN(" . $language . ")
                                ) as TS
                            Group by Periodo
                            Order By Periodo
                        ";
    $sqlDay = "
                            SELECT
                                LEFT(CAST(TweetTimeStampStringa AS CHAR), 10) as Periodo,
                                AVG(Confidence) AS Totale
                            FROM
                                AnalisiTweets
                            WHERE 
                                ". $FilterFrom ."
                                AND Lingua IN(" . $language . ")
                            GROUP BY
                                LEFT(CAST(TweetTimeStampStringa AS CHAR), 10)
                            ORDER BY
                                LEFT(CAST(TweetTimeStampStringa AS CHAR), 10)

                    ";

    $rowsLineMonth = $objSql->SelectArray($objSql->Query('', $sqlMonth));
    $rowsLineHalfMonth = $objSql->SelectArray($objSql->Query('', $sqlHalfMonth));
    $rowsLineDay = $objSql->SelectArray($objSql->Query('', $sqlDay));
}
?>

<style>
    /* tell the SVG path to be a thin blue line without any area fill */
    path {
        stroke: steelblue;
        stroke-width: 1;
        fill: none;
    }

    .axis {
        shape-rendering: crispEdges;
    }
    .x.axis line {
        stroke: lightgrey;
    }
    .x.axis .minor {
        stroke-opacity: .5;
    }
    .x.axis path {
        display: none;
    }
    .y.axis line, .y.axis path {
        fill: none;
        stroke: #000;
    }
</style>


<div class="content-body">    
    <div class="row">
        <div class="col-md-12">  
            <label class="form-label">Period</label>&nbsp;
            <select class="" id="cbPeriod" name="cbPeriod">
                <option selected value="tcMonth">Month</option>
                <option value="tcHalfMonth">Half Month</option>
                <option value="tcDay">Day</option>
            </select>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col-md-12">  
            <div id="graphHolder" class="WordCount"></div>
        </div>
    </div>
</div>



<script type="text/javascript" src="../assets/js/charts/d3.v4.js"></script>


<script type="text/javascript">
    var CurrentLineChart = [];

    $(document).ready(function () {
        $("#cbPeriod").change(function () {
            FocusComboEvent(this.value);
        });

        $(window).on('resize', function () {
            FocusComboEvent($("#cbPeriod").val());
            loadLineChart();
        });

        function FocusComboEvent(str)
        {
            if (str === 'tcMonth')
            {
                CurrentLineChart = LineChartMonth;
            } else if (str === 'tcHalfMonth')
            {
                CurrentLineChart = LineHalfMonth;
            } else if (str === 'tcDay')
            {
                CurrentLineChart = LineDay;
            }
            loadLineChart();
        }

    });

    $(document).ready(function () {
        loadLineChart();
    });

</script>

<script src="../assets/js/charts/chart.js"></script>
<script src="../assets/js/charts/linechart.js"></script>


<script type="text/javascript">
    var LineChartMonth = [
<?php
if (isset($rowsLineMonth)) {// && count($rowsWordCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsLineMonth as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo '{"date": "' . DateTime::createFromFormat('Y/m', trim($row['Periodo'])) . '", "close": ' . $row['Totale'] . '}';
        //echo '{"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        echo '{"index": ' . $RowIndex . ',"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        $RowIndex++;
    }
}
?>
    ];

    var LineHalfMonth = [
<?php
if (isset($rowsLineHalfMonth)) {// && count($rowsHashCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsLineHalfMonth as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo '{"date": "' . DateTime::createFromFormat('Y/m', trim($row['Periodo'])) . '", "close": ' . $row['Totale'] . '}';
        //echo '{"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        echo '{"index": ' . $RowIndex . ',"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        $RowIndex++;
    }
}
?>
    ];

    var LineDay = [
<?php
if (isset($rowsLineDay)) { // && count($rowsNameCloud) > 0)
    $RowIndex = 0;
    foreach ($rowsLineDay as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        //echo '{"date": "' . DateTime::createFromFormat('Y/m', trim($row['Periodo'])) . '", "close": ' . $row['Totale'] . '}';
        //echo '{"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        echo '{"index": ' . $RowIndex . ',"date": "' . trim($row['Periodo']) . '", "close": ' . $row['Totale'] . '}';
        $RowIndex++;
    }
}
?>
    ];

    //var fill = d3.scale.category20();
    CurrentLineChart = LineChartMonth;
</script>
