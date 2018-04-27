var nSvgWidth = 0, nSvgHeight = 0;
var xPos = 0, yPos = 0;
$(document).ready(function () {

    $('#schedulaStart').datepicker({
        locale: 'it',
        format: 'dd/mm/yyyy',

        //endDate: '+10d',
        autoclose: true,
        useCurrent: true
    });

    $('#schedulaEnd').datepicker({
        locale: 'it',
        format: 'dd/mm/yyyy',

        //endDate: '+10d',
        autoclose: true,
        useCurrent: true
    });

    $("#reset").click(function () {
        $("input").val("");
        // $('#search').find('input, select, textarea').not("#maxtwitter").not("#ora").not("#users").val('');
    });

    $("#submit").click(function () {
        $("#nodeChart").html("");
        getRelationData(false);
        // $('#search').submit();
    });
    $("#Show").click(function () {
        $("#nodeChart").html("");
        getRelationData(true);
        // $('#search').submit();
    });

    $("#cbSelectedGraphType").change(function () {
        // $('#search').submit();
    });
    $("#Levels").val("3");
});

function drawRelationChart(strMainNodeName){
    nSvgWidth = $("#nodeChart").width() ;
    nSvgHeight = $(window).height() - $("#nodeChart").position().top - 10;
    // get the data
    d3.csv("../temp/temp.csv", function(error, links) {
        // console.log(error);
    var nodes = {};

    // Compute the distinct nodes from the links.
    links.forEach(function(link) {
        link.source = nodes[link.source] || 
            (nodes[link.source] = {name: link.source});
        link.target = nodes[link.target] || 
            (nodes[link.target] = {name: link.target});
        link.value = +link.value;
    });

    var width = nSvgWidth,
        height = nSvgHeight;

    var force = d3.layout.force()
        .nodes(d3.values(nodes))
        .links(links)
        .size([width, height])
        .linkDistance(60)
        .charge(-300)
        .on("tick", tick)
        .start();

    var svg = d3.select("#nodeChart").append("svg")
        .attr("width", width)
        .attr("height", height);

    // build the arrow.
    svg.append("svg:defs").selectAll("marker")
        .data(["end"])      // Different link/path types can be defined here
      .enter().append("svg:marker")    // This section adds in the arrows
        .attr("id", String)
        .attr("viewBox", "0 -5 10 10")
        .attr("refX", 15)
        .attr("refY", -1.5)
        .attr("markerWidth", 6)
        .attr("markerHeight", 6)
        .attr("orient", "auto")
      .append("svg:path")
        .attr("d", "M0,-5L10,0L0,5");

    // add the links and the arrows
    var path = svg.append("svg:g").selectAll("path")
        .data(force.links())
      .enter().append("svg:path")
        .attr("class", "link")
        .attr("marker-end", "url(#end)")
        .attr("source", function(d){ return d.source.name;})
        .attr("target", function(d){ return d.target.name;})
        .attr("value", function(d){return d.value;});

    var node = svg.selectAll(".node")
        .data(force.nodes())
      .enter().append("g")
        .attr("class", "node")
        .call(force.drag);

    node.append("circle")
        .attr("r", 5)
        .filter( function(d,i){return d.name == strMainNodeName;})
        .attr("class","mainCircle");

    node.append("text")
        .attr("x", 12)
        .attr("dy", ".35em")
        .attr("class", "HideItem")
        .text(function(d) { return d.name; });

    function tick() {
        path.attr("d", function(d) {
            var dx = d.target.x - d.source.x,
                dy = d.target.y - d.source.y,
                dr = Math.sqrt(dx * dx + dy * dy);
            return "M" + 
                d.source.x + "," + 
                d.source.y + "A" + 
                dr + "," + dr + " 0 0,1 " + 
                d.target.x + "," + 
                d.target.y;
        });

        node
            .attr("transform", function(d) { 
            return "translate(" + d.x + "," + d.y + ")"; });
    }
    $("circle").on("click", function(e){
      circleClicked( this.parentElement.getElementsByTagName("text")[0].innerHTML);
    })
    $("circle").hover(function(){
      this.classList.add("activeCircle");
      this.parentElement.getElementsByTagName("text")[0].classList.remove("HideItem");
      var strNodeName = this.parentElement.getElementsByTagName("text")[0].innerHTML;
      var elems = $("path").filter(function(_index){ 
        var elem = $("path").eq(_index);
        return elem.attr("source") == strNodeName || elem.attr("target") == strNodeName;});
      // for( var i = 0; i < elems.length; i ++){
        // console.log(elems);
        // debugger;
        // var strClass = elems.eq(i).attr("class");
        elems.attr("class", "link hoverLink");
        // pathHover(elems.eq(i));
      // }
    }, function(){
      this.classList.remove("activeCircle");
      this.parentElement.getElementsByTagName("text")[0].classList.add("HideItem");
      var elems = $("path").filter( function(_index){
        var elem = $("path").eq(_index);
        var strClass = elem.attr("class");
        if( typeof strClass == undefined) return false;
        if( typeof strClass == "undefined") return false;
        return strClass.indexOf("link") >= 0;
      })
      elems.attr("class", "link");
    });
    $("path").on("click", function(e){
        var strFromName = this.getAttribute("source");
        var strToName = this.getAttribute("target");
        var strValue = this.getAttribute("value");
        pathClicked( strFromName, strToName, strValue);
    });
    $("path").hover( function(){
        var strFromName = this.getAttribute("source");
        var strToName = this.getAttribute("target");
        var strValue = this.getAttribute("value");
        $("#fromName").html(strFromName);
        $("#toName").html(strToName);
        $("#linkCount").html(strValue);
        $("#LineDetails").css({left:xPos+10, top:yPos});
        $("#LineDetails").removeClass("HideItem").addClass("ShowItem");
    }, function(){
        $("#LineDetails").removeClass("ShowItem").addClass("HideItem");
    });
    });
}
// getRelationData(false);
function pathClicked(strSrcName, strTgtName, nCount){
    window.open("Intelligence_From_To.php?FromScreenName="+strSrcName+"&ToScreenName="+strTgtName + "&Count=" + nCount, "_blank");
    window.open("Twitter_From_To.php?FromScreenName="+strSrcName+"&ToScreenName="+strTgtName + "&Count=" + nCount, "_blank");
}
function circleClicked( strNodeName){
    window.open("Intelligence_individual.php?FromScreenName="+strNodeName);
    window.open("Twitter_individual.php?FromScreenName="+strNodeName);
    // document.location.href = "Intelligence_individual.php?FromScreenName="+strNodeName;
}
function getRelationData(isFirst){
    var nLevels = $("#ExtLevels").val() * 1;
    if( nLevels == 0 ) nLevels = $("#Levels").val() * 1;
    var strScreenName = $("#ScreenName").val();
    var nConversations = $("#ExtConvs").val() * 1;
    var strStartDate = $("#schedulaStart").val();
    if( strStartDate == ""){
        strStartDate = "0000";
    } else{
        strStartDate = strStartDate.split('/').reverse().join('-') + " " + $("#oraStart").val();
    }
    var strEndDate = $("#schedulaEnd").val();
    if( strEndDate == ""){
        strEndDate = "9999";
    } else{
        strEndDate = strEndDate.split('/').reverse().join('-') + " " + $("#oraEnd").val();
    }
    console.log("StartDate:" + strStartDate);
    console.log("EndDate:" + strEndDate);
    if( isFirst == false){
        if( strScreenName == "" || nLevels == 0){
            alert("Screen Name and / or levels omitted");
            return;
        }
        $(".waiting").removeClass("HideItem").addClass("ShowItem");
        var nLeft = ($(window).width() - $(".waiting").width()) / 2;
        var nTop = $(".waiting").position().top + 100;
        $(".waiting").css({left:nLeft, top: nTop, position:'absolute'});
        $.ajax({
            method: "POST",
            url: '../class/intelligence_db.php',
            data: { insertRelations: "strNumber", strScreenName: strScreenName, nLevels: nLevels, StartDate: strStartDate, EndDate: strEndDate}
        }).done( function(msg){
            console.log( "Count = " + msg);
            $(".waiting").removeClass("ShowItem").addClass("HideItem");
            if( msg * 1 == 0){
                alert("No conversation found.");
            } else{
                $.ajax({
                    method: "POST",
                    url: '../class/intelligence_db.php',
                    data: { getRelations: "strNumber", nLevels: nLevels, nConversations: nConversations, strScreenName: strScreenName, isFirst: isFirst}
                }).done( function(msg){
                    if( msg.indexOf("Y") == 0){
                        drawRelationChart( strScreenName);
                    }
                });
            }
        });
    
    } else{
        $.ajax({
            method: "POST",
            url: '../class/intelligence_db.php',
            data: { getRelations: "strNumber", nLevels: nLevels, nConversations: nConversations, strScreenName: strScreenName, isFirst: isFirst}
        }).done( function(msg){
            if( msg.indexOf("Y") == 0){
                drawRelationChart( strScreenName);
            }
        });
    }
   }
(function() {
document.onmousemove = handleMouseMove;
function handleMouseMove(event) {
    xPos = event.clientX;
    yPos = event.clientY;
}
})();