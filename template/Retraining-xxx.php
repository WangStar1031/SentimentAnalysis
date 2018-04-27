<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT.'/class/connection.class.php');
require(DOCUMENT_ROOT.'/class/common.class.php');
$objCommon = new Common();
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
Gestione dei twitt 7.2<br>
1. seleziono lingua<br>
2. seleziono modalità (1 o 2)
3. faccio vedere una box riepilogativi con tutti i totali e con semaforo (verde ok, ecc<br>

Se verde posso procedere all'insert

<br>Grid composta da colonne: testo, peso (+1 o -1)
<div class="col-lg-12">
    <section class="box ">
        <header class="panel_header">
            <h2 class="title pull-left">Nuova ricerca</h2>

        </header>

        <div class="content-body">

            <form id="icon_validate" action="#" novalidate="novalidate">

                <div class="row">
                    <div class="col-lg-4 col-xs-12">

                        <div class="form-group has-success">
                            <label class="form-label">Keyword</label>
                            <span class="desc">obbligatorio</span>
                            <div class="controls">
                                <i class="fa fa-check"></i>
                                <input type="text" class="form-control" name="formfield1" aria-required="true" aria-invalid="false" aria-describedby="formfield1-error">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <div class="form-group">
                            <label class="form-label">Lingua:</label>
                            <div class="controls">
                                <select class="form-control m-bot15">
                                    <option>Italiano</option>
                                    <option>Inglese</option>
                                    <option>Tedesco</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-12">

                        <div class="form-group">
                            <label class="form-label">Ricerca pianificata il:</label>
                            <div class="controls">
                                <input type="text" id="schedula" class="form-control datepicker" data-format="mm/dd/yyyy">
                            </div>
                        </div>

                    </div>



                        <div class="pull-right">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                            <button type="button" class="btn btn-default"><i class="fa fa-times"></i> Cancel</button>
                        </div>


                </div>
            </form>


        </div>
    </section></div>


<div class="col-lg-12">
                <section class="box ">
                    <header class="panel_header">
                        <h2 class="title pull-left">Ricerche effettuate </h2>
                        <div class="actions panel_actions pull-right">
                            <a class="box_toggle fa fa-chevron-down"></a>
                            <a class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></a>
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
                                        <th>Lingua</th>
                                        <th>Data</th>
                                        <th>Schedulazione</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="odd gradeX">
                                        <td><span class="label label-success">Success</span></td>
                                        <td><a href="Detail.php"><ins>Pippo</ins></a></td>
                                        <td>IT</td>
                                        <td class="center">26/06/2017 11.42</td>
                                        <td class="center"></td>

                                    </tr>
                                    <tr class="even gradeC">
                                        <td><span class="label label-warning">Warning</span></td>
                                        <td><a href="Detail.php"><ins>Pluto</ins></a></td>
                                        <td>IT</td>
                                        <td class="center">26/06/2017 11.43</td>
                                        <td class="center">28/06/2017 11.44</td>

                                    </tr>
                                    <tr class="odd gradeA">
                                        <td><span class="label label-danger">Danger</span></td>
                                        <td><a href="Detail.php"><ins>Paperino</ins></a></td>
                                        <td>EN</td>
                                        <td class="center">26/06/2017 11.44</td>
                                        <td class="center">26/06/2017 11.44</td>

                                    </tr>

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


    $('#schedula').datepicker({
        locale: 'it',
        format: 'mm/dd/yyyy',
        startDate: '-0d',
        endDate: '+10d',
        autoclose: true,
        useCurrent: true
    });
</script>
<?php
echo '</body>';
echo '</html>';
?>



