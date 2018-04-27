/*
 * editablegrid-2.1.0-b25.js
 *
 * This file is part of EditableGrid.
 * http://www.editablegrid.net
 *
 * Copyright (c) 2012 Webismymind SPRL
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.editablegrid.net/page/en/9/license.html
 */
if (typeof _$ == "undefined") {
    function _$(a) {
        return document.getElementById(a)
    }
}

function Column(a) {
    var b = {
        name: "",
        label: "",
        editable: true,
        renderable: true,
        datatype: "string",
        unit: null,
        precision: -1,
        nansymbol: "",
        decimal_point: ",",
        thousands_separator: ".",
        unit_before_number: false,
        bar: true,
        hidden: false,
        headerRenderer: null,
        headerEditor: null,
        cellRenderer: null,
        cellEditor: null,
        cellValidators: [],
        enumProvider: null,
        optionValues: null,
        optionValuesForRender: null,
        columnIndex: -1
    };
    for (var c in b) {
        this[c] = (typeof a == "undefined" || typeof a[c] == "undefined") ? b[c] : a[c]
    }
}
Column.prototype.getOptionValuesForRender = function(b) {
    if (!this.enumProvider) {
        console.log("getOptionValuesForRender called on column " + this.name + " but there is no EnumProvider");
        return null
    }
    var a = this.enumProvider.getOptionValuesForRender(this.editablegrid, this, b);
    return a ? a : this.optionValuesForRender
};
Column.prototype.getOptionValuesForEdit = function(b) {
    if (!this.enumProvider) {
        console.log("getOptionValuesForEdit called on column " + this.name + " but there is no EnumProvider");
        return null
    }
    var a = this.enumProvider.getOptionValuesForEdit(this.editablegrid, this, b);
    return a ? this.editablegrid._convertOptions(a) : this.optionValues
};
Column.prototype.isValid = function(b) {
    for (var a = 0; a < this.cellValidators.length; a++) {
        if (!this.cellValidators[a].isValid(b)) {
            return false
        }
    }
    return true
};
Column.prototype.isNumerical = function() {
    return this.datatype == "double" || this.datatype == "integer"
};

function EnumProvider(a) {
    this.getOptionValuesForRender = function(c, d, e) {
        return null
    };
    this.getOptionValuesForEdit = function(c, d, e) {
        return null
    };
    for (var b in a) {
        this[b] = a[b]
    }
}

function EditableGrid(b, a) {
    if (b) {
        this.init(b, a)
    }
}
EditableGrid.prototype.enableSort = true;
EditableGrid.prototype.enableStore = true;
EditableGrid.prototype.doubleclick = false;
EditableGrid.prototype.editmode = "absolute";
EditableGrid.prototype.editorzoneid = "";
EditableGrid.prototype.allowSimultaneousEdition = false;
EditableGrid.prototype.saveOnBlur = true;
EditableGrid.prototype.invalidClassName = "invalid";
EditableGrid.prototype.ignoreLastRow = false;
EditableGrid.prototype.caption = null;
EditableGrid.prototype.dateFormat = "EU";
EditableGrid.prototype.shortMonthNames = null;
EditableGrid.prototype.smartColorsBar = ["#dc243c", "#4040f6", "#00f629", "#efe100", "#f93fb1", "#6f8183", "#111111"];
EditableGrid.prototype.smartColorsPie = ["#FF0000", "#00FF00", "#0000FF", "#FFD700", "#FF00FF", "#00FFFF", "#800080"];
EditableGrid.prototype.pageSize = 0;
EditableGrid.prototype.serverSide = false;
EditableGrid.prototype.pageCount = 0;
EditableGrid.prototype.totalRowCount = 0;
EditableGrid.prototype.unfilteredRowCount = 0;
EditableGrid.prototype.paginatorAttributes = null;
EditableGrid.prototype.lastURL = null;
EditableGrid.prototype.init = function(c, b) {
    if (typeof c != "string" || (typeof b != "object" && typeof b != "undefined")) {
        alert("The EditableGrid constructor takes two arguments:\n- name (string)\n- config (object)\n\nGot instead " + (typeof c) + " and " + (typeof b) + ".")
    }
    if (typeof b != "undefined") {
        for (var d in b) {
            this[d] = b[d]
        }
    }
    this.Browser = {
        IE: !!(window.attachEvent && navigator.userAgent.indexOf("Opera") === -1),
        Opera: navigator.userAgent.indexOf("Opera") > -1,
        WebKit: navigator.userAgent.indexOf("AppleWebKit/") > -1,
        Gecko: navigator.userAgent.indexOf("Gecko") > -1 && navigator.userAgent.indexOf("KHTML") === -1,
        MobileSafari: !!navigator.userAgent.match(/Apple.*Mobile.*Safari/)
    };
    if (typeof this.detectDir != "function") {
        var a = new Error();
        alert("Who is calling me now ? " + a.stack)
    }
    this.name = c;
    this.columns = [];
    this.data = [];
    this.dataUnfiltered = null;
    this.xmlDoc = null;
    this.sortedColumnName = -1;
    this.sortDescending = false;
    this.baseUrl = this.detectDir();
    this.nbHeaderRows = 1;
    this.lastSelectedRowIndex = -1;
    this.currentPageIndex = 0;
    this.currentFilter = null;
    this.currentContainerid = null;
    this.currentClassName = null;
    this.currentTableid = null;
    if (this.enableSort) {
        this.sortUpImage = new Image();
        if (typeof b != "undefined" && typeof b.sortIconUp != "undefined") {
            this.sortUpImage.src = b.sortIconUp
        } else {
            this.sortUpImage.src = this.baseUrl + "/images/bullet_arrow_up.png"
        }
        this.sortDownImage = new Image();
        if (typeof b != "undefined" && typeof b.sortIconDown != "undefined") {
            this.sortDownImage.src = b.sortIconDown
        } else {
            this.sortDownImage.src = this.baseUrl + "/images/bullet_arrow_down.png"
        }
    }
    this.currentPageIndex = this.localisset("pageIndex") ? parseInt(this.localget("pageIndex")) : 0;
    this.sortedColumnName = this.localisset("sortColumnIndexOrName") ? this.localget("sortColumnIndexOrName") : -1;
    this.sortDescending = this.localisset("sortColumnIndexOrName") && this.localisset("sortDescending") ? this.localget("sortDescending") == "true" : false;
    this.currentFilter = this.localisset("filter") ? this.localget("filter") : null
};
EditableGrid.prototype.tableLoaded = function() {};
EditableGrid.prototype.chartRendered = function() {};
EditableGrid.prototype.tableRendered = function(c, b, a) {};
EditableGrid.prototype.tableSorted = function(a, b) {};
EditableGrid.prototype.tableFiltered = function() {};
EditableGrid.prototype.modelChanged = function(e, b, a, c, d) {};
EditableGrid.prototype.rowSelected = function(b, a) {};
EditableGrid.prototype.isHeaderEditable = function(b, a) {
    return false
};
EditableGrid.prototype.isEditable = function(b, a) {
    return true
};
EditableGrid.prototype.readonlyWarning = function() {};
EditableGrid.prototype.rowRemoved = function(a, b) {};
EditableGrid.prototype.loadXML = function(c, d, a) {
    this.lastURL = c;
    var b = this;
    if (window.ActiveXObject) {
        this.xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        this.xmlDoc.onreadystatechange = function() {
            if (b.xmlDoc.readyState == 4) {
                b.processXML();
                b._callback("xml", d)
            }
        };
        this.xmlDoc.load(this._addUrlParameters(c, a))
    } else {
        if (window.XMLHttpRequest) {
            this.xmlDoc = new XMLHttpRequest();
            this.xmlDoc.onreadystatechange = function() {
                if (this.readyState == 4) {
                    b.xmlDoc = this.responseXML;
                    if (!b.xmlDoc) {
                        console.error("Could not load XML from url '" + c + "'");
                        return false
                    }
                    b.processXML();
                    b._callback("xml", d)
                }
            };
            this.xmlDoc.open("GET", this._addUrlParameters(c, a), true);
            this.xmlDoc.send("")
        } else {
            if (document.implementation && document.implementation.createDocument) {
                this.xmlDoc = document.implementation.createDocument("", "", null);
                this.xmlDoc.onload = function() {
                    b.processXML();
                    b._callback("xml", d)
                };
                this.xmlDoc.load(this._addUrlParameters(c, a))
            } else {
                alert("Cannot load a XML url with this browser!");
                return false
            }
        }
    }
    return true
};
EditableGrid.prototype.loadXMLFromString = function(a) {
    if (window.DOMParser) {
        var b = new DOMParser();
        this.xmlDoc = b.parseFromString(a, "application/xml")
    } else {
        this.xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        this.xmlDoc.async = "false";
        this.xmlDoc.loadXML(a)
    }
    this.processXML()
};
EditableGrid.prototype.processXML = function() {
    with(this) {
        this.data = [];
        this.dataUnfiltered = null;
        this.table = null;
        var metadata = xmlDoc.getElementsByTagName("metadata");
        if (metadata && metadata.length >= 1) {
            this.columns = [];
            var columnDeclarations = metadata[0].getElementsByTagName("column");
            for (var i = 0; i < columnDeclarations.length; i++) {
                var col = columnDeclarations[i];
                var datatype = col.getAttribute("datatype");
                var optionValuesForRender = null;
                var optionValues = null;
                var enumValues = col.getElementsByTagName("values");
                if (enumValues.length > 0) {
                    optionValues = [];
                    optionValuesForRender = {};
                    var enumGroups = enumValues[0].getElementsByTagName("group");
                    if (enumGroups.length > 0) {
                        for (var g = 0; g < enumGroups.length; g++) {
                            var groupOptionValues = [];
                            enumValues = enumGroups[g].getElementsByTagName("value");
                            for (var v = 0; v < enumValues.length; v++) {
                                var _value = enumValues[v].getAttribute("value");
                                var _label = enumValues[v].firstChild ? enumValues[v].firstChild.nodeValue : "";
                                optionValuesForRender[_value] = _label;
                                groupOptionValues.push({
                                    value: _value,
                                    label: _label
                                })
                            }
                            optionValues.push({
                                label: enumGroups[g].getAttribute("label"),
                                values: groupOptionValues
                            })
                        }
                    } else {
                        enumValues = enumValues[0].getElementsByTagName("value");
                        for (var v = 0; v < enumValues.length; v++) {
                            var _value = enumValues[v].getAttribute("value");
                            var _label = enumValues[v].firstChild ? enumValues[v].firstChild.nodeValue : "";
                            optionValuesForRender[_value] = _label;
                            optionValues.push({
                                value: _value,
                                label: _label
                            })
                        }
                    }
                }
                columns.push(new Column({
                    name: col.getAttribute("name"),
                    label: (typeof col.getAttribute("label") == "string" ? col.getAttribute("label") : col.getAttribute("name")),
                    datatype: (col.getAttribute("datatype") ? col.getAttribute("datatype") : "string"),
                    editable: col.getAttribute("editable") == "true",
                    bar: (col.getAttribute("bar") ? col.getAttribute("bar") == "true" : true),
                    hidden: (col.getAttribute("hidden") ? col.getAttribute("hidden") == "true" : false),
                    optionValuesForRender: optionValuesForRender,
                    optionValues: optionValues
                }))
            }
            processColumns()
        }
        var paginator = xmlDoc.getElementsByTagName("paginator");
        if (paginator && paginator.length >= 1) {
            this.paginatorAttributes = null;
            this.pageCount = paginator[0].getAttribute("pagecount");
            this.totalRowCount = paginator[0].getAttribute("totalrowcount");
            this.unfilteredRowCount = paginator[0].getAttribute("unfilteredrowcount")
        }
        var defaultRowId = 1;
        var rows = xmlDoc.getElementsByTagName("row");
        for (var i = 0; i < rows.length; i++) {
            var cellValues = {};
            var cols = rows[i].getElementsByTagName("column");
            for (var j = 0; j < cols.length; j++) {
                var colname = cols[j].getAttribute("name");
                if (!colname) {
                    if (j >= columns.length) {
                        console.error("You defined too many columns for row " + (i + 1))
                    } else {
                        colname = columns[j].name
                    }
                }
                cellValues[colname] = cols[j].firstChild ? cols[j].firstChild.nodeValue : ""
            }
            var rowData = {
                visible: true,
                originalIndex: i,
                id: rows[i].getAttribute("id") ? rows[i].getAttribute("id") : defaultRowId++
            };
            for (var attrIndex = 0; attrIndex < rows[i].attributes.length; attrIndex++) {
                var node = rows[i].attributes.item(attrIndex);
                if (node.nodeName != "id") {
                    rowData[node.nodeName] = node.nodeValue
                }
            }
            rowData.columns = [];
            for (var c = 0; c < columns.length; c++) {
                var cellValue = columns[c].name in cellValues ? cellValues[columns[c].name] : "";
                rowData.columns.push(getTypedValue(c, cellValue))
            }
            data.push(rowData)
        }
    }
    return true
};
EditableGrid.prototype.loadJSON = function(c, e, a) {
    this.lastURL = c;
    var b = this;
    if (!window.XMLHttpRequest) {
        alert("Cannot load a JSON url with this browser!");
        return false
    }
    var d = new XMLHttpRequest();
    d.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (!this.responseText) {
                console.error("Could not load JSON from url '" + c + "'");
                return false
            }
            if (!b.processJSON(this.responseText)) {
                console.error("Invalid JSON data obtained from url '" + c + "'");
                return false
            }
            b._callback("json", e)
        }
    };
    d.open("GET", this._addUrlParameters(c, a), true);
    d.send("");
    return true
};
EditableGrid.prototype._addUrlParameters = function(c, a) {
    var b = c.indexOf("?") >= 0 ? "&" : "?";
    c += b + (new Date().getTime());
    if (!this.serverSide) {
        return c
    }
    return c + "&page=" + (this.currentPageIndex + 1) + "&filter=" + (this.currentFilter ? encodeURIComponent(this.currentFilter) : "") + "&sort=" + (this.sortedColumnName && this.sortedColumnName != -1 ? encodeURIComponent(this.sortedColumnName) : "") + "&asc=" + (this.sortDescending ? 0 : 1) + (a ? "&data_only=1" : "")
};
EditableGrid.prototype._callback = function(a, b) {
    if (b) {
        b.call(this)
    } else {
        if (this.serverSide) {
            this.refreshGrid = function(c) {
                var e = function() {
                    EditableGrid.prototype.refreshGrid.call(this)
                };
                var d = a == "xml" ? this.loadXML : this.loadJSON;
                d.call(this, c || this.lastURL, e, true)
            }
        }
        this.tableLoaded()
    }
};
EditableGrid.prototype.loadJSONFromString = function(a) {
    return this.processJSON(a)
};
EditableGrid.prototype.load = function(a) {
    return this.processJSON(a)
};
EditableGrid.prototype.update = function(b) {
    if (b.data) {
        for (var e = 0; e < b.data.length; e++) {
            var m = b.data[e];
            if (!m.id || !m.values) {
                continue
            }
            var k = this.getRowIndex(m.id);
            var a = this.data[k];
            if (Object.prototype.toString.call(m.values) !== "[object Array]") {
                cellValues = m.values
            } else {
                cellValues = {};
                for (var d = 0; d < m.values.length && d < this.columns.length; d++) {
                    cellValues[this.columns[d].name] = m.values[d]
                }
            }
            for (var h in m) {
                if (h != "id" && h != "values") {
                    a[h] = m[h]
                }
            }
            a.columns = [];
            for (var g = 0; g < this.columns.length; g++) {
                var l = this.columns[g].name in cellValues ? cellValues[this.columns[g].name] : "";
                a.columns.push(this.getTypedValue(g, l))
            }
            var f = this.getRow(k);
            for (var d = 0; d < f.cells.length && d < this.columns.length; d++) {
                if (this.columns[d].renderable) {
                    this.columns[d].cellRenderer._render(k, d, f.cells[d], this.getValueAt(k, d))
                }
            }
            this.tableRendered(this.currentContainerid, this.currentClassName, this.currentTableid)
        }
    }
};
EditableGrid.prototype.processJSON = function(jsonData) {
    if (typeof jsonData == "string") {
        jsonData = eval("(" + jsonData + ")")
    }
    if (!jsonData) {
        return false
    }
    this.data = [];
    this.dataUnfiltered = null;
    this.table = null;
    if (jsonData.metadata) {
        this.columns = [];
        for (var c = 0; c < jsonData.metadata.length; c++) {
            var columndata = jsonData.metadata[c];
            var optionValues = columndata.values ? this._convertOptions(columndata.values) : null;
            var optionValuesForRender = null;
            if (optionValues) {
                var optionValuesForRender = {};
                for (var optionIndex = 0; optionIndex < optionValues.length; optionIndex++) {
                    var optionValue = optionValues[optionIndex];
                    if (typeof optionValue.values == "object") {
                        for (var groupOptionIndex = 0; groupOptionIndex < optionValue.values.length; groupOptionIndex++) {
                            var groupOptionValue = optionValue.values[groupOptionIndex];
                            optionValuesForRender[groupOptionValue.value] = groupOptionValue.label
                        }
                    } else {
                        optionValuesForRender[optionValue.value] = optionValue.label
                    }
                }
            }
            this.columns.push(new Column({
                name: columndata.name,
                label: (columndata.label ? columndata.label : columndata.name),
                datatype: (columndata.datatype ? columndata.datatype : "string"),
                editable: (columndata.editable ? true : false),
                bar: (typeof columndata.bar == "undefined" ? true : (columndata.bar ? true : false)),
                hidden: (typeof columndata.hidden == "undefined" ? false : (columndata.hidden ? true : false)),
                optionValuesForRender: optionValuesForRender,
                optionValues: optionValues
            }))
        }
        this.processColumns()
    }
    if (jsonData.paginator) {
        this.paginatorAttributes = jsonData.paginator;
        this.pageCount = jsonData.paginator.pagecount;
        this.totalRowCount = jsonData.paginator.totalrowcount;
        this.unfilteredRowCount = jsonData.paginator.unfilteredrowcount
    }
    var defaultRowId = 1;
    if (jsonData.data) {
        for (var i = 0; i < jsonData.data.length; i++) {
            var row = jsonData.data[i];
            if (!row.values) {
                continue
            }
            if (Object.prototype.toString.call(row.values) !== "[object Array]") {
                cellValues = row.values
            } else {
                cellValues = {};
                for (var j = 0; j < row.values.length && j < this.columns.length; j++) {
                    cellValues[this.columns[j].name] = row.values[j]
                }
            }
            var rowData = {
                visible: true,
                originalIndex: i,
                id: row.id ? row.id : defaultRowId++
            };
            for (var attributeName in row) {
                if (attributeName != "id" && attributeName != "values") {
                    rowData[attributeName] = row[attributeName]
                }
            }
            rowData.columns = [];
            for (var c = 0; c < this.columns.length; c++) {
                var cellValue = this.columns[c].name in cellValues ? cellValues[this.columns[c].name] : "";
                rowData.columns.push(this.getTypedValue(c, cellValue))
            }
            this.data.push(rowData)
        }
    }
    return true
};
EditableGrid.prototype.processColumns = function() {
    for (var b = 0; b < this.columns.length; b++) {
        var a = this.columns[b];
        a.columnIndex = b;
        a.editablegrid = this;
        this.parseColumnType(a);
        if (!a.enumProvider) {
            a.enumProvider = a.optionValues ? new EnumProvider() : null
        }
        if (!a.cellRenderer) {
            this._createCellRenderer(a)
        }
        if (!a.headerRenderer) {
            this._createHeaderRenderer(a)
        }
        if (!a.cellEditor) {
            this._createCellEditor(a)
        }
        if (!a.headerEditor) {
            this._createHeaderEditor(a)
        }
        this._addDefaultCellValidators(a)
    }
};
EditableGrid.prototype.parseColumnType = function(a) {
    a.unit = null;
    a.precision = -1;
    a.decimal_point = ",";
    a.thousands_separator = ".";
    a.unit_before_number = false;
    a.nansymbol = "";
    if (a.datatype.match(/(.*)\((.*),(.*),(.*),(.*),(.*),(.*)\)$/)) {
        a.datatype = RegExp.$1;
        a.unit = RegExp.$2;
        a.precision = parseInt(RegExp.$3);
        a.decimal_point = RegExp.$4;
        a.thousands_separator = RegExp.$5;
        a.unit_before_number = RegExp.$6;
        a.nansymbol = RegExp.$7;
        a.unit = a.unit.trim();
        a.decimal_point = a.decimal_point.trim();
        a.thousands_separator = a.thousands_separator.trim();
        a.unit_before_number = a.unit_before_number.trim() == "1";
        a.nansymbol = a.nansymbol.trim()
    } else {
        if (a.datatype.match(/(.*)\((.*),(.*),(.*),(.*),(.*)\)$/)) {
            a.datatype = RegExp.$1;
            a.unit = RegExp.$2;
            a.precision = parseInt(RegExp.$3);
            a.decimal_point = RegExp.$4;
            a.thousands_separator = RegExp.$5;
            a.unit_before_number = RegExp.$6;
            a.unit = a.unit.trim();
            a.decimal_point = a.decimal_point.trim();
            a.thousands_separator = a.thousands_separator.trim();
            a.unit_before_number = a.unit_before_number.trim() == "1"
        } else {
            if (a.datatype.match(/(.*)\((.*),(.*),(.*)\)$/)) {
                a.datatype = RegExp.$1;
                a.unit = RegExp.$2.trim();
                a.precision = parseInt(RegExp.$3);
                a.nansymbol = RegExp.$4.trim()
            } else {
                if (a.datatype.match(/(.*)\((.*),(.*)\)$/)) {
                    a.datatype = RegExp.$1.trim();
                    a.unit = RegExp.$2.trim();
                    a.precision = parseInt(RegExp.$3)
                } else {
                    if (a.datatype.match(/(.*)\((.*)\)$/)) {
                        a.datatype = RegExp.$1.trim();
                        var b = RegExp.$2.trim();
                        if (b.match(/^[0-9]*$/)) {
                            a.precision = parseInt(b)
                        } else {
                            a.unit = b
                        }
                    }
                }
            }
        }
    }
    if (a.decimal_point == "comma") {
        a.decimal_point = ","
    }
    if (a.decimal_point == "dot") {
        a.decimal_point = "."
    }
    if (a.thousands_separator == "comma") {
        a.thousands_separator = ","
    }
    if (a.thousands_separator == "dot") {
        a.thousands_separator = "."
    }
    if (isNaN(a.precision)) {
        a.precision = -1
    }
    if (a.unit == "") {
        a.unit = null
    }
    if (a.nansymbol == "") {
        a.nansymbol = null
    }
};
EditableGrid.prototype.getTypedValue = function(a, c) {
    if (c === null) {
        return c
    }
    var b = this.getColumnType(a);
    if (b == "boolean") {
        c = (c && c != 0 && c != "false") ? true : false
    }
    if (b == "integer") {
        c = parseInt(c, 10)
    }
    if (b == "double") {
        c = parseFloat(c)
    }
    if (b == "string") {
        c = "" + c
    }
    return c
};
EditableGrid.prototype.attachToHTMLTable = function(a, c) {
    this.data = [];
    this.dataUnfiltered = null;
    this.table = null;
    if (c) {
        this.columns = c;
        for (var h = 0; h < this.columns.length; h++) {
            this.columns[h].optionValues = this._convertOptions(this.columns[h].optionValues)
        }
        this.processColumns()
    }
    this.table = typeof a == "string" ? _$(a) : a;
    if (!this.table) {
        console.error("Invalid table given: " + a)
    }
    this.tHead = this.table.tHead;
    this.tBody = this.table.tBodies[0];
    if (!this.tBody) {
        this.tBody = document.createElement("TBODY");
        this.table.insertBefore(this.tBody, this.table.firstChild)
    }
    if (!this.tHead) {
        this.tHead = document.createElement("THEAD");
        this.table.insertBefore(this.tHead, this.tBody)
    }
    if (this.tHead.rows.length == 0 && this.tBody.rows.length > 0) {
        this.tHead.appendChild(this.tBody.rows[0])
    }
    this.nbHeaderRows = this.tHead.rows.length;
    var l = this.tHead.rows;
    for (var f = 0; f < l.length; f++) {
        var k = l[f].cells;
        var g = 0;
        for (var e = 0; e < k.length && g < this.columns.length; e++) {
            if (!this.columns[g].label || this.columns[g].label == this.columns[g].name) {
                this.columns[g].label = k[e].innerHTML
            }
            var b = parseInt(k[e].getAttribute("colspan"));
            g += b > 1 ? b : 1
        }
    }
    var l = this.tBody.rows;
    for (var f = 0; f < l.length; f++) {
        var d = [];
        var k = l[f].cells;
        for (var e = 0; e < k.length && e < this.columns.length; e++) {
            d.push(this.getTypedValue(e, k[e].innerHTML))
        }
        this.data.push({
            visible: true,
            originalIndex: f,
            id: l[f].id,
            columns: d
        });
        l[f].rowId = l[f].id;
        l[f].id = this._getRowDOMId(l[f].id)
    }
};
EditableGrid.prototype._createCellRenderer = function(a) {
    a.cellRenderer = a.enumProvider && a.datatype == "list" && typeof MultiselectCellRenderer != "undefined" ? new MultiselectCellRenderer() : a.enumProvider ? new EnumCellRenderer() : a.datatype == "integer" || a.datatype == "double" ? new NumberCellRenderer() : a.datatype == "boolean" ? new CheckboxCellRenderer() : a.datatype == "email" ? new EmailCellRenderer() : a.datatype == "website" || a.datatype == "url" ? new WebsiteCellRenderer() : a.datatype == "date" ? new DateCellRenderer() : new CellRenderer();
    if (a.cellRenderer) {
        a.cellRenderer.editablegrid = this;
        a.cellRenderer.column = a
    }
};
EditableGrid.prototype._createHeaderRenderer = function(a) {
    a.headerRenderer = (this.enableSort && a.datatype != "html") ? new SortHeaderRenderer(a.name) : new CellRenderer();
    if (a.headerRenderer) {
        a.headerRenderer.editablegrid = this;
        a.headerRenderer.column = a
    }
};
EditableGrid.prototype._createCellEditor = function(a) {
    a.cellEditor = a.enumProvider && a.datatype == "list" && typeof MultiselectCellEditor != "undefined" ? new MultiselectCellEditor() : a.enumProvider ? new SelectCellEditor() : a.datatype == "integer" || a.datatype == "double" ? new NumberCellEditor(a.datatype) : a.datatype == "boolean" ? null : a.datatype == "email" ? new TextCellEditor(a.precision) : a.datatype == "website" || a.datatype == "url" ? new TextCellEditor(a.precision) : a.datatype == "date" ? (typeof jQuery == "undefined" || typeof jQuery.datepicker == "undefined" ? new TextCellEditor(a.precision, 10) : new DateCellEditor({
        fieldSize: a.precision,
        maxLength: 10
    })) : new TextCellEditor(a.precision);
    if (a.cellEditor) {
        a.cellEditor.editablegrid = this;
        a.cellEditor.column = a
    }
};
EditableGrid.prototype._createHeaderEditor = function(a) {
    a.headerEditor = new TextCellEditor();
    if (a.headerEditor) {
        a.headerEditor.editablegrid = this;
        a.headerEditor.column = a
    }
};
EditableGrid.prototype.getRowCount = function() {
    return this.data.length
};
EditableGrid.prototype.getUnfilteredRowCount = function() {
    if (this.unfilteredRowCount > 0) {
        return this.unfilteredRowCount
    }
    var a = this.dataUnfiltered == null ? this.data : this.dataUnfiltered;
    return a.length
};
EditableGrid.prototype.getTotalRowCount = function() {
    if (this.totalRowCount > 0) {
        return this.totalRowCount
    }
    return this.getRowCount()
};
EditableGrid.prototype.getColumnCount = function() {
    return this.columns.length
};
EditableGrid.prototype.hasColumn = function(a) {
    return this.getColumnIndex(a) >= 0
};
EditableGrid.prototype.getColumn = function(b) {
    var a = this.getColumnIndex(b);
    if (a < 0) {
        console.error("[getColumn] Column not found with index or name " + b);
        return null
    }
    return this.columns[a]
};
EditableGrid.prototype.getColumnName = function(a) {
    return this.getColumn(a).name
};
EditableGrid.prototype.getColumnLabel = function(a) {
    return this.getColumn(a).label
};
EditableGrid.prototype.getColumnType = function(a) {
    return this.getColumn(a).datatype
};
EditableGrid.prototype.getColumnUnit = function(a) {
    return this.getColumn(a).unit
};
EditableGrid.prototype.getColumnPrecision = function(a) {
    return this.getColumn(a).precision
};
EditableGrid.prototype.isColumnBar = function(b) {
    var a = this.getColumn(b);
    return (a.bar && a.isNumerical())
};
EditableGrid.prototype.isColumnNumerical = function(b) {
    var a = this.getColumn(b);
    return a.isNumerical()
};
EditableGrid.prototype.getValueAt = function(d, b) {
    if (b < 0 || b >= this.columns.length) {
        console.error("[getValueAt] Invalid column index " + b);
        return null
    }
    var a = this.columns[b];
    if (d < 0) {
        return a.label
    }
    if (typeof this.data[d] == "undefined") {
        console.error("[getValueAt] Invalid row index " + d);
        return null
    }
    var c = this.data[d]["columns"];
    return c ? c[b] : null
};
EditableGrid.prototype.getDisplayValueAt = function(d, a) {
    var c = this.getValueAt(d, a);
    if (c !== null) {
        var b = d < 0 ? this.columns[a].headerRenderer : this.columns[a].cellRenderer;
        c = b.getDisplayValue(d, c)
    }
    return c
};
EditableGrid.prototype.setValueAt = function(g, e, h, a) {
    if (typeof a == "undefined") {
        a = true
    }
    var d = null;
    if (e < 0 || e >= this.columns.length) {
        console.error("[setValueAt] Invalid column index " + e);
        return null
    }
    var c = this.columns[e];
    if (g < 0) {
        d = c.label;
        c.label = h
    } else {
        if (typeof this.data[g] == "undefined") {
            console.error("Invalid rowindex " + g);
            return null
        }
        var b = this.data[g]["columns"];
        d = b[e];
        if (b) {
            b[e] = this.getTypedValue(e, h)
        }
    }
    if (a) {
        var f = g < 0 ? c.headerRenderer : c.cellRenderer;
        var j = this.getCell(g, e);
        if (j) {
            f._render(g, e, j, h)
        }
    }
    return d
};
EditableGrid.prototype.getColumnIndex = function(a) {
    if (typeof a == "undefined" || a === "") {
        return -1
    }
    if (!isNaN(a) && a >= 0 && a < this.columns.length) {
        return a
    }
    for (var b = 0; b < this.columns.length; b++) {
        if (this.columns[b].name == a) {
            return b
        }
    }
    return -1
};
EditableGrid.prototype.getRow = function(a) {
    if (a < 0) {
        return this.tHead.rows[a + this.nbHeaderRows]
    }
    if (typeof this.data[a] == "undefined") {
        console.error("[getRow] Invalid row index " + a);
        return null
    }
    return _$(this._getRowDOMId(this.data[a].id))
};
EditableGrid.prototype.getRowId = function(a) {
    return (a < 0 || a >= this.data.length) ? null : this.data[a]["id"]
};
EditableGrid.prototype.getRowIndex = function(a) {
    a = typeof a == "object" ? a.rowId : a;
    for (var b = 0; b < this.data.length; b++) {
        if (this.data[b].id == a) {
            return b
        }
    }
    return -1
};
EditableGrid.prototype.getRowAttribute = function(b, a) {
    if (typeof this.data[b] == "undefined") {
        console.error("Invalid rowindex " + b);
        return null
    }
    return this.data[b][a]
};
EditableGrid.prototype.setRowAttribute = function(c, a, b) {
    this.data[c][a] = b
};
EditableGrid.prototype._getRowDOMId = function(a) {
    return this.currentContainerid != null ? this.name + "_" + a : a
};
EditableGrid.prototype.removeRow = function(a) {
    return this.remove(this.getRowIndex(a))
};
EditableGrid.prototype.remove = function(f) {
    var e = this.data[f].id;
    var d = this.data[f].originalIndex;
    var a = this.dataUnfiltered == null ? this.data : this.dataUnfiltered;
    var c = _$(this._getRowDOMId(e));
    if (c != null) {
        this.tBody.removeChild(c)
    }
    for (var b = 0; b < a.length; b++) {
        if (a[b].originalIndex >= d) {
            a[b].originalIndex--
        }
    }
    this.data.splice(f, 1);
    if (this.dataUnfiltered != null) {
        for (var b = 0; b < this.dataUnfiltered.length; b++) {
            if (this.dataUnfiltered[b].id == e) {
                this.dataUnfiltered.splice(b, 1);
                break
            }
        }
    }
    this.rowRemoved(f, e);
    this.refreshGrid()
};
EditableGrid.prototype.getRowValues = function(c) {
    var b = {};
    for (var a = 0; a < this.getColumnCount(); a++) {
        b[this.getColumnName(a)] = this.getValueAt(c, a)
    }
    return b
};
EditableGrid.prototype.append = function(d, b, a, c) {
    return this.insertAfter(this.data.length - 1, d, b, a, c)
};
EditableGrid.prototype.addRow = function(d, b, a, c) {
    return this.append(d, b, a, c)
};
EditableGrid.prototype._insert = function(p, g, d, j, o, n) {
    var e = null;
    var h = 0;
    var b = this.dataUnfiltered == null ? this.data : this.dataUnfiltered;
    if (typeof this.data[p] != "undefined") {
        e = this.data[p].id;
        h = this.data[p].originalIndex + g
    }
    if (this.currentContainerid == null) {
        var m = this.tBody.insertRow(p + g);
        m.rowId = d;
        m.id = this._getRowDOMId(d);
        for (var l = 0; l < this.columns.length; l++) {
            m.insertCell(l)
        }
    }
    var f = {
        visible: true,
        originalIndex: h,
        id: d
    };
    if (o) {
        for (var k in o) {
            f[k] = o[k]
        }
    }
    f.columns = [];
    for (var l = 0; l < this.columns.length; l++) {
        var q = this.columns[l].name in j ? j[this.columns[l].name] : "";
        f.columns.push(this.getTypedValue(l, q))
    }
    for (var a = 0; a < b.length; a++) {
        if (b[a].originalIndex >= h) {
            b[a].originalIndex++
        }
    }
    this.data.splice(p + g, 0, f);
    if (this.dataUnfiltered != null) {
        if (e === null) {
            this.dataUnfiltered.splice(p + g, 0, f)
        } else {
            for (var a = 0; a < this.dataUnfiltered.length; a++) {
                if (this.dataUnfiltered[a].id == e) {
                    this.dataUnfiltered.splice(a + g, 0, f);
                    break
                }
            }
        }
    }
    this.refreshGrid();
    if (!n) {
        this.sort()
    }
    this.filter()
};
EditableGrid.prototype.insert = function(e, d, b, a, c) {
    if (e < 0) {
        e = 0
    }
    if (e >= this.data.length && this.data.length > 0) {
        return this.insertAfter(this.data.length - 1, d, b, a, c)
    }
    return this._insert(e, 0, d, b, a, c)
};
EditableGrid.prototype.insertAfter = function(e, d, b, a, c) {
    if (e < 0) {
        return this.insert(0, d, b, a, c)
    }
    if (e >= this.data.length) {
        e = this.data.length - 1
    }
    return this._insert(e, 1, d, b, a, c)
};
EditableGrid.prototype.setHeaderRenderer = function(c, d) {
    var b = this.getColumnIndex(c);
    if (b < 0) {
        console.error("[setHeaderRenderer] Invalid column: " + c)
    } else {
        var a = this.columns[b];
        a.headerRenderer = (this.enableSort && a.datatype != "html") ? new SortHeaderRenderer(a.name, d) : d;
        if (d) {
            if (this.enableSort && a.datatype != "html") {
                a.headerRenderer.editablegrid = this;
                a.headerRenderer.column = a
            }
            d.editablegrid = this;
            d.column = a
        }
    }
};
EditableGrid.prototype.setCellRenderer = function(c, d) {
    var b = this.getColumnIndex(c);
    if (b < 0) {
        console.error("[setCellRenderer] Invalid column: " + c)
    } else {
        var a = this.columns[b];
        a.cellRenderer = d;
        if (d) {
            d.editablegrid = this;
            d.column = a
        }
    }
};
EditableGrid.prototype.setCellEditor = function(d, c) {
    var b = this.getColumnIndex(d);
    if (b < 0) {
        console.error("[setCellEditor] Invalid column: " + d)
    } else {
        var a = this.columns[b];
        a.cellEditor = c;
        if (c) {
            c.editablegrid = this;
            c.column = a
        }
    }
};
EditableGrid.prototype.setHeaderEditor = function(d, c) {
    var b = this.getColumnIndex(d);
    if (b < 0) {
        console.error("[setHeaderEditor] Invalid column: " + d)
    } else {
        var a = this.columns[b];
        a.headerEditor = c;
        if (c) {
            c.editablegrid = this;
            c.column = a
        }
    }
};
EditableGrid.prototype.setEnumProvider = function(d, b) {
    var c = this.getColumnIndex(d);
    if (c < 0) {
        console.error("[setEnumProvider] Invalid column: " + d)
    } else {
        var a = this.columns[c].enumProvider != null;
        this.columns[c].enumProvider = b;
        if (!a) {
            this._createCellRenderer(this.columns[c]);
            this._createCellEditor(this.columns[c])
        }
    }
};
EditableGrid.prototype.clearCellValidators = function(b) {
    var a = this.getColumnIndex(b);
    if (a < 0) {
        console.error("[clearCellValidators] Invalid column: " + b)
    } else {
        this.columns[a].cellValidators = []
    }
};
EditableGrid.prototype.addDefaultCellValidators = function(b) {
    var a = this.getColumnIndex(b);
    if (a < 0) {
        console.error("[addDefaultCellValidators] Invalid column: " + b)
    }
    return this._addDefaultCellValidators(this.columns[a])
};
EditableGrid.prototype._addDefaultCellValidators = function(a) {
    if (a.datatype == "integer" || a.datatype == "double") {
        a.cellValidators.push(new NumberCellValidator(a.datatype))
    } else {
        if (a.datatype == "email") {
            a.cellValidators.push(new EmailCellValidator())
        } else {
            if (a.datatype == "website" || a.datatype == "url") {
                a.cellValidators.push(new WebsiteCellValidator())
            } else {
                if (a.datatype == "date") {
                    a.cellValidators.push(new DateCellValidator(this))
                }
            }
        }
    }
};
EditableGrid.prototype.addCellValidator = function(c, a) {
    var b = this.getColumnIndex(c);
    if (b < 0) {
        console.error("[addCellValidator] Invalid column: " + c)
    } else {
        this.columns[b].cellValidators.push(a)
    }
};
EditableGrid.prototype.setCaption = function(a) {
    this.caption = a
};
EditableGrid.prototype.getCell = function(c, a) {
    var b = this.getRow(c);
    if (b == null) {
        console.error("[getCell] Invalid row index " + c);
        return null
    }
    return b.cells[a]
};
EditableGrid.prototype.getCellX = function(b) {
    var a = 0;
    while (b != null && this.isStatic(b)) {
        try {
            a += b.offsetLeft;
            b = b.offsetParent
        } catch (c) {
            b = null
        }
    }
    return a
};
EditableGrid.prototype.getCellY = function(b) {
    var a = 0;
    while (b != null && this.isStatic(b)) {
        try {
            a += b.offsetTop;
            b = b.offsetParent
        } catch (c) {
            b = null
        }
    }
    return a
};
EditableGrid.prototype.getScrollXOffset = function(b) {
    var a = 0;
    while (b != null && typeof b.scrollLeft != "undefined" && this.isStatic(b) && b != document.body) {
        try {
            a += parseInt(b.scrollLeft);
            b = b.parentNode
        } catch (c) {
            b = null
        }
    }
    return a
};
EditableGrid.prototype.getScrollYOffset = function(b) {
    var a = 0;
    while (b != null && typeof b.scrollTop != "undefined" && this.isStatic(b) && b != document.body) {
        try {
            a += parseInt(b.scrollTop);
            b = b.parentNode
        } catch (c) {
            b = null
        }
    }
    return a
};
EditableGrid.prototype._rendergrid = function(containerid, className, tableid) {
    with(this) {
        lastSelectedRowIndex = -1;
        _currentPageIndex = getCurrentPageIndex();
        if (typeof table != "undefined" && table != null) {
            var _data = dataUnfiltered == null ? data : dataUnfiltered;
            _renderHeaders();
            var rows = tBody.rows;
            var skipped = 0;
            var displayed = 0;
            var rowIndex = 0;
            for (var i = 0; i < rows.length; i++) {
                if (!_data[i].visible || (pageSize > 0 && displayed >= pageSize)) {
                    if (rows[i].style.display != "none") {
                        rows[i].style.display = "none";
                        rows[i].hidden_by_editablegrid = true
                    }
                } else {
                    if (skipped < pageSize * _currentPageIndex) {
                        skipped++;
                        if (rows[i].style.display != "none") {
                            rows[i].style.display = "none";
                            rows[i].hidden_by_editablegrid = true
                        }
                    } else {
                        displayed++;
                        var cols = rows[i].cells;
                        if (typeof rows[i].hidden_by_editablegrid != "undefined" && rows[i].hidden_by_editablegrid) {
                            rows[i].style.display = "";
                            rows[i].hidden_by_editablegrid = false
                        }
                        rows[i].rowId = getRowId(rowIndex);
                        rows[i].id = _getRowDOMId(rows[i].rowId);
                        for (var j = 0; j < cols.length && j < columns.length; j++) {
                            if (columns[j].renderable) {
                                columns[j].cellRenderer._render(rowIndex, j, cols[j], getValueAt(rowIndex, j))
                            }
                        }
                    }
                    rowIndex++
                }
            }
            table.editablegrid = this;
            if (doubleclick) {
                table.ondblclick = function(e) {
                    this.editablegrid.mouseClicked(e)
                }
            } else {
                table.onclick = function(e) {
                    this.editablegrid.mouseClicked(e)
                }
            }
        } else {
            if (!containerid) {
                return console.warn("The container ID not specified (renderGrid not called yet ?)")
            }
            if (!_$(containerid)) {
                return console.error("Unable to get element [" + containerid + "]")
            }
            currentContainerid = containerid;
            currentClassName = className;
            currentTableid = tableid;
            var startRowIndex = 0;
            var endRowIndex = getRowCount();
            if (pageSize > 0) {
                startRowIndex = _currentPageIndex * pageSize;
                endRowIndex = Math.min(getRowCount(), startRowIndex + pageSize)
            }
            this.table = document.createElement("table");
            table.className = className || "editablegrid";
            if (typeof tableid != "undefined") {
                table.id = tableid
            }
            while (_$(containerid).hasChildNodes()) {
                _$(containerid).removeChild(_$(containerid).firstChild)
            }
            _$(containerid).appendChild(table);
            if (caption) {
                var captionElement = document.createElement("CAPTION");
                captionElement.innerHTML = this.caption;
                table.appendChild(captionElement)
            }
            this.tHead = document.createElement("THEAD");
            table.appendChild(tHead);
            var trHeader = tHead.insertRow(0);
            var columnCount = getColumnCount();
            for (var c = 0; c < columnCount; c++) {
                var headerCell = document.createElement("TH");
                var td = trHeader.appendChild(headerCell);
                columns[c].headerRenderer._render(-1, c, td, columns[c].label)
            }
            this.tBody = document.createElement("TBODY");
            table.appendChild(tBody);
            var insertRowIndex = 0;
            for (var i = startRowIndex; i < endRowIndex; i++) {
                var tr = tBody.insertRow(insertRowIndex++);
                tr.rowId = data[i]["id"];
                tr.id = this._getRowDOMId(data[i]["id"]);
                for (j = 0; j < columnCount; j++) {
                    var td = tr.insertCell(j);
                    columns[j].cellRenderer._render(i, j, td, getValueAt(i, j))
                }
            }
            _$(containerid).editablegrid = this;
            if (doubleclick) {
                _$(containerid).ondblclick = function(e) {
                    this.editablegrid.mouseClicked(e)
                }
            } else {
                _$(containerid).onclick = function(e) {
                    this.editablegrid.mouseClicked(e)
                }
            }
        }
        tableRendered(containerid, className, tableid)
    }
};
EditableGrid.prototype.renderGrid = function(c, b, a) {
    this._rendergrid(c, b, a);
    if (!this.serverSide) {
        this.sort();
        this.filter()
    }
};
EditableGrid.prototype.refreshGrid = function() {
    if (this.currentContainerid != null) {
        this.table = null
    }
    this._rendergrid(this.currentContainerid, this.currentClassName, this.currentTableid)
};
EditableGrid.prototype._renderHeaders = function() {
    with(this) {
        var rows = tHead.rows;
        for (var i = 0; i < 1; i++) {
            var rowData = [];
            var cols = rows[i].cells;
            var columnIndexInModel = 0;
            for (var j = 0; j < cols.length && columnIndexInModel < columns.length; j++) {
                columns[columnIndexInModel].headerRenderer._render(-1, columnIndexInModel, cols[j], columns[columnIndexInModel].label);
                var colspan = parseInt(cols[j].getAttribute("colspan"));
                columnIndexInModel += colspan > 1 ? colspan : 1
            }
        }
    }
};
EditableGrid.prototype.mouseClicked = function(e) {
    e = e || window.event;
    with(this) {
        var target = e.target || e.srcElement;
        while (target) {
            if (target.tagName == "A" || target.tagName == "TD" || target.tagName == "TH") {
                break
            } else {
                target = target.parentNode
            }
        }
        if (!target || !target.parentNode || !target.parentNode.parentNode || (target.parentNode.parentNode.tagName != "TBODY" && target.parentNode.parentNode.tagName != "THEAD") || target.isEditing) {
            return
        }
        if (target.tagName == "A") {
            return
        }
        var rowIndex = getRowIndex(target.parentNode);
        var columnIndex = target.cellIndex;
        var column = columns[columnIndex];
        if (column) {
            if (rowIndex > -1 && rowIndex != lastSelectedRowIndex) {
                rowSelected(lastSelectedRowIndex, rowIndex);
                lastSelectedRowIndex = rowIndex
            }
            if (!column.editable) {
                readonlyWarning(column)
            } else {
                if (rowIndex < 0) {
                    if (column.headerEditor && isHeaderEditable(rowIndex, columnIndex)) {
                        column.headerEditor.edit(rowIndex, columnIndex, target, column.label)
                    }
                } else {
                    if (column.cellEditor && isEditable(rowIndex, columnIndex)) {
                        column.cellEditor.edit(rowIndex, columnIndex, target, getValueAt(rowIndex, columnIndex))
                    }
                }
            }
        }
    }
};
EditableGrid.prototype.sortColumns = function(headerArray) {
    with(this) {
        newColumns = [];
        newColumnIndeces = [];
        for (var i = 0; i < headerArray.length; i++) {
            columnIndex = this.getColumnIndex(headerArray[i]);
            if (columnIndex == -1) {
                console.error("[sortColumns] Invalid column: " + columnIndex);
                return false
            }
            newColumns[i] = this.columns[columnIndex];
            newColumnIndeces[i] = columnIndex
        }
        this.columns = newColumns;
        for (var i = 0; i < this.data.length; i++) {
            var myData = this.data[i];
            var myDataColumns = myData.columns;
            var newDataColumns = [];
            for (var j = 0; j < myDataColumns.length; j++) {
                newIndex = newColumnIndeces[j];
                newDataColumns[j] = myDataColumns[newIndex]
            }
            this.data[i].columns = newDataColumns
        }
        return true
    }
};
EditableGrid.prototype.sort = function(columnIndexOrName, descending, backOnFirstPage) {
    with(this) {
        if (typeof columnIndexOrName == "undefined" && sortedColumnName === -1) {
            tableSorted(-1, sortDescending);
            return true
        }
        if (typeof columnIndexOrName == "undefined") {
            columnIndexOrName = sortedColumnName
        }
        if (typeof descending == "undefined") {
            descending = sortDescending
        }
        localset("sortColumnIndexOrName", columnIndexOrName);
        localset("sortDescending", descending);
        if (serverSide) {
            return backOnFirstPage ? setPageIndex(0) : refreshGrid()
        }
        var columnIndex = columnIndexOrName;
        if (parseInt(columnIndex, 10) !== -1) {
            columnIndex = this.getColumnIndex(columnIndexOrName);
            if (columnIndex < 0) {
                console.error("[sort] Invalid column: " + columnIndexOrName);
                return false
            }
        }
        if (!enableSort) {
            tableSorted(columnIndex, descending);
            return
        }
        var filterActive = dataUnfiltered != null;
        if (filterActive) {
            data = dataUnfiltered
        }
        var type = columnIndex < 0 ? "" : getColumnType(columnIndex);
        var row_array = [];
        var rowCount = getRowCount();
        for (var i = 0; i < rowCount - (ignoreLastRow ? 1 : 0); i++) {
            row_array.push([columnIndex < 0 ? null : getDisplayValueAt(i, columnIndex), i, data[i].originalIndex])
        }
        row_array.sort(columnIndex < 0 ? unsort : type == "integer" || type == "double" ? sort_numeric : type == "boolean" ? sort_boolean : type == "date" ? sort_date : sort_alpha);
        if (descending) {
            row_array = row_array.reverse()
        }
        if (ignoreLastRow) {
            row_array.push([columnIndex < 0 ? null : getDisplayValueAt(rowCount - 1, columnIndex), rowCount - 1, data[rowCount - 1].originalIndex])
        }
        var _data = data;
        data = [];
        for (var i = 0; i < row_array.length; i++) {
            data.push(_data[row_array[i][1]])
        }
        delete row_array;
        if (filterActive) {
            dataUnfiltered = data;
            data = [];
            for (var r = 0; r < rowCount; r++) {
                if (dataUnfiltered[r].visible) {
                    data.push(dataUnfiltered[r])
                }
            }
        }
        if (backOnFirstPage) {
            setPageIndex(0)
        } else {
            refreshGrid()
        }
        tableSorted(columnIndex, descending);
        return true
    }
};
EditableGrid.prototype.filter = function(filterString, cols) {
    with(this) {
        if (typeof filterString != "undefined") {
            this.currentFilter = filterString;
            this.localset("filter", filterString)
        }
        if (serverSide) {
            return setPageIndex(0)
        }
        if (currentFilter == null || currentFilter == "") {
            if (dataUnfiltered != null) {
                data = dataUnfiltered;
                dataUnfiltered = null;
                for (var r = 0; r < getRowCount(); r++) {
                    data[r].visible = true
                }
                setPageIndex(0);
                tableFiltered()
            }
            return
        }
        var words = currentFilter.toLowerCase().split(" ");
        if (dataUnfiltered != null) {
            data = dataUnfiltered
        }
        var rowCount = getRowCount();
        var columnCount = typeof cols != "undefined" ? cols.length : getColumnCount();
        for (var r = 0; r < rowCount; r++) {
            var row = data[r];
            row.visible = true;
            var rowContent = "";
            for (var c = 0; c < columnCount; c++) {
                if (getColumnType(c) == "boolean") {
                    continue
                }
                var displayValue = getDisplayValueAt(r, typeof cols != "undefined" ? cols[c] : c);
                var value = getValueAt(r, typeof cols != "undefined" ? cols[c] : c);
                rowContent += displayValue + " " + (displayValue == value ? "" : value + " ")
            }
            for (var attributeName in row) {
                if (attributeName != "visible" && attributeName != "originalIndex" && attributeName != "columns") {
                    rowContent += row[attributeName]
                }
            }
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                var match = false;
                var invertMatch = word.startsWith("!");
                if (invertMatch) {
                    word = word.substr(1)
                }
                var colindex = -1;
                var attributeName = null;
                if (word.contains("!=")) {
                    var parts = word.split("!=");
                    colindex = getColumnIndex(parts[0]);
                    if (colindex >= 0) {
                        word = parts[1];
                        invertMatch = !invertMatch
                    } else {
                        if (typeof row[parts[0]] != "undefined") {
                            attributeName = parts[0];
                            word = parts[1];
                            invertMatch = !invertMatch
                        }
                    }
                } else {
                    if (word.contains("=")) {
                        var parts = word.split("=");
                        colindex = getColumnIndex(parts[0]);
                        if (colindex >= 0) {
                            word = parts[1]
                        } else {
                            if (typeof row[parts[0]] != "undefined") {
                                attributeName = parts[0];
                                word = parts[1]
                            }
                        }
                    }
                }
                if (!word.endsWith("!")) {
                    if (colindex >= 0) {
                        match = (getValueAt(r, colindex) + " " + getDisplayValueAt(r, colindex)).trim().toLowerCase().indexOf(word) >= 0
                    } else {
                        if (attributeName !== null) {
                            match = ("" + getRowAttribute(r, attributeName)).trim().toLowerCase().indexOf(word) >= 0
                        } else {
                            match = rowContent.toLowerCase().indexOf(word) >= 0
                        }
                    }
                } else {
                    word = word.substr(0, word.length - 1);
                    if (colindex >= 0) {
                        match = ("" + getDisplayValueAt(r, colindex)).trim().toLowerCase() == word || ("" + getValueAt(r, colindex)).trim().toLowerCase() == word
                    } else {
                        if (attributeName !== null) {
                            match = ("" + getRowAttribute(r, attributeName)).trim().toLowerCase() == word
                        } else {
                            for (var c = 0; c < columnCount; c++) {
                                if (getColumnType(typeof cols != "undefined" ? cols[c] : c) == "boolean") {
                                    continue
                                }
                                if (("" + getDisplayValueAt(r, typeof cols != "undefined" ? cols[c] : c)).trim().toLowerCase() == word || ("" + getValueAt(r, typeof cols != "undefined" ? cols[c] : c)).trim().toLowerCase() == word) {
                                    match = true
                                }
                            }
                        }
                    }
                }
                if (invertMatch ? match : !match) {
                    data[r].visible = false;
                    break
                }
            }
        }
        dataUnfiltered = data;
        data = [];
        for (var r = 0; r < rowCount; r++) {
            if (dataUnfiltered[r].visible) {
                data.push(dataUnfiltered[r])
            }
        }
        setPageIndex(0);
        tableFiltered()
    }
};
EditableGrid.prototype.setPageSize = function(a) {
    this.pageSize = parseInt(a);
    if (isNaN(this.pageSize)) {
        this.pageSize = 0
    }
    this.currentPageIndex = 0;
    this.refreshGrid()
};
EditableGrid.prototype.getPageCount = function() {
    if (this.getRowCount() == 0) {
        return 0
    }
    if (this.pageCount > 0) {
        return this.pageCount
    } else {
        if (this.pageSize <= 0) {
            console.error("getPageCount: no or invalid page size defined (" + this.pageSize + ")");
            return -1
        }
    }
    return Math.ceil(this.getRowCount() / this.pageSize)
};
EditableGrid.prototype.getCurrentPageIndex = function() {
    if (this.pageSize <= 0 && !this.serverSide) {
        return 0
    }
    return Math.max(0, this.currentPageIndex >= this.getPageCount() ? this.getPageCount() - 1 : this.currentPageIndex)
};
EditableGrid.prototype.setPageIndex = function(a) {
    this.currentPageIndex = a;
    this.localset("pageIndex", a);
    this.refreshGrid()
};
EditableGrid.prototype.prevPage = function() {
    if (this.canGoBack()) {
        this.setPageIndex(this.getCurrentPageIndex() - 1)
    }
};
EditableGrid.prototype.firstPage = function() {
    if (this.canGoBack()) {
        this.setPageIndex(0)
    }
};
EditableGrid.prototype.nextPage = function() {
    if (this.canGoForward()) {
        this.setPageIndex(this.getCurrentPageIndex() + 1)
    }
};
EditableGrid.prototype.lastPage = function() {
    if (this.canGoForward()) {
        this.setPageIndex(this.getPageCount() - 1)
    }
};
EditableGrid.prototype.canGoBack = function() {
    return this.getCurrentPageIndex() > 0
};
EditableGrid.prototype.canGoForward = function() {
    return this.getCurrentPageIndex() < this.getPageCount() - 1
};
EditableGrid.prototype.getSlidingPageInterval = function(b) {
    var a = this.getPageCount();
    if (a <= 1) {
        return null
    }
    var f = this.getCurrentPageIndex();
    var c = Math.max(0, f - Math.floor(b / 2));
    var d = Math.min(a - 1, f + Math.floor(b / 2));
    if (d - c < b) {
        var e = b - (d - c + 1);
        c = Math.max(0, c - e);
        d = Math.min(a - 1, d + e)
    }
    return {
        startPageIndex: c,
        endPageIndex: d
    }
};
EditableGrid.prototype.getPagesInInterval = function(b, d) {
    var a = [];
    for (var c = b.startPageIndex; c <= b.endPageIndex; c++) {
        a.push(typeof d == "function" ? d(c, c == this.getCurrentPageIndex()) : c)
    }
    return a
};
var EditableGrid_check_lib = null;
EditableGrid.prototype.checkChartLib = function() {
    try {
        $("dummy").highcharts()
    } catch (a) {
        alert("HighCharts library not loaded!");
        return false
    }
    return true
};
EditableGrid.prototype.hex2rgba = function(b, c) {
    if (typeof c == "undefined") {
        c = 1
    }
    var a = {
        red: parseInt(b.substr(1, 2), 16),
        green: parseInt(b.substr(3, 2), 16),
        blue: parseInt(b.substr(5, 2), 16)
    };
    return "rgba(" + a.red + "," + a.green + "," + a.blue + "," + c + ")"
};
EditableGrid.prototype.getFormattedValue = function(f, c, e) {
    try {
        var b = document.createElement("div");
        var d = this.getColumn(c).cellRenderer;
        d._render(f, c, b, e);
        return b.innerHTML
    } catch (a) {
        return e
    }
};
EditableGrid.prototype.renderBarChart = function(divId, title, labelColumnIndexOrName, options) {
    this.legend = null;
    this.bgColor = null;
    this.alpha = 0.9;
    this.limit = 0;
    this.bar3d = false;
    this.rotateXLabels = 0;
    with(this) {
        if (EditableGrid_check_lib === null) {
            EditableGrid_check_lib = checkChartLib()
        }
        if (!EditableGrid_check_lib) {
            return false
        }
        if (options) {
            for (var p in options) {
                this[p] = options[p]
            }
        }
        labelColumnIndexOrName = labelColumnIndexOrName || 0;
        var cLabel = getColumnIndex(labelColumnIndexOrName);
        var columnCount = getColumnCount();
        var rowCount = getRowCount() - (ignoreLastRow ? 1 : 0);
        if (limit > 0 && rowCount > limit) {
            rowCount = limit
        }
        var chart = {
            chart: {
                type: "column",
                backgroundColor: bgColor,
                plotBackgroundColor: bgColor,
                options3d: {
                    enabled: bar3d
                }
            },
            plotOptions: {
                column: {
                    groupPadding: 0.1,
                    pointPadding: 0.1,
                    borderWidth: 0
                }
            },
            credits: {
                enabled: false
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: "{series.name}: <b>{point.formattedValue}</b>"
            }
        };
        chart.xAxis = {
            title: {
                text: legend || getColumnLabel(labelColumnIndexOrName)
            },
            labels: {
                rotation: rotateXLabels
            }
        };
        chart.xAxis.categories = [];
        for (var r = 0; r < rowCount; r++) {
            if (getRowAttribute(r, "skip") == "1") {
                continue
            }
            var label = getRowAttribute(r, "barlabel");
            chart.xAxis.categories.push(label ? label : getValueAt(r, cLabel))
        }
        chart.series = [];
        var minvalue = 0;
        var maxvalue = 0;
        for (var c = 0; c < columnCount; c++) {
            if (!isColumnBar(c)) {
                continue
            }
            var serie = {
                name: getColumnLabel(c),
                color: hex2rgba(smartColorsBar[chart.series.length % smartColorsBar.length], alpha),
                data: []
            };
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var value = getValueAt(r, c);
                if (value > maxvalue) {
                    maxvalue = value
                }
                if (value < minvalue) {
                    minvalue = value
                }
                serie.data.push({
                    y: value,
                    formattedValue: this.getFormattedValue(r, c, value)
                })
            }
            chart.series.push(serie)
        }
        chart.yAxis = {
            min: (minvalue < 0 ? minvalue : 0),
            max: maxvalue,
            title: {
                text: ""
            }
        };
        $("#" + divId).highcharts(chart)
    }
};
EditableGrid.prototype.renderStackedBarChart = function(a, c, d, b) {};
EditableGrid.prototype.renderPieChart = function(divId, title, valueColumnIndexOrName, labelColumnIndexOrName, options) {
    this.startAngle = 0;
    this.bgColor = null;
    this.alpha = 0.9;
    this.limit = 0;
    this.pie3d = false, this.gradientFill = true;
    if (options) {
        for (var p in options) {
            this[p] = options[p]
        }
    }
    with(this) {
        if (EditableGrid_check_lib === null) {
            EditableGrid_check_lib = checkChartLib()
        }
        if (!EditableGrid_check_lib) {
            return false
        }
        labelColumnIndexOrName = labelColumnIndexOrName || 0;
        title = (typeof title == "undefined" || title === null) ? getColumnLabel(valueColumnIndexOrName) : title;
        var cValue = getColumnIndex(valueColumnIndexOrName);
        var cLabel = getColumnIndex(labelColumnIndexOrName);
        var rowCount = getRowCount() - (ignoreLastRow ? 1 : 0);
        if (limit > 0 && rowCount > limit) {
            rowCount = limit
        }
        var type = getColumnType(valueColumnIndexOrName);
        if (type != "double" && type != "integer" && cValue != cLabel) {
            return false
        }
        var chart = {
            chart: {
                type: "pie",
                backgroundColor: bgColor,
                plotBackgroundColor: bgColor,
                plotBorderWidth: 0,
                options3d: {
                    enabled: pie3d,
                    alpha: 45
                }
            },
            credits: {
                enabled: false
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>"
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        format: cValue == cLabel ? "<b>{point.name}</b>" : "<b>{point.name}</b><br/>{point.formattedValue}"
                    },
                    startAngle: startAngle
                }
            }
        };
        chart.series = [];
        var serie = {
            name: title,
            data: []
        };
        chart.series.push(serie);
        if (cValue == cLabel) {
            var distinctValues = {};
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var rowValue = getValueAt(r, cValue);
                if (rowValue in distinctValues) {
                    distinctValues[rowValue]++
                } else {
                    distinctValues[rowValue] = 1
                }
            }
            for (var value in distinctValues) {
                var occurences = distinctValues[value];
                serie.data.push({
                    y: occurences,
                    name: value,
                    formattedValue: value,
                    color: hex2rgba(smartColorsBar[serie.data.length % smartColorsPie.length], alpha)
                })
            }
            chart.series.push(serie)
        } else {
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var value = getValueAt(r, cValue);
                if (value !== null && !isNaN(value)) {
                    serie.data.push({
                        y: value,
                        name: getValueAt(r, cLabel),
                        formattedValue: this.getFormattedValue(r, cValue, value),
                        color: hex2rgba(smartColorsBar[serie.data.length % smartColorsPie.length], alpha)
                    })
                }
            }
        }
        $("#" + divId).highcharts(chart);
        return serie.data.length
    }
};
EditableGrid.prototype.clearChart = function(a) {
    $("#" + a).html("")
};
var EditableGrid_pending_charts = {};

function EditableGrid_loadChart(a) {
    var b = findSWF(a);
    if (b && typeof b.load == "function") {
        b.load(JSON.stringify(EditableGrid_pending_charts[a]))
    } else {
        setTimeout("EditableGrid_loadChart('" + a + "');", 100)
    }
}

function EditableGrid_get_chart_data(a) {
    setTimeout("EditableGrid_loadChart('" + a + "');", 100);
    return JSON.stringify(EditableGrid_pending_charts[a])
}
EditableGrid.prototype.checkChartLib_OFC = function() {
    EditableGrid_check_lib = false;
    if (typeof JSON.stringify == "undefined") {
        alert("This method needs the JSON javascript library");
        return false
    } else {
        if (typeof findSWF == "undefined") {
            alert("This method needs the open flash chart javascript library (findSWF)");
            return false
        } else {
            if (typeof ofc_chart == "undefined") {
                alert("This method needs the open flash chart javascript library (ofc_chart)");
                return false
            } else {
                if (typeof swfobject == "undefined") {
                    alert("This method needs the swfobject javascript library");
                    return false
                } else {
                    return true
                }
            }
        }
    }
};
EditableGrid.prototype.renderBarChart_OFC = function(divId, title, labelColumnIndexOrName, options) {
    with(this) {
        if (EditableGrid_check_lib && !checkChartLib_OFC()) {
            return false
        }
        this.legend = null;
        this.bgColor = "#ffffff";
        this.alpha = 0.9;
        this.limit = 0;
        this.bar3d = true;
        this.rotateXLabels = 0;
        if (options) {
            for (var p in options) {
                this[p] = options[p]
            }
        }
        labelColumnIndexOrName = labelColumnIndexOrName || 0;
        var cLabel = getColumnIndex(labelColumnIndexOrName);
        var chart = new ofc_chart();
        chart.bg_colour = bgColor;
        chart.set_title({
            text: title || "",
            style: "{font-size: 20px; color:#0000ff; font-family: Verdana; text-align: center;}"
        });
        var columnCount = getColumnCount();
        var rowCount = getRowCount() - (ignoreLastRow ? 1 : 0);
        if (limit > 0 && rowCount > limit) {
            rowCount = limit
        }
        var maxvalue = 0;
        for (var c = 0; c < columnCount; c++) {
            if (!isColumnBar(c)) {
                continue
            }
            var bar = new ofc_element(bar3d ? "bar_3d" : "bar");
            bar.alpha = alpha;
            bar.colour = smartColorsBar[chart.elements.length % smartColorsBar.length];
            bar.fill = "transparent";
            bar.text = getColumnLabel(c);
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var value = getValueAt(r, c);
                if (value > maxvalue) {
                    maxvalue = value
                }
                bar.values.push(value)
            }
            chart.add_element(bar)
        }
        var ymax = 10;
        while (ymax < maxvalue) {
            ymax *= 10
        }
        var dec_step = ymax / 10;
        while (ymax - dec_step > maxvalue) {
            ymax -= dec_step
        }
        var xLabels = [];
        for (var r = 0; r < rowCount; r++) {
            if (getRowAttribute(r, "skip") == "1") {
                continue
            }
            var label = getRowAttribute(r, "barlabel");
            xLabels.push(label ? label : getValueAt(r, cLabel))
        }
        chart.x_axis = {
            stroke: 1,
            tick_height: 10,
            colour: "#E2E2E2",
            "grid-colour": "#E2E2E2",
            labels: {
                rotate: rotateXLabels,
                labels: xLabels
            },
            "3d": 5
        };
        chart.y_axis = {
            stroke: 4,
            tick_length: 3,
            colour: "#428BC7",
            "grid-colour": "#E2E2E2",
            offset: 0,
            steps: ymax / 10,
            max: ymax
        };
        chart.x_legend = {
            text: legend || getColumnLabel(labelColumnIndexOrName),
            style: "{font-size: 11px; color: #000033}"
        };
        chart.y_legend = {
            text: "",
            style: "{font-size: 11px; color: #000033}"
        };
        updateChart(divId, chart)
    }
};
EditableGrid.prototype.renderStackedBarChart_OFC = function(divId, title, labelColumnIndexOrName, options) {
    with(this) {
        if (EditableGrid_check_lib && !checkChartLib_OFC()) {
            return false
        }
        this.legend = null;
        this.bgColor = "#ffffff";
        this.alpha = 0.8;
        this.limit = 0;
        this.rotateXLabels = 0;
        if (options) {
            for (var p in options) {
                this[p] = options[p]
            }
        }
        labelColumnIndexOrName = labelColumnIndexOrName || 0;
        var cLabel = getColumnIndex(labelColumnIndexOrName);
        var chart = new ofc_chart();
        chart.bg_colour = bgColor;
        chart.set_title({
            text: title || "",
            style: "{font-size: 20px; color:#0000ff; font-family: Verdana; text-align: center;}"
        });
        var columnCount = getColumnCount();
        var rowCount = getRowCount() - (ignoreLastRow ? 1 : 0);
        if (limit > 0 && rowCount > limit) {
            rowCount = limit
        }
        var maxvalue = 0;
        var bar = new ofc_element("bar_stack");
        bar.alpha = alpha;
        bar.colours = smartColorsBar;
        bar.fill = "transparent";
        bar.keys = [];
        for (var c = 0; c < columnCount; c++) {
            if (!isColumnBar(c)) {
                continue
            }
            bar.keys.push({
                colour: smartColorsBar[bar.keys.length % smartColorsBar.length],
                text: getColumnLabel(c),
                "font-size": "13"
            })
        }
        for (var r = 0; r < rowCount; r++) {
            if (getRowAttribute(r, "skip") == "1") {
                continue
            }
            var valueRow = [];
            var valueStack = 0;
            for (var c = 0; c < columnCount; c++) {
                if (!isColumnBar(c)) {
                    continue
                }
                var value = getValueAt(r, c);
                value = isNaN(value) ? 0 : value;
                valueStack += value;
                valueRow.push(value)
            }
            if (valueStack > maxvalue) {
                maxvalue = valueStack
            }
            bar.values.push(valueRow)
        }
        chart.add_element(bar);
        var ymax = 10;
        while (ymax < maxvalue) {
            ymax *= 10
        }
        var dec_step = ymax / 10;
        while (ymax - dec_step > maxvalue) {
            ymax -= dec_step
        }
        var xLabels = [];
        for (var r = 0; r < rowCount; r++) {
            if (getRowAttribute(r, "skip") == "1") {
                continue
            }
            xLabels.push("aa " + getValueAt(r, cLabel))
        }
        chart.x_axis = {
            stroke: 1,
            tick_height: 10,
            colour: "#E2E2E2",
            "grid-colour": "#E2E2E2",
            labels: {
                rotate: rotateXLabels,
                labels: xLabels
            },
            "3d": 5
        };
        chart.y_axis = {
            stroke: 4,
            tick_length: 3,
            colour: "#428BC7",
            "grid-colour": "#E2E2E2",
            offset: 0,
            steps: ymax / 10,
            max: ymax
        };
        chart.x_legend = {
            text: legend || getColumnLabel(labelColumnIndexOrName),
            style: "{font-size: 11px; color: #000033}"
        };
        chart.y_legend = {
            text: "",
            style: "{font-size: 11px; color: #000033}"
        };
        updateChart(divId, chart)
    }
};
EditableGrid.prototype.renderPieChart_OFC = function(divId, title, valueColumnIndexOrName, labelColumnIndexOrName, options) {
    with(this) {
        if (EditableGrid_check_lib && !checkChartLib_OFC()) {
            return false
        }
        this.startAngle = 0;
        this.bgColor = "#ffffff";
        this.alpha = 0.5;
        this.limit = 0;
        this.gradientFill = true;
        if (options) {
            for (var p in options) {
                this[p] = options[p]
            }
        }
        var type = getColumnType(valueColumnIndexOrName);
        if (type != "double" && type != "integer" && valueColumnIndexOrName != labelColumnIndexOrName) {
            return
        }
        labelColumnIndexOrName = labelColumnIndexOrName || 0;
        title = (typeof title == "undefined" || title === null) ? getColumnLabel(valueColumnIndexOrName) : title;
        var cValue = getColumnIndex(valueColumnIndexOrName);
        var cLabel = getColumnIndex(labelColumnIndexOrName);
        var chart = new ofc_chart();
        chart.bg_colour = bgColor;
        chart.set_title({
            text: title,
            style: "{font-size: 20px; color:#0000ff; font-family: Verdana; text-align: center;}"
        });
        var rowCount = getRowCount() - (ignoreLastRow ? 1 : 0);
        if (limit > 0 && rowCount > limit) {
            rowCount = limit
        }
        var pie = new ofc_element("pie");
        pie.colours = smartColorsPie;
        pie.alpha = alpha;
        pie["gradient-fill"] = gradientFill;
        if (typeof startAngle != "undefined" && startAngle !== null) {
            pie["start-angle"] = startAngle
        }
        if (valueColumnIndexOrName == labelColumnIndexOrName) {
            var distinctValues = {};
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var rowValue = getValueAt(r, cValue);
                if (rowValue in distinctValues) {
                    distinctValues[rowValue]++
                } else {
                    distinctValues[rowValue] = 1
                }
            }
            for (var value in distinctValues) {
                var occurences = distinctValues[value];
                pie.values.push({
                    value: occurences,
                    label: value + " (" + (100 * (occurences / rowCount)).toFixed(1) + "%)"
                })
            }
        } else {
            var total = 0;
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var rowValue = getValueAt(r, cValue);
                total += isNaN(rowValue) ? 0 : rowValue
            }
            for (var r = 0; r < rowCount; r++) {
                if (getRowAttribute(r, "skip") == "1") {
                    continue
                }
                var value = getValueAt(r, cValue);
                var label = getValueAt(r, cLabel);
                if (!isNaN(value)) {
                    pie.values.push({
                        value: value,
                        label: label + " (" + (100 * (value / total)).toFixed(1) + "%)"
                    })
                }
            }
        }
        chart.add_element(pie);
        if (pie.values.length > 0) {
            updateChart(divId, chart)
        }
        return pie.values.length
    }
};
EditableGrid.prototype.updateChart = function(divId, chart) {
    if (typeof this.ofcSwf == "undefined" || !this.ofcSwf) {
        this.ofcSwf = "open-flash-chart.swf";
        var e = document.getElementsByTagName("script");
        for (var i = 0; i < e.length; i++) {
            var index = e[i].src.indexOf("openflashchart");
            if (index != -1) {
                this.ofcSwf = e[i].src.substr(0, index + 15) + this.ofcSwf;
                break
            }
        }
    }
    with(this) {
        var swf = findSWF(divId);
        if (swf && typeof swf.load == "function") {
            try {
                swf.load(JSON.stringify(chart))
            } catch (ex) {
                console.error(ex)
            }
        } else {
            var div = _$(divId);
            EditableGrid_pending_charts[divId] = chart;
            var w = parseInt(getStyle(div, "width"));
            var h = parseInt(getStyle(div, "height"));
            w = Math.max(isNaN(w) ? 0 : w, div.offsetWidth);
            h = Math.max(isNaN(h) ? 0 : h, div.offsetHeight);
            swfobject.embedSWF(this.ofcSwf, divId, "" + (w || 500), "" + (h || 200), "9.0.0", "expressInstall.swf", {
                "get-data": "EditableGrid_get_chart_data",
                id: divId
            }, null, {
                wmode: "Opaque",
                salign: "l",
                AllowScriptAccess: "always"
            })
        }
        chartRendered()
    }
};

function CellEditor(a) {
    this.init(a)
}
CellEditor.prototype.init = function(a) {
    if (a) {
        for (var b in a) {
            this[b] = a[b]
        }
    }
};
CellEditor.prototype.edit = function(e, b, a, d) {
    a.isEditing = true;
    a.rowIndex = e;
    a.columnIndex = b;
    var c = this.getEditor(a, d);
    if (!c) {
        return false
    }
    c.element = a;
    c.celleditor = this;
    c.onkeydown = function(f) {
        f = f || window.event;
        if (f.keyCode == 13 || f.keyCode == 9) {
            this.onblur_backup = this.onblur;
            this.onblur = null;
            if (this.celleditor.applyEditing(this.element, this.celleditor.getEditorValue(this)) === false) {
                this.onblur = this.onblur_backup
            }
            return false
        }
        if (f.keyCode == 27) {
            this.onblur = null;
            this.celleditor.cancelEditing(this.element);
            return false
        }
    };
    if (!this.editablegrid.allowSimultaneousEdition) {
        c.onblur = this.editablegrid.saveOnBlur ? function(f) {
            this.onblur_backup = this.onblur;
            this.onblur = null;
            if (this.celleditor.applyEditing(this.element, this.celleditor.getEditorValue(this)) === false) {
                this.onblur = this.onblur_backup
            }
        } : function(f) {
            this.onblur = null;
            this.celleditor.cancelEditing(this.element)
        }
    }
    this.displayEditor(a, c);
    c.focus()
};
CellEditor.prototype.getEditor = function(a, b) {
    return null
};
CellEditor.prototype.getEditorValue = function(a) {
    return a.value
};
CellEditor.prototype.formatValue = function(a) {
    return a
};
CellEditor.prototype.displayEditor = function(h, l, g, f) {
    l.style.fontFamily = this.editablegrid.getStyle(h, "fontFamily", "font-family");
    l.style.fontSize = this.editablegrid.getStyle(h, "fontSize", "font-size");
    if (this.editablegrid.editmode == "static") {
        while (h.hasChildNodes()) {
            h.removeChild(h.firstChild)
        }
        h.appendChild(l)
    }
    if (this.editablegrid.editmode == "absolute") {
        h.appendChild(l);
        l.style.position = "absolute";
        var b = this.editablegrid.paddingLeft(h);
        var k = this.editablegrid.paddingTop(h);
        var e = this.editablegrid.getScrollXOffset(h);
        var c = this.editablegrid.getScrollYOffset(h);
        var a = this.editablegrid.verticalAlign(h) == "middle" ? (h.offsetHeight - l.offsetHeight) / 2 - k : 0;
        l.style.left = (this.editablegrid.getCellX(h) - e + b + (g ? g : 0)) + "px";
        l.style.top = (this.editablegrid.getCellY(h) - c + k + a + (f ? f : 0)) + "px";
        if (this.column.datatype == "integer" || this.column.datatype == "double") {
            var d = this.editablegrid.getCellX(h) - e + h.offsetWidth - (parseInt(l.style.left) + l.offsetWidth);
            l.style.left = (parseInt(l.style.left) + d) + "px";
            l.style.textAlign = "right"
        }
    }
    if (this.editablegrid.editmode == "fixed") {
        var j = _$(this.editablegrid.editorzoneid);
        while (j.hasChildNodes()) {
            j.removeChild(j.firstChild)
        }
        j.appendChild(l)
    }
};
CellEditor.prototype._clearEditor = function(a) {
    a.isEditing = false;
    if (this.editablegrid.editmode == "fixed") {
        var b = _$(this.editablegrid.editorzoneid);
        while (b.hasChildNodes()) {
            b.removeChild(b.firstChild)
        }
    }
};
CellEditor.prototype.cancelEditing = function(element) {
    with(this) {
        if (element && element.isEditing) {
            var renderer = this == column.headerEditor ? column.headerRenderer : column.cellRenderer;
            renderer._render(element.rowIndex, element.columnIndex, element, editablegrid.getValueAt(element.rowIndex, element.columnIndex));
            _clearEditor(element)
        }
    }
};
CellEditor.prototype.applyEditing = function(element, newValue) {
    with(this) {
        if (element && element.isEditing) {
            if (!column.isValid(newValue)) {
                return false
            }
            var formattedValue = formatValue(newValue);
            var previousValue = editablegrid.setValueAt(element.rowIndex, element.columnIndex, formattedValue);
            var newValue = editablegrid.getValueAt(element.rowIndex, element.columnIndex);
            if (!this.editablegrid.isSame(newValue, previousValue)) {
                editablegrid.modelChanged(element.rowIndex, element.columnIndex, previousValue, newValue, editablegrid.getRow(element.rowIndex))
            }
            _clearEditor(element);
            return true
        }
        return false
    }
};

function TextCellEditor(b, c, a) {
    if (b) {
        this.fieldSize = b
    }
    if (c) {
        this.maxLength = c
    }
    if (a) {
        this.init(a)
    }
}
TextCellEditor.prototype = new CellEditor();
TextCellEditor.prototype.fieldSize = -1;
TextCellEditor.prototype.maxLength = -1;
TextCellEditor.prototype.autoHeight = true;
TextCellEditor.prototype.editorValue = function(a) {
    return a
};
TextCellEditor.prototype.updateStyle = function(a) {
    if (this.column.isValid(this.getEditorValue(a))) {
        this.editablegrid.removeClassName(a, this.editablegrid.invalidClassName)
    } else {
        this.editablegrid.addClassName(a, this.editablegrid.invalidClassName)
    }
};
TextCellEditor.prototype.getEditor = function(b, c) {
    var d = document.createElement("input");
    d.setAttribute("type", "text");
    if (this.maxLength > 0) {
        d.setAttribute("maxlength", this.maxLength)
    }
    if (this.fieldSize > 0) {
        d.setAttribute("size", this.fieldSize)
    } else {
        d.style.width = this.editablegrid.autoWidth(b) + "px"
    }
    var a = this.editablegrid.autoHeight(b);
    if (this.autoHeight) {
        d.style.height = a + "px"
    }
    d.value = this.editorValue(c);
    d.onkeyup = function(e) {
        this.celleditor.updateStyle(this)
    };
    return d
};
TextCellEditor.prototype.displayEditor = function(a, b) {
    CellEditor.prototype.displayEditor.call(this, a, b, -1 * this.editablegrid.borderLeft(b), -1 * (this.editablegrid.borderTop(b) + 1));
    this.updateStyle(b);
    b.select()
};

function NumberCellEditor(b, a) {
    this.type = b;
    this.init(a)
}
NumberCellEditor.prototype = new TextCellEditor(-1, 32);
NumberCellEditor.prototype.editorValue = function(a) {
    return (a === null || isNaN(a)) ? "" : (a + "").replace(".", this.column.decimal_point)
};
NumberCellEditor.prototype.getEditorValue = function(a) {
    return a.value.replace(",", ".")
};
NumberCellEditor.prototype.formatValue = function(a) {
    return this.type == "integer" ? parseInt(a) : parseFloat(a)
};

function SelectCellEditor(a) {
    this.minWidth = 75;
    this.minHeight = 22;
    this.adaptHeight = true;
    this.adaptWidth = true;
    this.init(a)
}
SelectCellEditor.prototype = new CellEditor();
SelectCellEditor.prototype.isValueSelected = function(b, c, a) {
    return (!c && !a) || (c == a)
};
SelectCellEditor.prototype.getEditor = function(d, n) {
    var a = document.createElement("select");
    if (this.adaptWidth) {
        a.style.width = Math.max(this.minWidth, this.editablegrid.autoWidth(d)) + "px"
    }
    if (this.adaptHeight) {
        a.style.height = Math.max(this.minHeight, this.editablegrid.autoHeight(d)) + "px"
    }
    var m = this.column.getOptionValuesForEdit(d.rowIndex);
    var h = 0,
        c = false;
    for (var g = 0; g < m.length; g++) {
        var l = m[g];
        if (typeof l.values == "object") {
            var b = document.createElement("optgroup");
            b.label = l.label;
            a.appendChild(b);
            for (var k = 0; k < l.values.length; k++) {
                var o = l.values[k];
                var f = document.createElement("option");
                f.text = o.label;
                f.value = o.value ? o.value : "";
                b.appendChild(f);
                if (this.isValueSelected(a, o.value, n)) {
                    f.selected = true;
                    c = true
                } else {
                    f.selected = false
                }
                h++
            }
        } else {
            var f = document.createElement("option");
            f.text = l.label;
            f.value = l.value ? l.value : "";
            try {
                a.add(f, null)
            } catch (j) {
                a.add(f)
            }
            if (this.isValueSelected(a, l.value, n)) {
                f.selected = true;
                c = true
            } else {
                f.selected = false
            }
            h++
        }
    }
    if (!c) {
        var f = document.createElement("option");
        f.text = n ? n : "";
        f.value = n ? n : "";
        try {
            a.add(f, a.options[0])
        } catch (j) {
            a.add(f)
        }
        a.selectedIndex = 0
    }
    a.onchange = function(e) {
        this.onblur = null;
        this.celleditor.applyEditing(this.element, this.value)
    };
    return a
};

function DateCellEditor(a) {
    this.init(a)
}
DateCellEditor.prototype = new TextCellEditor();
DateCellEditor.prototype.displayEditor = function(a, b) {
    TextCellEditor.prototype.displayEditor.call(this, a, b);
    jQuery(b).datepicker({
        dateFormat: (this.editablegrid.dateFormat == "EU" ? "dd/mm/yy" : "mm/dd/yy"),
        changeMonth: true,
        changeYear: true,
        yearRange: "c-100:c+10",
        beforeShow: function() {
            this.onblur_backup = this.onblur;
            this.onblur = null
        },
        onClose: function(c) {
            if (c != "") {
                this.celleditor.applyEditing(b.element, c)
            } else {
                if (this.onblur_backup != null) {
                    this.onblur_backup()
                }
            }
        }
    }).datepicker("show")
};

function CellRenderer(a) {
    this.init(a)
}
CellRenderer.prototype.init = function(a) {
    for (var b in a) {
        this[b] = a[b]
    }
};
CellRenderer.prototype._render = function(d, b, a, c) {
    a.rowIndex = d;
    a.columnIndex = b;
    while (a.hasChildNodes()) {
        a.removeChild(a.firstChild)
    }
    a.isEditing = false;
    if (this.column.isNumerical()) {
        EditableGrid.prototype.addClassName(a, "number")
    }
    if (this.column.datatype == "boolean") {
        EditableGrid.prototype.addClassName(a, "boolean")
    }
    EditableGrid.prototype.addClassName(a, "editablegrid-" + this.column.name);
    a.setAttribute("data-title", this.column.label);
    return this.render(a, typeof c == "string" && this.column.datatype != "html" ? (c === null ? null : htmlspecialchars(c, "ENT_NOQUOTES").replace(/\s\s/g, " &nbsp;")) : c)
};
CellRenderer.prototype.render = function(c, d, b) {
    var a = b ? (typeof d == "string" && this.column.datatype != "html" ? (d === null ? null : htmlspecialchars(d, "ENT_NOQUOTES").replace(/\s\s/g, " &nbsp;")) : d) : d;
    c.innerHTML = a ? a : ""
};
CellRenderer.prototype.getDisplayValue = function(b, a) {
    return a
};

function EnumCellRenderer(a) {
    this.init(a)
}
EnumCellRenderer.prototype = new CellRenderer();
EnumCellRenderer.prototype.getLabel = function(e, d) {
    var c = "";
    if (typeof d != "undefined") {
        d = d ? d : "";
        var a = this.column.getOptionValuesForRender(e);
        if (a && d in a) {
            c = a[d]
        }
        if (c == "") {
            var b = typeof d == "number" && isNaN(d);
            c = b ? "" : d
        }
    }
    return c
};
EnumCellRenderer.prototype.render = function(b, c) {
    var a = this.getLabel(b.rowIndex, c);
    b.innerHTML = a ? (this.column.datatype != "html" ? htmlspecialchars(a, "ENT_NOQUOTES").replace(/\s\s/g, "&nbsp; ") : a) : ""
};
EnumCellRenderer.prototype.getDisplayValue = function(b, a) {
    return this.getLabel(b, a)
};

function NumberCellRenderer(a) {
    this.init(a)
}
NumberCellRenderer.prototype = new CellRenderer();
NumberCellRenderer.prototype.render = function(c, e) {
    var d = this.column || {};
    var a = e === null || (typeof e == "number" && isNaN(e));
    var b = a ? (d.nansymbol || "") : e;
    if (typeof b == "number") {
        if (d.precision !== null) {
            b = number_format(b, d.precision, d.decimal_point, d.thousands_separator)
        }
        if (d.unit !== null) {
            if (d.unit_before_number) {
                b = d.unit + " " + b
            } else {
                b = b + " " + d.unit
            }
        }
    }
    c.innerHTML = b;
    c.style.fontWeight = a ? "normal" : ""
};

function CheckboxCellRenderer(a) {
    this.init(a)
}
CheckboxCellRenderer.prototype = new CellRenderer();
CheckboxCellRenderer.prototype._render = function(d, b, a, c) {
    if (a.firstChild && (typeof a.firstChild.getAttribute != "function" || a.firstChild.getAttribute("type") != "checkbox")) {
        while (a.hasChildNodes()) {
            a.removeChild(a.firstChild)
        }
    }
    a.rowIndex = d;
    a.columnIndex = b;
    EditableGrid.prototype.addClassName(a, "editablegrid-" + this.column.name);
    a.setAttribute("data-title", this.column.label);
    return this.render(a, c)
};
CheckboxCellRenderer.prototype.render = function(a, c) {
    c = (c && c != 0 && c != "false") ? true : false;
    if (a.firstChild) {
        a.firstChild.checked = c;
        return
    }
    var d = document.createElement("input");
    d.setAttribute("type", "checkbox");
    d.element = a;
    d.cellrenderer = this;
    var b = new CellEditor();
    b.editablegrid = this.editablegrid;
    b.column = this.column;
    d.onclick = function(e) {
        a.rowIndex = this.cellrenderer.editablegrid.getRowIndex(a.parentNode);
        a.isEditing = true;
        b.applyEditing(a, d.checked ? true : false)
    };
    a.appendChild(d);
    d.checked = c;
    d.disabled = (!this.column.editable || !this.editablegrid.isEditable(a.rowIndex, a.columnIndex));
    EditableGrid.prototype.addClassName(a, "boolean")
};

function EmailCellRenderer(a) {
    this.init(a)
}
EmailCellRenderer.prototype = new CellRenderer();
EmailCellRenderer.prototype.render = function(a, b) {
    a.innerHTML = b ? "<a href='mailto:" + b + "'>" + b + "</a>" : ""
};

function WebsiteCellRenderer(a) {
    this.init(a)
}
WebsiteCellRenderer.prototype = new CellRenderer();
WebsiteCellRenderer.prototype.render = function(a, b) {
    a.innerHTML = b ? "<a href='" + (b.indexOf("//") == -1 ? "http://" + b : b) + "'>" + b + "</a>" : ""
};

function DateCellRenderer(a) {
    this.init(a)
}
DateCellRenderer.prototype = new CellRenderer;
DateCellRenderer.prototype.render = function(a, c) {
    var b = this.editablegrid.checkDate(c);
    if (typeof b == "object") {
        a.innerHTML = b.formattedDate
    } else {
        a.innerHTML = c
    }
    a.style.whiteSpace = "nowrap"
};

function SortHeaderRenderer(a, b) {
    this.columnName = a;
    this.cellRenderer = b
}
SortHeaderRenderer.prototype = new CellRenderer();
SortHeaderRenderer.prototype.render = function(cell, value) {
    if (!value) {
        if (this.cellRenderer) {
            this.cellRenderer.render(cell, value)
        }
    } else {
        var link = document.createElement("a");
        cell.appendChild(link);
        link.columnName = this.columnName;
        link.style.cursor = "pointer";
        link.innerHTML = value;
        link.editablegrid = this.editablegrid;
        link.renderer = this;
        link.onclick = function() {
            with(this.editablegrid) {
                var cols = tHead.rows[0].cells;
                var clearPrevious = -1;
                var backOnFirstPage = false;
                if (sortedColumnName != this.columnName) {
                    clearPrevious = sortedColumnName;
                    sortedColumnName = this.columnName;
                    sortDescending = false;
                    backOnFirstPage = true
                } else {
                    if (!sortDescending) {
                        sortDescending = true
                    } else {
                        clearPrevious = sortedColumnName;
                        sortedColumnName = -1;
                        sortDescending = false;
                        backOnFirstPage = true
                    }
                }
                sort(sortedColumnName, sortDescending, backOnFirstPage)
            }
        };
        if (this.editablegrid.sortedColumnName == this.columnName) {
            cell.appendChild(document.createTextNode("\u00a0"));
            cell.appendChild(this.editablegrid.sortDescending ? this.editablegrid.sortDownImage : this.editablegrid.sortUpImage)
        }
        if (this.cellRenderer) {
            this.cellRenderer.render(cell, value)
        }
    }
};
EditableGrid.prototype._convertOptions = function(a) {
    if (a !== null && (!(a instanceof Array)) && typeof a == "object") {
        var c = [];
        for (var b in a) {
            if (typeof a[b] == "object") {
                c.push({
                    label: b,
                    values: this._convertOptions(a[b])
                })
            } else {
                c.push({
                    value: b,
                    label: a[b]
                })
            }
        }
        a = c
    }
    return a
};
EditableGrid.prototype.setCookie = function(a, d, b) {
    var e = new Date();
    e.setDate(e.getDate() + b);
    var c = escape(d) + ((b == null) ? "" : "; expires=" + e.toUTCString());
    document.cookie = a + "=" + c
};
EditableGrid.prototype.getCookie = function(c) {
    var b = document.cookie.split(";");
    for (var d = 0; d < b.length; d++) {
        var a = b[d].substr(0, b[d].indexOf("="));
        var e = b[d].substr(b[d].indexOf("=") + 1);
        a = a.replace(/^\s+|\s+$/g, "");
        if (a == c) {
            return unescape(e)
        }
    }
    return null
};
EditableGrid.prototype.has_local_storage = function() {
    try {
        return "localStorage" in window && window.localStorage !== null
    } catch (a) {
        return false
    }
};
EditableGrid.prototype._localset = function(a, b) {
    if (this.has_local_storage()) {
        localStorage.setItem(a, b)
    } else {
        this.setCookie(a, b, null)
    }
};
EditableGrid.prototype._localunset = function(a) {
    if (this.has_local_storage()) {
        localStorage.removeItem(a)
    } else {
        this.setCookie(a, null, null)
    }
};
EditableGrid.prototype._localget = function(a) {
    if (this.has_local_storage()) {
        return localStorage.getItem(a)
    }
    return this.getCookie(a)
};
EditableGrid.prototype._localisset = function(a) {
    if (this.has_local_storage()) {
        return localStorage.getItem(a) !== null && localStorage.getItem(a) != "undefined"
    }
    return this.getCookie(a) !== null
};
EditableGrid.prototype.localset = function(a, b) {
    if (this.enableStore) {
        return this._localset(this.name + "_" + a, b)
    }
};
EditableGrid.prototype.localunset = function(a) {
    if (this.enableStore) {
        return this._localunset(this.name + "_" + a, value)
    }
};
EditableGrid.prototype.localget = function(a) {
    return this.enableStore ? this._localget(this.name + "_" + a) : null
};
EditableGrid.prototype.localisset = function(a) {
    return this.enableStore ? this._localget(this.name + "_" + a) !== null : false
};
EditableGrid.prototype.unsort = function(d, c) {
    aa = isNaN(d[2]) ? 0 : parseFloat(d[2]);
    bb = isNaN(c[2]) ? 0 : parseFloat(c[2]);
    return aa - bb
};
EditableGrid.prototype.sort_numeric = function(d, c) {
    aa = isNaN(parseFloat(d[0])) ? 0 : parseFloat(d[0]);
    bb = isNaN(parseFloat(c[0])) ? 0 : parseFloat(c[0]);
    return aa - bb
};
EditableGrid.prototype.sort_boolean = function(d, c) {
    aa = !d[0] || d[0] == "false" ? 0 : 1;
    bb = !c[0] || c[0] == "false" ? 0 : 1;
    return aa - bb
};
EditableGrid.prototype.sort_alpha = function(d, c) {
    if (d[0].toLowerCase() == c[0].toLowerCase()) {
        return 0
    }
    return d[0].toLowerCase().localeCompare(c[0].toLowerCase())
};
EditableGrid.prototype.sort_date = function(d, c) {
    date = EditableGrid.prototype.checkDate(d[0]);
    aa = typeof date == "object" ? date.sortDate : 0;
    date = EditableGrid.prototype.checkDate(c[0]);
    bb = typeof date == "object" ? date.sortDate : 0;
    return aa - bb
};
EditableGrid.prototype.getStyle = function(c, a, b) {
    b = b || a;
    if (c.currentStyle) {
        return c.currentStyle[a]
    } else {
        if (window.getComputedStyle) {
            return document.defaultView.getComputedStyle(c, null).getPropertyValue(b)
        }
    }
    return c.style[a]
};
EditableGrid.prototype.isStatic = function(b) {
    var a = this.getStyle(b, "position");
    return (!a || a == "static")
};
EditableGrid.prototype.verticalAlign = function(a) {
    return this.getStyle(a, "verticalAlign", "vertical-align")
};
EditableGrid.prototype.paddingLeft = function(a) {
    var b = parseInt(this.getStyle(a, "paddingLeft", "padding-left"));
    return isNaN(b) ? 0 : Math.max(0, b)
};
EditableGrid.prototype.paddingRight = function(a) {
    var b = parseInt(this.getStyle(a, "paddingRight", "padding-right"));
    return isNaN(b) ? 0 : Math.max(0, b)
};
EditableGrid.prototype.paddingTop = function(a) {
    var b = parseInt(this.getStyle(a, "paddingTop", "padding-top"));
    return isNaN(b) ? 0 : Math.max(0, b)
};
EditableGrid.prototype.paddingBottom = function(a) {
    var b = parseInt(this.getStyle(a, "paddingBottom", "padding-bottom"));
    return isNaN(b) ? 0 : Math.max(0, b)
};
EditableGrid.prototype.borderLeft = function(b) {
    var c = parseInt(this.getStyle(b, "borderRightWidth", "border-right-width"));
    var a = parseInt(this.getStyle(b, "borderLeftWidth", "border-left-width"));
    c = isNaN(c) ? 0 : c;
    a = isNaN(a) ? 0 : a;
    return Math.max(c, a)
};
EditableGrid.prototype.borderRight = function(a) {
    return this.borderLeft(a)
};
EditableGrid.prototype.borderTop = function(b) {
    var a = parseInt(this.getStyle(b, "borderTopWidth", "border-top-width"));
    var c = parseInt(this.getStyle(b, "borderBottomWidth", "border-bottom-width"));
    a = isNaN(a) ? 0 : a;
    c = isNaN(c) ? 0 : c;
    return Math.max(a, c)
};
EditableGrid.prototype.borderBottom = function(a) {
    return this.borderTop(a)
};
EditableGrid.prototype.autoWidth = function(a) {
    return a.offsetWidth - this.paddingLeft(a) - this.paddingRight(a) - this.borderLeft(a) - this.borderRight(a)
};
EditableGrid.prototype.autoHeight = function(a) {
    return a.offsetHeight - this.paddingTop(a) - this.paddingBottom(a) - this.borderTop(a) - this.borderBottom(a)
};
EditableGrid.prototype.detectDir = function() {
    var c = location.href;
    var d = document.getElementsByTagName("base");
    for (var a = 0; a < d.length; a++) {
        if (d[a].href) {
            c = d[a].href
        }
    }
    var d = document.getElementsByTagName("script");
    for (var a = 0; a < d.length; a++) {
        if (d[a].src && /(^|\/)editablegrid[^\/]*\.js([?#].*)?$/i.test(d[a].src)) {
            var f = new URI(d[a].src);
            var b = f.toAbsolute(c);
            b.path = b.path.replace(/[^\/]+$/, "");
            b.path = b.path.replace(/\/$/, "");
            delete b.query;
            delete b.fragment;
            return b.toString()
        }
    }
    return false
};
EditableGrid.prototype.isSame = function(b, a) {
    if (b === a) {
        return true
    }
    if (typeof b == "number" && isNaN(b) && typeof a == "number" && isNaN(a)) {
        return true
    }
    if (b === "" && a === null) {
        return true
    }
    if (a === "" && b === null) {
        return true
    }
    return false
};
EditableGrid.prototype.strip = function(a) {
    return a.replace(/^\s+/, "").replace(/\s+$/, "")
};
EditableGrid.prototype.hasClassName = function(a, b) {
    return (a.className.length > 0 && (a.className == b || new RegExp("(^|\\s)" + b + "(\\s|$)").test(a.className)))
};
EditableGrid.prototype.addClassName = function(a, b) {
    if (!this.hasClassName(a, b)) {
        a.className += (a.className ? " " : "") + b
    }
};
EditableGrid.prototype.removeClassName = function(a, b) {
    a.className = this.strip(a.className.replace(new RegExp("(^|\\s+)" + b + "(\\s+|$)"), " "))
};
String.prototype.trim = function() {
    return (this.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""))
};
String.prototype.contains = function(a) {
    return (this.match(a) == a)
};
String.prototype.startsWith = function(a) {
    return (this.match("^" + a) == a)
};
String.prototype.endsWith = function(a) {
    return (this.match(a + "$") == a)
};
EditableGrid.prototype.checkDate = function(m, g) {
    g = g || this.dateFormat;
    g = g || "EU";
    var m;
    var l;
    var c;
    var k;
    var d;
    var a;
    var n;
    var j;
    var f = false;
    var h = new Array("-", " ", "/", ".");
    var b;
    var e = 0;
    var o = this.shortMonthNames;
    o = o || ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    if (!m || m.length < 1) {
        return 0
    }
    for (b = 0; b < h.length; b++) {
        if (m.indexOf(h[b]) != -1) {
            l = m.split(h[b]);
            if (l.length != 3) {
                return 1
            } else {
                c = l[0];
                k = l[1];
                d = l[2]
            }
            f = true
        }
    }
    if (f == false) {
        if (m.length <= 5) {
            return 1
        }
        c = m.substr(0, 2);
        k = m.substr(2, 2);
        d = m.substr(4)
    }
    if (g == "US") {
        strTemp = c;
        c = k;
        k = strTemp
    }
    a = parseInt(c, 10);
    if (isNaN(a)) {
        return 2
    }
    n = parseInt(k, 10);
    if (isNaN(n)) {
        for (i = 0; i < 12; i++) {
            if (k.toUpperCase() == o[i].toUpperCase()) {
                n = i + 1;
                k = o[i];
                i = 12
            }
        }
        if (isNaN(n)) {
            return 3
        }
    }
    if (n > 12 || n < 1) {
        return 5
    }
    j = parseInt(d, 10);
    if (isNaN(j)) {
        return 4
    }
    if (j < 70) {
        j = 2000 + j;
        d = "" + j
    }
    if (j < 100) {
        j = 1900 + j;
        d = "" + j
    }
    if (j < 1900 || j > 2100) {
        return 11
    }
    if ((n == 1 || n == 3 || n == 5 || n == 7 || n == 8 || n == 10 || n == 12) && (a > 31 || a < 1)) {
        return 6
    }
    if ((n == 4 || n == 6 || n == 9 || n == 11) && (a > 30 || a < 1)) {
        return 7
    }
    if (n == 2) {
        if (a < 1) {
            return 8
        }
        if (LeapYear(j) == true) {
            if (a > 29) {
                return 9
            }
        } else {
            if (a > 28) {
                return 10
            }
        }
    }
    return {
        formattedDate: (g == "US" ? o[n - 1] + " " + a + " " + d : a + " " + o[n - 1] + " " + d),
        sortDate: Date.parse(n + "/" + a + "/" + j),
        dbDate: j + "-" + n + "-" + a
    }
};

function LeapYear(a) {
    if (a % 100 == 0) {
        if (a % 400 == 0) {
            return true
        }
    } else {
        if ((a % 4) == 0) {
            return true
        }
    }
    return false
}
URI = function(a) {
    this.scheme = null;
    this.authority = null;
    this.path = "";
    this.query = null;
    this.fragment = null;
    this.parse = function(d) {
        var c = d.match(/^(([A-Za-z][0-9A-Za-z+.-]*)(:))?((\/\/)([^\/?#]*))?([^?#]*)((\?)([^#]*))?((#)(.*))?/);
        this.scheme = c[3] ? c[2] : null;
        this.authority = c[5] ? c[6] : null;
        this.path = c[7];
        this.query = c[9] ? c[10] : null;
        this.fragment = c[12] ? c[13] : null;
        return this
    };
    this.toString = function() {
        var c = "";
        if (this.scheme != null) {
            c = c + this.scheme + ":"
        }
        if (this.authority != null) {
            c = c + "//" + this.authority
        }
        if (this.path != null) {
            c = c + this.path
        }
        if (this.query != null) {
            c = c + "?" + this.query
        }
        if (this.fragment != null) {
            c = c + "#" + this.fragment
        }
        return c
    };
    this.toAbsolute = function(e) {
        var e = new URI(e);
        var d = this;
        var c = new URI;
        if (e.scheme == null) {
            return false
        }
        if (d.scheme != null && d.scheme.toLowerCase() == e.scheme.toLowerCase()) {
            d.scheme = null
        }
        if (d.scheme != null) {
            c.scheme = d.scheme;
            c.authority = d.authority;
            c.path = b(d.path);
            c.query = d.query
        } else {
            if (d.authority != null) {
                c.authority = d.authority;
                c.path = b(d.path);
                c.query = d.query
            } else {
                if (d.path == "") {
                    c.path = e.path;
                    if (d.query != null) {
                        c.query = d.query
                    } else {
                        c.query = e.query
                    }
                } else {
                    if (d.path.substr(0, 1) == "/") {
                        c.path = b(d.path)
                    } else {
                        if (e.authority != null && e.path == "") {
                            c.path = "/" + d.path
                        } else {
                            c.path = e.path.replace(/[^\/]+$/, "") + d.path
                        }
                        c.path = b(c.path)
                    }
                    c.query = d.query
                }
                c.authority = e.authority
            }
            c.scheme = e.scheme
        }
        c.fragment = d.fragment;
        return c
    };

    function b(e) {
        var c = "";
        while (e) {
            if (e.substr(0, 3) == "../" || e.substr(0, 2) == "./") {
                e = e.replace(/^\.+/, "").substr(1)
            } else {
                if (e.substr(0, 3) == "/./" || e == "/.") {
                    e = "/" + e.substr(3)
                } else {
                    if (e.substr(0, 4) == "/../" || e == "/..") {
                        e = "/" + e.substr(4);
                        c = c.replace(/\/?[^\/]*$/, "")
                    } else {
                        if (e == "." || e == "..") {
                            e = ""
                        } else {
                            var d = e.match(/^\/?[^\/]*/)[0];
                            e = e.substr(d.length);
                            c = c + d
                        }
                    }
                }
            }
        }
        return c
    }
    if (a) {
        this.parse(a)
    }
};

function get_html_translation_table(j, g) {
    var d = {},
        f = {},
        c = 0,
        a = "";
    var e = {},
        b = {};
    var k = {},
        h = {};
    e[0] = "HTML_SPECIALCHARS";
    e[1] = "HTML_ENTITIES";
    b[0] = "ENT_NOQUOTES";
    b[2] = "ENT_COMPAT";
    b[3] = "ENT_QUOTES";
    k = !isNaN(j) ? e[j] : j ? j.toUpperCase() : "HTML_SPECIALCHARS";
    h = !isNaN(g) ? b[g] : g ? g.toUpperCase() : "ENT_COMPAT";
    if (k !== "HTML_SPECIALCHARS" && k !== "HTML_ENTITIES") {
        throw new Error("Table: " + k + " not supported")
    }
    if (k === "HTML_ENTITIES") {
        d["160"] = "&nbsp;";
        d["161"] = "&iexcl;";
        d["162"] = "&cent;";
        d["163"] = "&pound;";
        d["164"] = "&curren;";
        d["165"] = "&yen;";
        d["166"] = "&brvbar;";
        d["167"] = "&sect;";
        d["168"] = "&uml;";
        d["169"] = "&copy;";
        d["170"] = "&ordf;";
        d["171"] = "&laquo;";
        d["172"] = "&not;";
        d["173"] = "&shy;";
        d["174"] = "&reg;";
        d["175"] = "&macr;";
        d["176"] = "&deg;";
        d["177"] = "&plusmn;";
        d["178"] = "&sup2;";
        d["179"] = "&sup3;";
        d["180"] = "&acute;";
        d["181"] = "&micro;";
        d["182"] = "&para;";
        d["183"] = "&middot;";
        d["184"] = "&cedil;";
        d["185"] = "&sup1;";
        d["186"] = "&ordm;";
        d["187"] = "&raquo;";
        d["188"] = "&frac14;";
        d["189"] = "&frac12;";
        d["190"] = "&frac34;";
        d["191"] = "&iquest;";
        d["192"] = "&Agrave;";
        d["193"] = "&Aacute;";
        d["194"] = "&Acirc;";
        d["195"] = "&Atilde;";
        d["196"] = "&Auml;";
        d["197"] = "&Aring;";
        d["198"] = "&AElig;";
        d["199"] = "&Ccedil;";
        d["200"] = "&Egrave;";
        d["201"] = "&Eacute;";
        d["202"] = "&Ecirc;";
        d["203"] = "&Euml;";
        d["204"] = "&Igrave;";
        d["205"] = "&Iacute;";
        d["206"] = "&Icirc;";
        d["207"] = "&Iuml;";
        d["208"] = "&ETH;";
        d["209"] = "&Ntilde;";
        d["210"] = "&Ograve;";
        d["211"] = "&Oacute;";
        d["212"] = "&Ocirc;";
        d["213"] = "&Otilde;";
        d["214"] = "&Ouml;";
        d["215"] = "&times;";
        d["216"] = "&Oslash;";
        d["217"] = "&Ugrave;";
        d["218"] = "&Uacute;";
        d["219"] = "&Ucirc;";
        d["220"] = "&Uuml;";
        d["221"] = "&Yacute;";
        d["222"] = "&THORN;";
        d["223"] = "&szlig;";
        d["224"] = "&agrave;";
        d["225"] = "&aacute;";
        d["226"] = "&acirc;";
        d["227"] = "&atilde;";
        d["228"] = "&auml;";
        d["229"] = "&aring;";
        d["230"] = "&aelig;";
        d["231"] = "&ccedil;";
        d["232"] = "&egrave;";
        d["233"] = "&eacute;";
        d["234"] = "&ecirc;";
        d["235"] = "&euml;";
        d["236"] = "&igrave;";
        d["237"] = "&iacute;";
        d["238"] = "&icirc;";
        d["239"] = "&iuml;";
        d["240"] = "&eth;";
        d["241"] = "&ntilde;";
        d["242"] = "&ograve;";
        d["243"] = "&oacute;";
        d["244"] = "&ocirc;";
        d["245"] = "&otilde;";
        d["246"] = "&ouml;";
        d["247"] = "&divide;";
        d["248"] = "&oslash;";
        d["249"] = "&ugrave;";
        d["250"] = "&uacute;";
        d["251"] = "&ucirc;";
        d["252"] = "&uuml;";
        d["253"] = "&yacute;";
        d["254"] = "&thorn;";
        d["255"] = "&yuml;"
    }
    if (h !== "ENT_NOQUOTES") {
        d["34"] = "&quot;"
    }
    if (h === "ENT_QUOTES") {
        d["39"] = "&#39;"
    }
    d["60"] = "&lt;";
    d["62"] = "&gt;";
    for (c in d) {
        a = String.fromCharCode(c);
        f[a] = d[c]
    }
    return f
}

function htmlentities(b, e) {
    var d = {},
        c = "",
        a = "";
    a = b.toString();
    if (false === (d = this.get_html_translation_table("HTML_ENTITIES", e))) {
        return false
    }
    a = a.split("&").join("&amp;");
    d["'"] = "&#039;";
    for (c in d) {
        a = a.split(c).join(d[c])
    }
    return a
}

function htmlspecialchars(b, e) {
    var d = {},
        c = "",
        a = "";
    a = b.toString();
    if (false === (d = this.get_html_translation_table("HTML_SPECIALCHARS", e))) {
        return false
    }
    a = a.split("&").join("&amp;");
    for (c in d) {
        a = a.split(c).join(d[c])
    }
    return a
}

function number_format(f, c, h, e) {
    f = (f + "").replace(/[^0-9+\-Ee.]/g, "");
    var b = !isFinite(+f) ? 0 : +f,
        a = !isFinite(+c) ? 0 : c,
        k = (typeof e === "undefined") ? "," : e,
        d = (typeof h === "undefined") ? "." : h,
        j = "",
        g = function(o, m) {
            var l = Math.pow(10, m);
            return "" + Math.round(o * l) / l
        };
    j = (a < 0 ? ("" + b) : (a ? g(b, a) : "" + Math.round(b))).split(".");
    if (j[0].length > 3) {
        j[0] = j[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, k)
    }
    if ((j[1] || "").length < a) {
        j[1] = j[1] || "";
        j[1] += new Array(a - j[1].length + 1).join("0")
    }
    return j.join(d)
}

function CellValidator(a) {
    var b = {
        isValid: null
    };
    for (var c in b) {
        if (typeof a != "undefined" && typeof a[c] != "undefined") {
            this[c] = a[c]
        }
    }
}
CellValidator.prototype.isValid = function(a) {
    return true
};

function NumberCellValidator(a) {
    this.type = a
}
NumberCellValidator.prototype = new CellValidator;
NumberCellValidator.prototype.isValid = function(a) {
    if (isNaN(a)) {
        return false
    }
    if (this.type == "integer" && a != "" && parseInt(a) != parseFloat(a)) {
        return false
    }
    return true
};

function EmailCellValidator() {}
EmailCellValidator.prototype = new CellValidator;
EmailCellValidator.prototype.isValid = function(a) {
    return a == "" || /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(a)
};

function WebsiteCellValidator() {}
WebsiteCellValidator.prototype = new CellValidator;
WebsiteCellValidator.prototype.isValid = function(a) {
    return a == "" || (a.indexOf(".") > 0 && a.indexOf(".") < (a.length - 2))
};

function DateCellValidator(a) {
    this.grid = a
}
DateCellValidator.prototype = new CellValidator;
DateCellValidator.prototype.isValid = function(a) {
    return a == "" || typeof this.grid.checkDate(a) == "object"
};