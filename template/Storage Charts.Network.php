<?php
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();

if ((isset($_REQUEST['action']) AND trim($_REQUEST['action']) == 'search' ) Or $IdRicerca > -1) {
    //Query Per Code Flower
    {
        $JsonCodeFlower = [];
        if ($IdRicerca == -1) {
            foreach (explode(",", $keyword) as $row) {
                $SqlFlower = "
                                SELECT 
                                    ScreenName,
                                    AVG(Confidence) AS MediaSentiment,
                                    SUM(Confidence) AS SommaSentiment,
                                    COUNT(*) AS ConteggioTweet,
                                    ROUND(AVG(FollowersCount),0) AS MediaFollowers,
                                    SUM(ReTweetCount) AS TotaleRetweet,
                                    ROUND(sqrt ((AVG(FollowersCount) * SUM(ReTweetCount)) + COUNT(*)),0) as Influencer
                                FROM
                                    AnalisiTweets
                                WHERE
                                    Keyword = " . trim($row) . " ";

                if ($StartDate != '') {
                    $SqlFlower .= " AND DATE(TweetTimeStampStringa) >= '$StartDate' ";
                }
                if ($EndDate != '') {
                    $SqlFlower .= " AND DATE(TweetTimeStampStringa) <= '$EndDate' ";
                }

                $SqlFlower .= " AND (Lingua IN(" . $language . ") or Lingua is null)";
                $SqlFlower .= "
                                GROUP BY
                                    ScreenName
                                Having COUNT(*) > 1
                                
                            ";
                //print_r($SqlFlower);
                $JsonCodeFlower[trim($row, "'")] = $objSql->SelectArray($objSql->Query('', $SqlFlower));
            }
        } else {
            $SqlFlower = "
                                SELECT 
                                    ScreenName,
                                    AVG(Confidence) AS MediaSentiment,
                                    SUM(Confidence) AS SommaSentiment,
                                    COUNT(*) AS ConteggioTweet,
                                    ROUND(AVG(FollowersCount),0) AS MediaFollowers,
                                    SUM(ReTweetCount) AS TotaleRetweet,
                                    ROUND(sqrt ((AVG(FollowersCount) * SUM(ReTweetCount)) + COUNT(*)),0) as Influencer
                                FROM
                                    AnalisiTweets
                                WHERE
                                    IdRicerca = '" . $IdRicerca . "' ";
            $SqlFlower .= " AND (Lingua IN(" . $language . ") or Lingua is null)";
            $SqlFlower .= "
                                GROUP BY
                                    ScreenName
                                Having COUNT(*) > 1
                                
                            ";
            //print_r($SqlFlower);
            $JsonCodeFlower[$IdRicerca] = $objSql->SelectArray($objSql->Query('', $SqlFlower));
        }
    }
}
?>


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
    <div class="col-md-12">
        <div id="graphHolder" class="WordCount"></div>
    </div>
</div>

<script type="text/javascript" src="../assets/js/d3.v2.js"></script>

<script type="text/javascript">
    var currentCodeFlower;
    var CurrentWordFormatList = [];

    $(document).ready(function () {
        $("#cbGraphType").change(function () {
            FocusComboEvent(this.value);
        });

        $(window).on('resize', function () {
            FocusComboEvent($("#cbGraphType").val());
            loadNetwork();
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
            loadNetwork();
        }
    });

    $(document).ready(function () {
        loadNetwork();
    });

</script>

<script src="../assets/js/charts/chart.js"></script>
<script src="../assets/js/charts/network.js"></script>


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
                        . ' "color": "' . $objUtils->ColorTable(floatval($row["MediaSentiment"])) . '", '
                        . ' "textcolor": "' . trim("#000") . '", '
                        //. ' "size": ' . ($row['Influencer'] / $MaxInfluencer) * 50 . ', '
                        . ' "size": ' . (($row['Influencer'] == 0) ? 0 : log($row['Influencer'])) . ', '
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
                    . ' "weight": ' . abs(floatval($row["MediaSentiment"])) . ', '
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


</script>


<script type="text/javascript">
    var fill = d3.scale.category20();
</script>
