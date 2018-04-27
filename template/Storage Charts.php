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
//print_r($_POST);  

$TipoCloudSelezionato = 'Init';
$SelectedGraphPage = "WordCloud";

$GraphPage = [["WordCloud", "Word Cloud"], ["Chord", "Chord"], ["Network", "Network"], ["LineChart", "LineChart"], ["Pie", "Pie"]];

$rowskeyword = $objSql->SelectArray($objSql->Query('tipo_keyword', 'GROUP BY Keyword ORDER BY Keyword'));
$rowslingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));

$IdRicerca = -1;
if (isset($_GET["IdRicerca"]) AND trim($_GET["IdRicerca"]) <> '') {
    $IdRicerca = $_GET["IdRicerca"];

    $SqlRicerca = "Select * From richieste Where idRicerca = " . $IdRicerca;
    $RecordRicerca = $objSql->SelectArray($objSql->Query('', $SqlRicerca))[0];
    $language = $objUtils->GetLanguageShortList($RecordRicerca["ListaCodiceLingua"], $rowslingue);
    //print_r($language);
}


if (isset($_REQUEST['action']) AND trim($_REQUEST['action']) <> '') {

    switch ($_REQUEST['action']) {
        case 'search':

            if ($IdRicerca == -1) {
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

                $StartDate = "";
                $EndDate = "";

                if (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') {
                    $tStartString = $_POST['schedulaStart'] . " " . $_POST['oraStart'];
                    $tStartDate = DateTime::createFromFormat('d/m/Y H:i:s', $tStartString);
                    $StartDate = $tStartDate->format('Y-m-d H:i:s');
                }

                if (isset($_POST['schedulaEnd']) && $_POST['schedulaEnd'] <> '') {
                    $tEndString = $_POST['schedulaEnd'] . " " . $_POST['oraEnd'];
                    $tEndDate = DateTime::createFromFormat('d/m/Y H:i:s', $tEndString);
                    $EndDate = $tEndDate->format('Y-m-d H:i:s');
                }
            }
            if (isset($_POST['cbSelectedGraphType']) && $_POST['cbSelectedGraphType'] <> '') {
                $SelectedGraphPage = $_POST['cbSelectedGraphType'];
            }

//            break;
//        case 'load':
            break;
    }
}

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
         ';
echo $objCommon->jsInclude($addScriptPage);
?>


<script type="text/javascript" src="../assets/js/charts/chart.js"></script>

<form name="search" id="search" action="<?php echo basename($_SERVER['PHP_SELF']) . (($IdRicerca > -1) ? "?IdRicerca=" . $IdRicerca : ""); ?>" method="post" novalidate>

    <div class="col-xs-12">
        <section class="box " <?php
        if ($IdRicerca > -1) {
            echo "hidden";
        }
        ?>>
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


                            <select class="required" id="s2example-1" name="keyword[]" multiple required placeholder="Choose your favorite keywords">
                                <option></option>
                                <optgroup label="keyword">
                                    <?php
                                    if (count($rowskeyword) > 0) {
                                        foreach ($rowskeyword as $row) {
                                            if ($row['Keyword']) {
                                                if (isset($_POST['keyword']) && in_array(trim($row['Keyword']), $_POST['keyword'])) {
                                                    echo '<option selected value="' . trim($row['Keyword']) . '" >' . trim($row['Keyword']) . '</option>';
                                                } else {
                                                    echo '<option value="' . trim($row['Keyword']) . '" >' . trim($row['Keyword']) . '</option>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </optgroup>
                            </select>

                        </div>
                    </div>


                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Language Selection</label>

                            <select class="required" id="s2example-2" name="language[]" multiple required placeholder="Choose your favorite language">
                                <option></option>
                                <optgroup label="Lingue">


                                    <?php
                                    if (count($rowslingue) > 0) {
                                        $cont = 0;
                                        foreach ($rowslingue as $row) {
                                            if ($row['CodiceLingua']) {
                                                if (
                                                        (isset($_POST['language']) && in_array(trim($row['CodiceLingua']), $_POST['language'])) ||
                                                        (!isset($_POST['language']) && $cont == 0)
                                                ) {
                                                    echo '<option selected value="' . trim($row['CodiceLingua']) . '" >' . trim($row['CodiceLingua']) . '</option>';
                                                } else {
                                                    echo '<option value="' . trim($row['CodiceLingua']) . '" >' . trim($row['CodiceLingua']) . '</option>';
                                                }

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
                                <input type="text" name="schedulaStart" id="schedulaStart" class="form-control datepicker" placeholder="Choose your favorite date" value="<?php echo (isset($_POST['schedulaStart']) && $_POST['schedulaStart'] <> '') ? $_POST['schedulaStart'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Start Time:</label>
                            <div class="controls">
                                <input type="text" name="oraStart" id="oraStart" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-show-meridian="false" data-minute-step="5" data-default-time="00:00 AM" value="<?php echo (isset($_POST['oraStart']) && $_POST['oraStart'] <> '') ? $_POST['oraStart'] : ''; ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">End date:</label>
                            <div class="controls">
                                <input type="text" name="schedulaEnd" id="schedulaEnd" class="form-control datepicker" placeholder="Choose your favorite date" value="<?php echo (isset($_POST['schedulaEnd']) && $_POST['schedulaEnd'] <> '') ? $_POST['schedulaEnd'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">End Time:</label>
                            <div class="controls">
                                <input type="text" name="oraEnd" id="oraEnd" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-show-meridian="false" data-minute-step="5" data-default-time="24:59 PM" value="<?php echo (isset($_POST['oraEnd']) && $_POST['oraEnd'] <> '') ? $_POST['oraEnd'] : ''; ?>">
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
        <section class="box " <?php
        if ($IdRicerca == -1) {
            echo "hidden";
        }
        ?>>
            <header class="panel_header">       
                <h4>&nbsp;Selected Keyword : <ins><?php echo $RecordRicerca["Keyword"]; ?></ins></h4>
                <div class="actions panel_actions pull-right">
                    <a class="box_toggle fa fa-chevron-down"></a>
                </div>
            </header>
            <div class="content-body">    
                <table>
                    <tr>
                        <td style="width: 12%;">User : </td>
                        <td style="width: 21%;"><b><?php echo $RecordRicerca["utente"]; ?></b></td>
                        <td style="width: 12%;">Status : </td>
                        <td style="width: 21%;"><b><?php echo $RecordRicerca["Stato"]; ?></b></td>
                        <td style="width: 12%; overflow: hidden;">Request Time : </td>
                        <td style="width: 21%;"><b><?php echo $RecordRicerca["DataOraRichiesta"]; ?></b></td>
                    </tr>
                    <tr>
                        <td>Total Tweets : </td>
                        <td><b><?php echo number_format($RecordRicerca["NumeroTweet"], 0, ',', '.'); ?></b></td>
                        <td>Analized Tweets : </td>
                        <td><b><?php echo number_format($RecordRicerca["TweetAnalizzati"], 0, ',', '.'); ?></b></td>
                        <td>Max Tweet : </td>
                        <td><b><?php echo number_format($RecordRicerca["MaxTweet"], 0, ',', '.'); ?></b></td>
                    </tr>
                    <tr>
                        <td>Languages : </td>
                        <td><b><?php
                                $RowLanguage = "";
                                $arrLanguages = explode(',', $RecordRicerca["ListaCodiceLingua"]);
                                foreach ($arrLanguages as $value) {
                                    $RowLanguage .= $objUtils->GetLanguageList($value, $rowslingue) . "|";
                                }
                                echo trim($RowLanguage, "|");
                                ?></b></td>
                    </tr>   
                </table>
            </div>
        </section>
    </div>

    <div class="col-xs-12">
        <section class="box ">
            <header class="panel_header">       
                <div class="actions panel_actions pull-right">
<!--                    <form name="load" id="load" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" >-->
                    <label class="form-label">Selected Graph Type</label>&nbsp;
                    <select class="" id="cbSelectedGraphType" name="cbSelectedGraphType">
                        <?php
                        if (count($GraphPage) > 0) {
                            $cont = 0;
                            foreach ($GraphPage as $row) {
                                if (
                                        (isset($_POST['cbSelectedGraphType']) && $row[0] == $_POST['cbSelectedGraphType']) ||
                                        (!isset($_POST['cbSelectedGraphType']) && $_POST['cbSelectedGraphType'] <> '' && $cont == 0)
                                ) {
                                    echo '<option selected value="' . trim($row[0]) . '" >' . $row[1] . '</option>';
                                } else {
                                    echo '<option value="' . trim($row[0]) . '" >' . $row[1] . '</option>';
                                }

                                $cont++;
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" name="action" value="search" id="submit" class="btn btn-success"><i class="fa fa-check"></i>Show</button>
                    <!--                    </form>-->
                    <a class="box_toggle fa fa-chevron-down"></a>
                </div>
            </header>
            <div class="content-body">    
                <?php
                include(DOCUMENT_ROOT . '/template/Storage Charts.' . $SelectedGraphPage . '.php');
                ?>
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
?>


<script type="text/javascript">
    var currentCodeFlower;

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

        $("#cbSelectedGraphType").change(function () {
            $('#search').submit();
        });

    });


</script>





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
