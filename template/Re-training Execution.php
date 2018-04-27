<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');

$objSql = new Sql();
$objCommon = new Common();

$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));

if (isset($_REQUEST['action']) AND trim($_REQUEST['action']) <> '') {
    switch ($_REQUEST['action']) {
        case 'save':
            $Language = $_REQUEST['CodiceLingua'];
            $Type = $_REQUEST['RetrainingType'];

            $objConfirm = $objSql->RetrainingInsert($Language, $Type);

            if ($objConfirm[0] == 'ok') {
                if (isset($_REQUEST['redirect']) && trim($_REQUEST['redirect']) <> '')
                    header(sprintf("Location: Dashboard.php"));
            }
            break;
    }
}


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
            <h2 class="title pull-left">New Retraining</h2>
        </header>
        <div class="content-body">
            <form name="save" id="save" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
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
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="CodiceLingua" class="control-label">Language</label>
                            <select name="CodiceLingua" id="CodiceLingua" class="form-control" required="true" >
                                <?php
                                if (count($rowsLingue) > 0) {

                                    foreach ($rowsLingue as $row) {
                                        if ($row['LIngua']) {

                                            echo '<option value="' . $row['idCodiceLingua'] . '"> ' . $row['LIngua'] . '</option> ';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="Type" class="control-label">Retraining Type</label>
                            <select class="form-control" id="RetrainingType" name="RetrainingType">
                                <option value="1">Tweet</option>
                                <option value="2">Word + Tweet</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" name="action" value="save" id="submit" class="btn btn-success btn-block"><i class="fa fa-check"></i> Save</button>
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-default btn-block"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
            </form>
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
?>



