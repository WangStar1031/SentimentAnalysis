<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT.'/class/connection.class.php');
require(DOCUMENT_ROOT.'/class/common.class.php');
require(DOCUMENT_ROOT.'/class/sql.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objSql = new Sql();


$addScriptHead = '<link href="../assets/plugins/calendar/fullcalendar.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="../assets/plugins/icheck/skins/minimal/minimal.css" rel="stylesheet" type="text/css" media="screen"/>';
echo $objCommon->head($addScriptHead);
echo '</head>';
echo '<body class="">';
echo $objCommon->topBar();
echo '<div class="page-container row-fluid container-fluid">';
echo $objCommon->menuLeft();
echo '<section id="main-content" class=" ">';
echo '<section class="wrapper main-wrapper row" style="">';
echo $objCommon->locationBar();
?>

            <div class="col-lg-12">
                <section class="box nobox ">
                    <div class="content-body">
                        <div class="row">


                            <div id='calendar' class="col-xs-12 col-md-12"></div>
                        </div>
                    </div>
                </section></div>

<?php
echo '</section>';
echo '</section>';
echo '<div class="chatapi-windows "></div>';

echo '</div>';
$addScriptPage = '<script src="../assets/plugins/calendar/moment.min.js" type="text/javascript"></script>
<script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
<script src="../assets/plugins/calendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="../assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>';
echo $objCommon->jsInclude($addScriptPage);
?>
<script>
    $(document).ready(function() {


        $("#delete").click(function() {
           // alert($('#idRicerca').val());
            
            var r = confirm("Confermi eliminazione?");
            if (r == true) {
                $(window.location).attr('href', 'New-Search.php?action=delete&redirect=<?php echo basename($_SERVER['PHP_SELF']); ?>&id='+$('#idRicerca').val());
            } 
           
           // $('#search').submit();
           //<?php echo basename($_SERVER['PHP_SELF']); ?>?action=delete&id=
        })


        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
            },
            // defaultDate: '2017-05-12',
            droppable: false, // this allows things to be dropped onto the calendar !!!
            editable: false,
            navLinks: true, // can click day/week names to navigate views
            eventLimit: true, // allow "more" link when too many events
            selectable: true,
            events: {
                url: '../json/get-events.php',
                error: function() {
                    //alert('carica?');
                    $('#script-warning').show();
                    
                }
            },
            eventClick: function(event) {

                if (event.url) {
                   //apre modale per lettura
                    $('#idRicerca').val(event.idRicerca);
                    $('#modalTitle').html(event.title);
                    $('#modalBody').html(event.description);
                    //$('#eventUrl').attr('href',event.url);
                    $('#fullCalModal').modal();
                        return false;

                };
            },
            select: function(start, end, allDay) {

               
                
  if(compareDate(formatDate(start)))
  {
               $(window.location).attr('href', 'New-Search.php?action=calendar&redirect=<?php echo basename($_SERVER['PHP_SELF']); ?>&start='+formatDate(start));
     }         
                
            },
            loading: function(bool) {
                $('#loading').toggle(bool);
                console.log('ok');
            }
        });


function compareDate(dateEntered) {

var date = dateEntered.substring(0, 2);
var month = dateEntered.substring(3, 5);
var year = dateEntered.substring(6, 10);

var dateToCompare = new Date(year, month - 1, date);
var currentDate = new Date();
//alert(currentDate+' '+dateToCompare);
if (dateToCompare < currentDate) {
    alert("Not possibible!");
    return false
}
else {
    return true;
}
}

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [day,month,year].join('/');
        }


    });



</script>
<div id="fullCalModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body"></div>
            <input type="hidden" name="idRicerca"  id="idRicerca" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="delete" class="btn btn-danger">Delete event</button>
            </div>
        </div>
    </div>
</div>




</div>
<?php
echo '</body>';
echo '</html>';
?>


