function loadLineChart() {
    loadCharts();
    drawLineChart();
}

function drawLineChart() {

    var pW = graphWidth - 100;
    var pH = graphHeight - 100;

    var g = d3.select("#graphHolder")
            .append("svg").attr("id", "graph")
            .attr("width", graphWidth)
            .attr("height", graphHeight)
            .append("g")
            .attr("transform", "translate(50,50)");

    var parseTime = d3.timeParse("%Y-%m-%d");

    var x = d3.scaleTime()
            .rangeRound([0, pW]);

    var y = d3.scaleLinear()
            .rangeRound([pH, 0]);

    var line = d3.line()
            .x(function (d) {
                return x(parseTime(d.date));
            })
            .y(function (d) {
                return y(d.close);
            });

    x.domain(d3.extent(CurrentLineChart, function (d) {
        return parseTime(d.date);
    }));
    y.domain(d3.extent(CurrentLineChart, function (d) {
        return d.close;
    }));

    g.append("g")
            .attr("transform", "translate(0," + pH + ")")
            .call(d3.axisBottom(x))
            .select(".domain")
            .remove();

    g.append("g")
            .call(d3.axisLeft(y))
            .append("text")
            .attr("fill", "#000")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", "0.71em")
            .attr("text-anchor", "end")
            .text("Confidence (%)");

    g.append("path")
            .datum(CurrentLineChart)
            .attr("fill", "none")
            .attr("stroke", "steelblue")
            .attr("stroke-linejoin", "round")
            .attr("stroke-linecap", "round")
            .attr("stroke-width", 1.5)
            .attr("d", line);


}