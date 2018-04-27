<?php
require_once('../config/config.php');
require_once(DOCUMENT_ROOT . '/class/connection.class.php');
require_once(DOCUMENT_ROOT . '/class/common.class.php');
require_once(DOCUMENT_ROOT . '/class/sql.class.php');
require_once(DOCUMENT_ROOT . '/class/utility.class.php');
// require_once(DOCUMENT_ROOT . '/class/intelligence_db.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objUtils = new Utility();
$objSql = new Sql();
//print_r($_POST);  

$TipoCloudSelezionato = 'Init';
$SelectedGraphPage = "WordCloud";

$GraphPage = [["WordCloud", "Word Cloud"], ["Chord", "Chord"], ["Network", "Network"], ["LineChart", "LineChart"], ["Pie", "Pie"]];

$rowskeyword = $objSql->SelectArray($objSql->Query('tipo_keyword', 'GROUP BY Keyword ORDER BY Keyword'));
$rowslingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));

$IdRicerca = -1;

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
// echo $objCommon->topBar();
echo '<div class="page-container row-fluid container-fluid">';
// echo $objCommon->menuLeft();
echo '<section id="main-content" class=" ">';
echo '<section class="wrapper main-wrapper row" style=\'\'>';
// echo $objCommon->locationBar();
//OTHER SCRIPTS INCLUDED ON THIS PAGE
?>
<div class="col-xs-12">
    <div class="page-title">
        <div class="pull-left">
            <h1 class="title">Intelligence</h1>
        </div>
    </div>
</div>

<?php
$addScriptPage = '
    <script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> 
    <!-- SELECT -->
    <script src="../assets/plugins/select2/select2.min.js" type="text/javascript"></script> 
    <!-- DATAPIKER -->
    <script src="../assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>
    <script src="../assets/plugins/timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="../assets/js/canvas-toBlob.js"></script>
    <script src="../assets/js/FileSaver.min.js"></script>
    <script src="../assets/js/d3.min.js"></script>
         ';
echo $objCommon->jsInclude($addScriptPage);
?>

<!-- Intelligence -->
<link href="./assets/css/Intelligence.css?<?= time(); ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../assets/js/charts/chart.js"></script>

<form name="search" id="search" action="<?php echo basename($_SERVER['PHP_SELF']) . (($IdRicerca > -1) ? "?IdRicerca=" . $IdRicerca : ""); ?>" method="post" novalidate>

    <div class="col-xs-12">
            <div class="row box">    
                <div class="content-body col-lg-9 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-3 col-md-5 col-sm-5 col-xs-8">
                        <div class="form-group">
                            <label class="form-label">ScreenName</label>
                            <div class="controls">
                                <input type="text" name="ScreenName" class="form-control" id="ScreenName" placeholder="ScreenName">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4">
                        <div class="form-group">
                            <label class="form-label">Levels</label>
                            <div class="controls">
                                <input type="text" name="Levels" id="Levels" class="form-control" >
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-4 col-sm-4 col-xs-6">
                        <div class="form-group">
                            <label class="form-label">StartDate:</label>
                            <div class="controls">
                                <input type="text" name="schedulaStart" id="schedulaStart" class="form-control datepicker" placeholder="Choose your favorite date" value="<?php echo (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') ? $_POST['schedulaStart'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6">
                        <div class="form-group">
                            <label class="form-label">StartTime:</label>
                            <div class="controls">
                                <input type="text" name="oraStart" id="oraStart" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-show-meridian="false" data-minute-step="5" data-default-time="00:00 AM" value="<?php echo (isset($_POST['oraStart']) && $_POST['oraStart'] <> '') ? $_POST['oraStart'] : ''; ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-1 col-md-4 col-sm-4 col-xs-6">
                        <div class="form-group">
                            <label class="form-label">EndDate:</label>
                            <div class="controls">
                                <input type="text" name="schedulaEnd" id="schedulaEnd" class="form-control datepicker" placeholder="Choose your favorite date" value="<?php echo (isset($_POST['schedulaEnd']) && $_POST['schedulaEnd'] <> '') ? $_POST['schedulaEnd'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6">
                        <div class="form-group">
                            <label class="form-label">EndTime:</label>
                            <div class="controls">
                                <input type="text" name="oraEnd" id="oraEnd" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-show-meridian="false" data-minute-step="5" data-default-time="24:59 PM" value="<?php echo (isset($_POST['oraEnd']) && $_POST['oraEnd'] <> '') ? $_POST['oraEnd'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                        <br/>
                        <button type="button" name="action" value="search" id="submit" class="btn btn-success btn-block"><i class="fa fa-check"></i>Search</button>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                        <br/>
                        <button type="button" id="reset" class="btn btn-default btn-block"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
                <div class="content-body col-lg-3 col-md-12 col-sm-12 col-xs-12 ShowOption">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="form-group">
                            <label class="form-label">Levels</label>
                            <div class="controls">
                                <input type="text" name="ExtLevels" id="ExtLevels" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="form-group">
                            <label class="form-label">Conversations</label>
                            <div class="controls">
                                <input type="text" name="ExtConvs" id="ExtConvs" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <br/>
                        <button type="button" name="action" value="search" id="Show" class="btn btn-success btn-block"><i class="fa fa-check"></i>Apply</button>
                    </div>
                </div>
            </div>
    </div>
    <div class="row"></div>
    <div class="ChartArea">
        <div id="nodeChart"></div>
    </div>
</form>
<div id="LineDetails" class="HideItem">
    <table>
        <tr>
            <td>From:</td>
            <td id="fromName"></td>
        </tr>
        <tr>
            <td>To:</td>
            <td id="toName"></td>
        </tr>
        <tr>
            <td>Count:</td>
            <td id="linkCount"></td>
        </tr>
    </table>
</div>
<div class="waiting HideItem">
    <img src="../assets/images/giphy.gif">
</div>
    <!-- Intelligence -->
    <script src="../assets/js/Intelligence.js?<?= time(); ?>" type="text/javascript"></script>

<?php
echo '</section>';
echo '</section>';
echo '</div>';
?>


<?php
echo '</body>';
echo '</html>';
?>
