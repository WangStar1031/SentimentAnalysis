<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objSql = new Sql();

$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));
//print_r($rowsLingue);
//$objConnection->db_close();
//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptHead = '<link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>';
echo $objCommon->head($addScriptHead);
echo '</head>';
echo '<body class="">';
echo $objCommon->topBar();
echo '<div class="page-container row-fluid container-fluid">';
echo $objCommon->menuLeft();
echo '<section id="main-content" class=" ">';
echo '<section class="wrapper main-wrapper row" style=\'\'>';
echo $objCommon->locationBar();
?>



<div class="col-xs-12">
    <section class="box ">
        <header class="panel_header">       
            <div class="actions panel_actions pull-right">
                <a class="box_toggle fa fa-chevron-down"></a>
            </div>
        </header>
        <div class="content-body">    
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label class="form-label">Filter</label><br/>
                        <input type="text" id="filter" name="filter" placeholder="Filter"  /> 
                        <div id="message"></div>
                    </div>
                </div>



                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label"></label>
                        <a id="showaddformbutton" data-toggle="modal" href="#addform" class="btn btn-default btn-block"><i class="fa fa-plus"></i>&nbsp;Add New Word</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="col-xs-12">
    <div id="wrap">

        <div id="toolbar">


        </div>
        <div class="table-responsive">
            <div id="tablecontent">
                <div class="sk-cube-grid">
                    <div class="sk-cube sk-cube1"></div>
                    <div class="sk-cube sk-cube2"></div>
                    <div class="sk-cube sk-cube3"></div>
                    <div class="sk-cube sk-cube4"></div>
                    <div class="sk-cube sk-cube5"></div>
                    <div class="sk-cube sk-cube6"></div>
                    <div class="sk-cube sk-cube7"></div>
                    <div class="sk-cube sk-cube8"></div>
                    <div class="sk-cube sk-cube9"></div>
                </div>
            </div>
        </div>
        <div id="paginator"></div>
    </div>
</div>



<!-- modal start -->
<div class="modal fade col-xs-12" id="addform"  tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Stopwords</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="field-1" class="control-label">StopWord</label>
                            <input value="" type="text" class="form-control" id="StopWord" name="StopWord" placeholder="StopWord" required="true" />
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field-2" class="control-label">Language</label>
                            <?php
                            ?>
                            <select name="CodiceLingua" id="CodiceLingua" class="form-control" required="true" >
                                <?php
                                if (count($rowsLingue) > 0) {

                                    foreach ($rowsLingue as $row) {
                                        if ($row['LIngua']) {

                                            echo '<option value="' . $row['CodiceLingua'] . '"> ' . $row['LIngua'] . '</option> ';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="addbutton" type="button" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                <button id="cancelbutton" type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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
?>

<?php
//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptPage = '<script src="../assets/js/editablegrid-2.1.0-b25.js"></script>
<script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
<script src="../ajax/data_stopwords.js" ></script>
';
echo $objCommon->jsInclude($addScriptPage);
?>

<script type="text/javascript">



    var datagrid = new DatabaseGrid();
    window.onload = function () {


        datagrid.editableGrid.filter('');

        // key typed in the filter field
        $("#filter").keyup(function () {
            datagrid.editableGrid.filter($(this).val());

            // To filter on some columns, you can set an array of column index
            //datagrid.editableGrid.filter( $(this).val(), [0,3,5]);
        });

        $("#showaddformbutton").click(function () {
            showAddForm();
        });
        $("#cancelbutton").click(function () {
            showAddForm();
        });

        $("#addbutton").click(function () {
            if ($("#StopWord").val() == "")
            {
                $("#StopWord").focus();
                alert('Require!');
                return false;
            }
            datagrid.addRow();

        });


    };




</script>
<?php
echo '</body>';
echo '</html>';
?>



