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

    $SqlPie = "
                        SELECT
                            Confidence,
                            COUNT(*) AS Conteggio
                        FROM
                            AnalisiTweets
                        WHERE
                            " . $FilterFrom . " 
                            AND (Lingua IN(" . $language . ") or Lingua is null) 
                        ";

    if ($IdRicerca == -1) {
        if ($StartDate != '') {
            $SqlPie .= " AND DATE(TweetTimeStampStringa) >= '$StartDate' ";
        }
        if ($EndDate != '') {
            $SqlPie .= " AND DATE(TweetTimeStampStringa) <= '$EndDate' ";
        }
    }

    $SqlPie .= "
                        GROUP BY
                            Confidence
                        ORDER BY
                            Confidence

                        ";

    $rowsPieData = $objSql->SelectArray($objSql->Query('', $SqlPie));
    //print_r($rowsWordChord);
}
?>

<style>
    .arc text {
        font: 10px sans-serif;
        text-anchor: middle;
    }

    .arc path {
        stroke: #fff;
    }

    path.slice{
        stroke-width:2px;
    }

    polyline{
        opacity: .3;
        stroke: black;
        stroke-width: 2px;
        fill: none;    
    }
</style>

<div id="tooltip"></div>
<div class="row">
    <div class="col-md-12">  
        &nbsp;
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col-md-12">
        <div id="graphHolder" class="WordCount"></div>
    </div>
</div>


<script type="text/javascript" src="../assets/js/charts/d3.v2.js"></script>

<script type="text/javascript">

    $(document).ready(function () {

        $(window).on('resize', function () {
            loadPie();
        });
    });

    $(document).ready(function () {
        loadPie();
    });

</script>

<script src="../assets/js/charts/chart.js"></script>
<script src="../assets/js/charts/pie.js"></script>


<script type="text/javascript">

    var pieData = [

<?php
if (isset($rowsPieData)) {
    $RowIndex = 0;
    foreach ($rowsPieData as $row) {
        if ($RowIndex > 0) {
            echo ",\r\n";
        }
        echo '{'
        . ' "confidence": "' . $row["Confidence"] . '", '
        . ' "conteggio": ' . $row["Conteggio"] . ' '
        . '}';
        $RowIndex++;
    }
}
?>
    ];
</script>
