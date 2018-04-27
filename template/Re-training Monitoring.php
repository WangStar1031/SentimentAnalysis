<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');

$objSql = new Sql();
$objCommon = new Common();


$SqlQuery = "SELECT * FROM RichiesteRiaddestramento Order By InizioElaborazione Desc";

$Elenco = $objSql->SelectArray($objSql->Query('', $SqlQuery));
$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));


//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptHead = '
    <!-- TABLE -->
    <link href="../assets/plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="../assets/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="../assets/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="../assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>
    <!-- SELECT -->
   <link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
   <link href="../assets/plugins/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" media="screen"/>
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
<div class="col-lg-12">
    <section class="box ">
        <header class="panel_header">
                <form name="search" id="search2" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" >
                    <h2 class="title pull-left"><button type="submit" name="action" value="refresh" id="refresh"><i class="fa fa-refresh"></i></button>&nbsp;Re-training List</h2>
                </form>
            <div class="actions panel_actions pull-right">
                <a class="box_toggle fa fa-chevron-down"></a>
                <!--<a class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></a>-->
                <!--<a class="box_close fa fa-times"></a>-->
            </div>
        </header>
        <div class="content-body">
            <div class="row">
                <div class="col-xs-12">
                    <table id="example-1" class="table table-striped dt-responsive display">
                        <thead>
                            <tr>
                                <th>Language</th>
                                <th>Request Time</th>
                                <th>Training Type</th>
                                <th>Current Progress</th>
                                <th>Start Processing</th>
                                <th>Elapsed<br/>(minutes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($Elenco as $Riga) {
                                $StrRiga = '';
                                $StrRiga .= '<tr style="vertical-align: middle"> ';

                                $key = array_search($Riga["idCodiceLingua"], array_column($rowsLingue, 'idCodiceLingua'));

                                if (false !== $key)
                                    $StrRiga .= '     <td>' . $rowsLingue[$key]["LIngua"] . '</td> ';
                                else
                                    $StrRiga .= '     <td>Undefined</td> ';

                                $StrRiga .= '     <td style="text-align: center;"> ' . $Riga["DataOraRichiesta"] . '</td> ';
                                $StrRiga .= '     <td>' . (($Riga["TipoAddestramento"] == "1") ? "Tweet" : "Word + Tweet") . '</td> ';
                                $StrRiga .= '     <td>' . GetProgress($Riga["RecordCorrente"]) . '</td> ';
                                $StrRiga .= '     <td style="text-align: center;"> ' . $Riga["InizioElaborazione"] . '</td> ';
                                $StrRiga .= '     <td style="text-align: right;"> ' . $Riga["DurataMinuti"] . '</td> ';
                                $StrRiga .= '</tr> ';
                                echo $StrRiga;
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
echo '</section>';
echo '</section>';
echo '<div class="chatapi-windows "></div>';
echo '</div>';
//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptPage = '
 <!-- TABLE -->
<script src="../assets/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="../assets/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="../assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="../assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script>

 <!-- CALENDAR -->
 <script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> 
 <script src="../assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>
 
 <script src="../assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
 <script src="../assets/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
 <script src="../assets/js/form-validation.js" type="text/javascript"></script>
';
echo $objCommon->jsInclude($addScriptPage);
echo $objCommon->modal();
?>
<script>

</script>
<?php
echo '</body>';
echo '</html>';

function GetProgress($Valore) {
    $Perc = 0;
    if ($Valore > 0 && $Valore < 100) {
        $Perc = $Valore;
    } else if ($Valore > 99) {
        $Perc = 100;
    }

    $RetVal = ''
            . '<div class="progress progress-lg">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="' . $Perc . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $Perc . '%">
                    ' . $Perc . '%
                </div>
            </div>';
    
    return $RetVal;
}
?>



