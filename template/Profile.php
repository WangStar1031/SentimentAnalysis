<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT.'/class/connection.class.php');
require(DOCUMENT_ROOT.'/class/common.class.php');
$objCommon = new Common();
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

<div class="col-lg-12">
    <div id="wrap">
        <div id="message"></div>
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
<script src="../ajax/data_profile.js" ></script>
';
echo $objCommon->jsInclude($addScriptPage);
?>

<script type="text/javascript">

    var datagrid = new DatabaseGrid();
    window.onload = function() {

        // key typed in the filter field
        $("#filter").keyup(function() {
            datagrid.editableGrid.filter( $(this).val());

            // To filter on some columns, you can set an array of column index
            //datagrid.editableGrid.filter( $(this).val(), [0,3,5]);
        });

        $("#showaddformbutton").click( function()  {
            showAddForm();
        });
        $("#cancelbutton").click( function() {
            showAddForm();
        });

        $("#addbutton").click(function() {
            datagrid.addRow();
        });


    };
</script>
<?php
echo '</body>';
echo '</html>';
?>



