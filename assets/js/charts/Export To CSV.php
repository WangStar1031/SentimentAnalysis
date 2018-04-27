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

$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));
$rows = $objSql->SelectArray($objSql->Query('2.1', 'ORDER BY DataOraRichiesta DESC LIMIT 0,20'));



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
                                <th>ID (Export By Search ID)</th>
                                <th>Keyword (Export By Keyword)</th>
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
                                    if ($row['idRicerca'] And $row['RicercaTerminata'] == 1) {
                                        $RowEcho = '<tr class="odd gradeX">
                                                        <td><a href="SearchToCSV.php?IdRicerca=' . $row['idRicerca'] . '">' . $row['idRicerca'] . '</a></td>';
                                        $RowEcho .= '   <td><a href="KeywordToCSV.php?Keyword=' . $row['Keyword'] . '">' . $row['Keyword'] . '</a></td>';

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