/**
 *  highlightRow and highlight are used to show a visual feedback. If the row has been successfully modified, it will be highlighted in green. Otherwise, in red
 */
function highlightRow(rowId, bgColor, after)
{
    var rowSelector = $("#" + rowId);
    rowSelector.css("background-color", bgColor);
    rowSelector.fadeTo("normal", 0.5, function() {
        rowSelector.fadeTo("fast", 1, function() {
            rowSelector.css("background-color", '');
        });
    });
}

function highlight(div_id, style) {
    highlightRow(div_id, style == "error" ? "#e5afaf" : style == "warning" ? "#ffcc00" : "#8dc70a");
}

/**
 updateCellValue calls the PHP script that will update the database.
 */
function updateCellValue(databaseGrid, editableGrid, rowIndex, columnIndex, oldValue, newValue, row, onResponse)
{
    var Self = databaseGrid;
    
    $.ajax({
        url: '../ajax/data_Retraining_Management.php?action=update',
        type: 'POST',
        dataType: "html",
        data: {
            tablename : editableGrid.name,
            id: editableGrid.getRowId(rowIndex),
            newvalue: editableGrid.getColumnType(columnIndex) == "boolean" ? (newValue ? 1 : 0) : newValue,
            colname: editableGrid.getColumnName(columnIndex),
            coltype: editableGrid.getColumnType(columnIndex)
        },
        success: function (response)
        {

            // reset old value if failed then highlight row
            var success = onResponse ? onResponse(response) : (response == "ok" || !isNaN(parseInt(response))); // by default, a sucessfull reponse can be "ok" or a database id
            if (!success) editableGrid.setValueAt(rowIndex, columnIndex, oldValue);
            highlight(row.id, success ? "ok" : "error");
            Self.totals();
        },
        error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n"); },
        async: true
    });

}



function DatabaseGrid()
{
    var Self = this;
    
    this.editableGrid = new EditableGrid("TrainingSet", {
        enableSort: true,
        // define the number of row visible by page
        pageSize: 50,
        // Once the table is displayed, we update the paginator state
        tableRendered:  function() {  updatePaginator(this); },
        tableLoaded: function() { datagrid.initializeGrid(this); },
        modelChanged: function(rowIndex, columnIndex, oldValue, newValue, row) {
            updateCellValue(Self, this, rowIndex, columnIndex, oldValue, newValue, row);
            //Self.totals();
        }
    });
    this.fetchGrid();
    
}

DatabaseGrid.prototype.fetchGrid = function()  {
    // call a PHP script to get the data
    this.editableGrid.loadJSON("../ajax/data_Retraining_Management.php?action=load&idLanguage=" + $("#CodiceLingua").val() + "&TipRiga=" + $("#TipoRiga").val());
   
};

DatabaseGrid.prototype.initializeGrid = function(grid) {

    var self = this;

// render for the action column
    grid.setCellRenderer("action", new CellRenderer({
        render: function(cell, id) {
            cell.innerHTML+= "<i onclick=\"datagrid.deleteRow('"+id+"');\" class='fa fa-trash-o red' ></i>";
        }
    }));


    grid.renderGrid("tablecontent", "table table-striped");
};

DatabaseGrid.prototype.deleteRow = function(id)
{

   var self = this;


    if ( confirm('Are you sur you want to delete the Keyword  ' + id )  )
    {

        $.ajax({
            url: '../ajax/data_Retraining_Management.php?action=delete',
            type: 'POST',
            dataType: "html",
            data: {
                id: id
            },
            success: function (response)
            {
                if (response == "ok" )
                {
                    self.editableGrid.removeRow(id);
                    self.totals();
                }
               // else
                 //   alert("error delete "+response+" "+id);
            },
            error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure"); },
            async: true
        });


    }

};


DatabaseGrid.prototype.addRow = function(id)
{

    var self = this;
    
   

    $.ajax({
        url: '../ajax/data_Retraining_Management.php?action=insert',
        type: 'POST',
        dataType: "html",
        data: {
            tablename : self.editableGrid.name,
            CodiceLingua:  $("#CodiceLingua").val(),
            TipoSet:  'I',
            TipoRiga:  $("#TipoRiga").val(),
            Peso:  parseInt($("#Weight").val()),
            Tweet:  $("#TweetWord").val(),
            

        },
        success: function (response)
        {
            if (response == "ok" ) {

                // hide form
                showAddForm();
                //$("#Keyword").val('');
                //$("#ScreenName").val('');

                //alert("Row added : reload model");
                $('#addform').modal('toggle');
                self.fetchGrid();
            }
            else
                alert("error insert"+response);
        },
        error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n"); },
        async: true
    });



};


DatabaseGrid.prototype.totals = function()
{
    var self = this;

    $.ajax({
        url: '../ajax/data_Retraining_Management.php?action=totals',
        type: 'POST',
        dataType: "html",
        data: {
            tablename : self.editableGrid.name,
            CodiceLingua:  $("#CodiceLingua").val(),
            TipoRiga:  $("#TipoRiga").val()
        },
        success: function (response)
        {
            var Totali = $.parseJSON(response);
            $("#TotTot").html(Totali[0].Totale);
            $("#TotNeg").html(Totali[0].Negativi);
            $("#TotPos").html(Totali[0].Positivi);
        },
        error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n"); },
        async: true
    });



};




function updatePaginator(grid, divId)
{
    divId = divId || "paginator";
    var paginator = $("#" + divId).empty();
    var nbPages = grid.getPageCount();

    // get interval
    var interval = grid.getSlidingPageInterval(20);
    if (interval == null) return;

    // get pages in interval (with links except for the current page)
    var pages = grid.getPagesInInterval(interval, function(pageIndex, isCurrent) {
        if (isCurrent) return "<span id='currentpageindex'>" + (pageIndex + 1)  +"</span>";
        return $("<a>").css("cursor", "pointer").html(pageIndex + 1).click(function(event) { grid.setPageIndex(parseInt($(this).html()) - 1); });
    });

    // "first" link
    var link = $("<a class='nobg'>").html("<i class='fa fa-fast-backward'></i>");
    if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
    else link.css("cursor", "pointer").click(function(event) { grid.firstPage(); });
    paginator.append(link);

    // "prev" link
    link = $("<a class='nobg'>").html("<i class='fa fa-backward'></i>");
    if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
    else link.css("cursor", "pointer").click(function(event) { grid.prevPage(); });
    paginator.append(link);

    // pages
    for (p = 0; p < pages.length; p++) paginator.append(pages[p]).append(" ");

    // "next" link
    link = $("<a class='nobg'>").html("<i class='fa fa-forward'>");
    if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
    else link.css("cursor", "pointer").click(function(event) { grid.nextPage(); });
    paginator.append(link);

    // "last" link
    link = $("<a class='nobg'>").html("<i class='fa fa-fast-forward'>");
    if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
    else link.css("cursor", "pointer").click(function(event) { grid.lastPage(); });
    paginator.append(link);
};


function showAddForm() {
    if ( $("#addform").is(':visible') )
        $("#addform").hide();
    else
    {
        $("#addform").show();
    }
}












