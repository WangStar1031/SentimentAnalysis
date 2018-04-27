<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
require(DOCUMENT_ROOT . '/class/utility.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objSql = new Sql();
$objUtil = new Utility();

$startDate = '';
if (isset($_REQUEST['action']) AND trim($_REQUEST['action']) <> '') {
    switch ($_REQUEST['action']) {
        case 'calendar':
            if (isset($_REQUEST['start']) AND trim($_REQUEST['start']) <> '') {
                $startDate = $_REQUEST['start'];
            }
            break;

        case 'save':
            $Keyword = $_REQUEST['keyword'];
            $MaxTweet = $_REQUEST['maxtwitter'];
            $utente = $_REQUEST['users'];

            $ListaCodiceLingua = '';
            if (isset($_POST['language'])) {
                //print_r($_POST['language']);
                foreach ($_POST['language'] as $key => $value) {
                    $ListaCodiceLingua .= $value . ",";
                }
                $ListaCodiceLingua = substr($ListaCodiceLingua, 0, -1);
            } else {
                $ListaCodiceLingua = '1';
            }

            $DataOraSchedulazione = '';
            if (isset($_REQUEST['schedula']) && trim($_REQUEST['schedula']) <> '')
                $DataOraSchedulazione = $_REQUEST['schedula'] . ' ' . ((isset($_REQUEST['ora'])) ? $_REQUEST['ora'] : "23:00:00");

            $objConfirm = $objSql->RichiesteInsert($utente, $Keyword, $MaxTweet, $ListaCodiceLingua, $DataOraSchedulazione);

            if ($objConfirm[0] == 'ok') {
                if (isset($_REQUEST['redirect']) && trim($_REQUEST['redirect']) <> '')
                    header(sprintf("Location: Dashboard.php")); //.$_REQUEST['redirect']








                    
//else
                //header(sprintf("Location: New-Search.php"));
            }
            break;

        case 'delete':
            if (isset($_REQUEST['id']) AND trim($_REQUEST['id']) <> '') {
                $objConfirm = $objSql->RichiesteDelete($_REQUEST['id']);

                if ($objConfirm[0] == 'ok') {
                    if (isset($_REQUEST['redirect']))
                        header(sprintf("Location: " . $_REQUEST['redirect']));
                }
            }

            break;
    }
}

if ($startDate == '') {
    $rows = $objSql->SelectArray($objSql->Query('2.1', 'ORDER BY DataOraRichiesta DESC LIMIT 0,20'));
    //print_r($rows);
}

$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));



$addScriptHead = '
<link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
    
    <!-- CALENDAR -->
   
   <link href="../assets/plugins/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" media="screen"/>
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
<div class="col-xs-12">
    <form name="search" id="search" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" >
        <input type="hidden"  name="redirect" id="redirect" value="<?php echo ((isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : ""); ?>">

        <?php
        if (isset($objConfirm) && $objConfirm[0] == 'ok') {
            ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-success alert-dismissible fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <strong>Success:</strong> Well done!</div>
                </div></div>
            <?php
        } else if (isset($objConfirm) && $objConfirm[0] == 'ko') {
            ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-error alert-dismissible fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <strong>Danger:</strong> Oh snap! <?php echo $objConfirm[2]; ?>
                    </div>
                </div></div>
            <?php
        }
        ?>

        <section class="box ">
            <header class="panel_header">       
                <div class="actions panel_actions pull-right">
                    <a class="box_toggle fa fa-chevron-down"></a>
                </div>
            </header>
            <div class="content-body">    
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Keyword</label>
                            <span class="desc">required</span>                       
                            <input type="text" class="form-control required" name="keyword" id="keyword" required placeholder="Choose your favorite keyword">         
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Language Selection</label>

                            <select class="required" id="s2example-2" name="language[]" multiple required placeholder="Choose your favorite language">
                                <option></option>
                                <optgroup label="Lingue">

                                    <?php
                                    if (count($rowsLingue) > 0) {

                                        foreach ($rowsLingue as $row) {
                                            if ($row['LIngua']) {

                                                echo '<option value="' . $row['idCodiceLingua'] . '"> ' . $row['LIngua'] . '</option> ';
                                            }
                                        }
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Schedule:</label>
                            <div class="controls">
                                <input type="text" name="schedula" id="schedula" class="form-control datepicker" data-format="mm/dd/yyyy" placeholder="Choose your favorite date" value="<?php echo $startDate; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Time:</label>
                            <div class="controls">
                                <input type="text" name="ora" id="ora" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-default-time="24:00 PM" data-show-meridian="false" data-minute-step="5">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Max twitter</label>
                            <span class="desc">required</span>
                            <input type="text" class="form-control required" name="maxtwitter" id="maxtwitter" value="100000" required  placeholder="Choose your favorite number twitter">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Users</label>
                            <span class="desc"></span>
                            <select class="form-control required" name="users" id="users" required>
                                <option value="1">User 1</option>
                                <option value="2">User 2</option>
                                <option value="3">User 3</option>
                                <option value="4">User 4</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <a data-toggle="modal" href="#cmpltadminModal-7" class="btn btn-info btn-block">Schedule</a>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <br/>
                        <button type="submit" name="action" value="save" id="submit" class="btn btn-success  btn-block"><i class="fa fa-check"></i> Save</button>
                    </div>
                    <div class="col-md-2">
                        <br/>
                        <button type="button" id="reset" class="btn btn-default btn-block"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
            </div>

        </section>

    </form>
</div>
<?php
if ($startDate == '') {
    ?>
    <div class="col-xs-12">
        <section class="box ">
            <header class="panel_header">
                <form name="search" id="search2" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" >
                    <h2 class="title pull-left"><button type="submit" name="action" value="refresh" id="refresh"><i class="fa fa-refresh"></i></button>&nbsp;Current Searches</h2>
                </form>
                <div class="actions panel_actions pull-right">

                    <a class="box_toggle fa fa-chevron-down"></a>
                    <!--<a class="box_close fa fa-times"></a>-->
                </div>
            </header>
            <div class="content-body">
                <div class="row">
                    <div class="col-xs-12">

                        <table id="example-1" class="table table-striped dt-responsive display">


                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Keyword</th>
                                    <th>Details</th>


                                    <th>Schedule</th>
    <!--                                    <th></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($rows) > 0) {
                                    $results = array();
                                    foreach ($rows as $row) {
                                        if ($row['idRicerca']) {
                                            $RowEcho = '<tr class="odd gradeX">
                                                        <td>' . $row['Stato'] . '</td>
                                                            ';
                                            if ($row['RicercaTerminata'] == 1) {
                                                $RowEcho .= '<td><a href="Storage Charts.php?IdRicerca=' . $row['idRicerca'] . '">' . $row['Keyword'] . '</a></td>';
                                            } else {
                                                $RowEcho .= '<td>' . $row['Keyword'] . '</td>';
                                            }

                                            $RowEcho .= '
                                                        <td>'
                                                    . 'User : ' . $row['utente'] . '<br>'
                                                    . 'Total Tweets : ' . number_format($row['NumeroTweet'], 0, ',', '.') . '<br>'
                                                    . 'Analized Tweets : ' . number_format($row['TweetAnalizzati'], 0, ',', '.') . '<br>'
                                                    //. 'Users: ' . $row['User'] . '<br>'
                                                    . 'Max Tweet : ' . number_format($row['MaxTweet'], 0, ',', '.') . '<br>'
                                                    . 'Search Ended : ' . $row['RicercaTerminata'] . '<br>'
                                                    . 'Language : ' . $objUtil->GetLanguageList($row['ListaCodiceLingua'], $rowsLingue) . '
                                                        </td>
                                                        <td class="center">' . $row['DataOraSchedulazione'] . '</td>'

//                                                        <td class="center">
//                                                            <a href="' . basename($_SERVER['PHP_SELF']) . '?action=delete&id=' . $row['idRicerca'] . '" class="btn btn-danger  btn-xs">Delete</a>
//                                                        </td>
                                                    . '</tr>';
                                            echo $RowEcho;
                                        }
                                    }
                                }
                                ?>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}
?>

<!-- modal start -->
<div class="modal fade col-xs-12" id="cmpltadminModal-7"  tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ripeti</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="field-1" class="control-label">Si ripete:</label>



                            <select class="form-control" id=":2l.frequency" title="Si ripete Ogni settimana
                                    "><option value="0" title="Ogni giorno">Ogni giorno</option><option value="1" title="Tutti i giorni della settimana (dal lunedì al venerdì)">Tutti i giorni della settimana (dal lunedì al venerdì)</option><option value="2" title="Tutti i lunedì, mercoledì e venerdì">Tutti i lunedì, mercoledì e venerdì</option><option value="3" title="Ogni martedì e giovedì">Ogni martedì e giovedì</option><option value="4" title="Ogni settimana">Ogni settimana</option><option value="5" title="Ogni mese">Ogni mese</option><option value="6" title="Ogni anno">Ogni anno</option></select>
                        </div>	

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="field-2" class="control-label">Ripeti ogni (settimane):</label>

                            <select class="form-control" id=":2m.interval" title="Ripeti ogni 1 settimane"><option value="1" selected="selected">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option></select>

                        </div>	

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="field-4" class="control-label">Ripeti il giorno:</label>

                            <div><span class="ep-rec-dow"><input id=":2m.dow1" name="MO" type="checkbox" aria-label="Ripeti di lunedì" title="lunedì"><label for=":2m.dow1" title="lunedì">L</label></span><span class="ep-rec-dow"><input id=":2m.dow2" name="TU" type="checkbox" aria-label="Ripeti di martedì" title="martedì"><label for=":2m.dow2" title="martedì">M</label></span><span class="ep-rec-dow"><input id=":2m.dow3" name="WE" type="checkbox" aria-label="Ripeti di mercoledì" title="mercoledì"><label for=":2m.dow3" title="mercoledì">M</label></span><span class="ep-rec-dow"><input id=":2m.dow4" name="TH" type="checkbox" aria-label="Ripeti di giovedì" title="giovedì"><label for=":2m.dow4" title="giovedì">G</label></span><span class="ep-rec-dow"><input id=":2m.dow5" name="FR" type="checkbox" checked="checked" aria-label="Ripeti di venerdì" title="venerdì"><label for=":2m.dow5" title="venerdì">V</label></span><span class="ep-rec-dow"><input id=":2m.dow6" name="SA" type="checkbox" aria-label="Ripeti di sabato" title="sabato"><label for=":2m.dow6" title="sabato">S</label></span><span class="ep-rec-dow"><input id=":2m.dow0" name="SU" type="checkbox" aria-label="Ripeti di domenica" title="domenica"><label for=":2m.dow0" title="domenica">D</label></span></div>
                        </div>	

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="field-5" class="control-label">Inizia il giorno:</label>

                            <input class="form-control" id=":2m.rstart" size="10" value="30/6/2017" disabled="disabled" aria-labelledby=":2m.rstart-label" autocomplete="off">
                        </div>	

                    </div>

                    <div class="col-md-12">

                        <div class="form-group">
                            <label for="field-6" class="control-label">Termina:</label>

                            <input   id=":2m.endson_never" name="endson" type="radio" checked="checked" aria-label="Non termina mai" title="Non termina mai"><label for=":2m.endson_never" title="Non termina mai">Mai</label></span><span class="ep-rec-ends-opt"><input id=":2m.endson_count" name="endson" type="radio" aria-label="Termina dopo un numero di occorrenze" title="Termina dopo un numero di occorrenze"><label for=":2m.endson_count" title="Termina dopo un numero di occorrenze"><br>
                                    Dopo <input  id=":2m.endson_count_input" size="3" value="" disabled="disabled" title="Occorrenze"> occorrenze</label></span><span class="ep-rec-ends-opt"><input   id=":2m.endson_until" name="endson" type="radio" aria-label="Termina nella data specificata" title="Termina nella data specificata"><label for=":2m.endson_until" title="Termina nella data specificata"><br>In data <input id=":2m.endson_until_input" size="10" value="" title="Data specificata" disabled="disabled" autocomplete="off"></label>
                        </div>	

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->



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
<script>

    $(document).ready(function () {

        $('#schedula').datepicker({
            locale: 'it',
            format: 'dd/mm/yyyy',
            startDate: '-0d',
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
        })


    });


</script>
<?php
echo '</body>';
echo '</html>';
?>



