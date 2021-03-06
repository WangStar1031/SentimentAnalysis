function loadPie() {

    loadCharts();


    drawPie();
}

function drawPie() {

    //var svg = d3.select("graphHolder");
    var radius = Math.min(graphWidth, graphHeight) / 2;
    var PieWidth = graphWidth;
    var PieHeight = graphHeight;

    
    var svg = d3.select("#graphHolder")
            .append("svg")
            //.attr("id", "graph")
            .attr("width", graphWidth)
            .attr("height", graphHeight)
            .append("g")
            .attr("transform", "translate(" + graphWidth / 2 + "," + graphHeight / 2 + ")");

    //var svg = d3.select("graphHolder").append("svg").append("g");
    //var svg = d3.select("#graphHolder").append("svg").append("g");

    svg.append("g")
            .attr("class", "slices");
    svg.append("g")
            .attr("class", "labels");
    svg.append("g")
            .attr("class", "lines");

    var pie = d3.layout.pie()
            .sort(null)
            .value(function (d) {
                return d.conteggio;
            });

    var arc = d3.svg.arc()
            .outerRadius(radius * 0.8)
            .innerRadius(radius * 0.4);

    var outerArc = d3.svg.arc()
            .innerRadius(radius * 0.9)
            .outerRadius(radius * 0.9);

    //svg.attr("transform", "translate(" + 0 + "," + 0 + ")");

    var key = function (d) {
        return d.data.confidence;
    };

    var color = d3.scale.category20c();

    change(pieData);

    function change(data) {

        /* ------- PIE SLICES -------*/
        var slice = svg.select(".slices").selectAll("path.slice")
                .data(pie(data), key);

        slice.enter()
                .insert("path")
                .style("fill", function (d, i) {
                    return color(i); //d.data.confidence);
                })
                .attr("class", "slice");

        slice
                .transition().duration(1000)
                .attrTween("d", function (d) {
                    this._current = this._current || d;
                    var interpolate = d3.interpolate(this._current, d);
                    this._current = interpolate(0);
                    return function (t) {
                        return arc(interpolate(t));
                    };
                })

        slice.exit()
                .remove();

        /* ------- TEXT LABELS -------*/

        var text = svg.select(".labels").selectAll("text")
                .data(pie(data), key);

        text.enter()
                .append("text")
                .attr("dy", ".35em")
                .text(function (d) {
                    return d.data.confidence;
                });

        function midAngle(d) {
            return d.startAngle + (d.endAngle - d.startAngle) / 2;
        }

        text.transition().duration(1000)
                .attrTween("transform", function (d) {
                    this._current = this._current || d;
                    var interpolate = d3.interpolate(this._current, d);
                    this._current = interpolate(0);
                    return function (t) {
                        var d2 = interpolate(t);
                        var pos = outerArc.centroid(d2);
                        //pos[0] = radius * (midAngle(d2) < Math.PI ? 1 : -1);
                        return "translate(" + pos + ")";
                    };
                })
                .styleTween("text-anchor", function (d) {
                    this._current = this._current || d;
                    var interpolate = d3.interpolate(this._current, d);
                    this._current = interpolate(0);
                    return function (t) {
                        var d2 = interpolate(t);
                        return midAngle(d2) < Math.PI ? "start" : "end";
                    };
                });

        text.exit()
                .remove();

        /* ------- SLICE TO TEXT POLYLINES -------*/

        var polyline = svg.select(".lines").selectAll("polyline")
                .data(pie(data), key);

        polyline.enter()
                .append("polyline");

        polyline.transition().duration(1000)
                .attrTween("points", function (d) {
                    this._current = this._current || d;
                    var interpolate = d3.interpolate(this._current, d);
                    this._current = interpolate(0);
                    return function (t) {
                        var d2 = interpolate(t);
                        var pos = outerArc.centroid(d2);
                        //pos[0] = radius * 0.95 * (midAngle(d2) < Math.PI ? 1 : -1);
                        return [arc.centroid(d2), outerArc.centroid(d2), pos];
                    };
                });

        polyline.exit()
                .remove();
    }
    ;
}
