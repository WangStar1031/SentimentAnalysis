function loadPie() {

    loadCharts();


    drawPie();
}

function drawPie() {

    var svg = d3.select("graphHolder");
    var radius = Math.min(graphWidth, graphHeight) / 2;

    var g = d3.select("#graphHolder")
            .append("svg").attr("id", "graph")
            .attr("width", graphWidth)
            .attr("height", graphHeight)
            .append("g")
            .attr("transform", "translate(" + graphWidth / 2 + "," + graphHeight / 2 + ")");

    //var g = svg.append("g").attr("transform", "translate(" + graphWidth / 2 + "," + graphHeight / 2 + ")");

    var color = d3.scaleOrdinal(d3.schemeCategory20c);
    //var color = d3.scaleOrdinal(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

    var pie = d3.pie()
            .sort(null)
            .value(function (d) {
                return d.conteggio;
            });

    var path = d3.arc()
            .outerRadius(radius - 10)
            .innerRadius(100);

    var label = d3.arc()
            .outerRadius(radius - 50)
            .innerRadius(radius - 50);


    var arc = g.selectAll(".arc")
            .data(pie(pieData))
            .enter().append("g")
            .attr("class", "arc");

    arc.append("path")
            .attr("d", path)
            .attr("fill", function (d, i) {
                return color(i); //d.data.confidence);
            });

    arc.append("text")
            .attr("transform", function (d) {
                var NewAngle = ((d.startAngle + d.endAngle) / 2 * (180 / Math.PI)) + 90;
                return "translate(" + label.centroid(d) + "), rotate(" + NewAngle + ")";
            })
            .attr("dy", "0.35em")
            .text(function (d) {
                return d.data.confidence;
            })
            ;

}
