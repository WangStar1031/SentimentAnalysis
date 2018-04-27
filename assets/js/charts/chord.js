var chordChart = {
    links: [], // Square matrix
    data: []
};

var newMapping = [];
var oldMapping = [];

function loadChord() {

    loadCharts();

    // clear chord, if available
    chordChart.links = [];
    chordChart.data = [];

    drawChord();
}

function buildChord() {
    oldMapping = [];
    newMapping = [];

    var k = 0;

    for (var i = 0; i < CurrentChordNodes.length; i++) {
        var node = CurrentChordNodes[i];
        var draw = true;
        if (hideUnrelated) {
            if (getAmountLinks(i) === 0) {
                draw = false;
            }
        }

        if (draw) {
            newMapping[i] = k;
            oldMapping[k] = i;
            k++;
        } else {
            newMapping[i] = -1;
        }
    }

    for (var i = 0; i < CurrentChordLinks.length; i++) {
        var link = CurrentChordLinks[i];
        var lang1 = CurrentChordNodes[link.source];
        var lang2 = CurrentChordNodes[link.target];
        var sim = link.weight;
        adjustSlider(sim);

        // just draw the links if similarity is higher than the threshold
        // or the nodes exist
        if (sim >= similarityThreshold / 100.0 && newMapping[link.source] != -1 && newMapping[link.target] != -1) {
            chordChart.data.push({
                source: lang1,
                target: lang2,
                size: lang1.size,
                similarity: sim,
                color: link.color
            });
        }
    }
    chordChart.data.forEach(function (d) {
        d.source.similarity = d.similarity;
        d.target.similarity = d.similarity;
        d.valueOf = value; // convert object to number implicitly
    });

    // Initialize link matrix
    for (var i = 0; i < k; i++) {
        chordChart.links[i] = [];
        for (var j = 0; j < k; j++) {
            chordChart.links[i][j] = 0;
        }
    }
    // Populate the link matrix with actual values
    chordChart.data.forEach(function (d) {
        chordChart.links[newMapping[d.source.id]][newMapping[d.target.id]] = d;
    });

    function value() {
        return +this.size;
    }
}

function drawChord() {

    buildChord();

    $("#hint").html("Move the mouse over any language to hide all others.");

    // Chart dimensions.
    var r1 = Math.min(graphWidth, graphHeight) / 2 - 20;
    var r0 = r1 - 100;

    // The chord layout, for computing the angles of chords and groups.
    var layout = d3.layout.chord()
            //.sortGroups(d3.descending)
            .sortSubgroups(d3.descending)
            .padding(6.28 / chordChart.links.length)
            .matrix(chordChart.links);

    // The arc generator for the groups
    var arc = d3.svg.arc().innerRadius(r0).outerRadius(r1);

    // The chord generator (quadratic BÃ©zier) for the chords
    var chord = d3.svg.chord().radius(r0);

    // Add an SVG element
    var svg = d3.select("#graphHolder")
            .append("svg").attr("id", "graph")
            .attr("width", graphWidth)
            .attr("height", graphHeight)
            .append("g")
            .attr("transform", "translate(" + (10 + graphWidth / 2) + "," + graphHeight / 2 + ")");

    // Add chords
    svg.selectAll("path")
            .data(layout.chords)
            .enter().append("path")
            .attr("class", "chord")
            .style("fill", function (d) {
                return d.source.value.color;
            })
            .style("stroke", function (d) {
                return d.source.value.color;
            })
            .attr("d", chord);

    // Add groups
    var g = svg.selectAll("g.group")
            .data(layout.groups)
            .enter().append("g")
            .attr("class", "group");

    // Add the group arc
    g.append("path")
            .on("mouseover", fade(0))
            .on("mouseout", fade(1))
            .on("click", chordClick)
            .style("fill", function (d) {
                return CurrentChordNodes[oldMapping[d.index]].color;
            })
            .attr("id", function (d, i) {
                return "group" + d.index;
            })
            .attr("d", arc);

    // Add the language label
    g.append("svg:text")
            .on("mouseover", fade(0))
            .on("mouseout", fade(1))
            .on("click", chordClick)
            .attr("x", 6)
            .attr("dy", 15)
            .attr("transform", function (d) {
                return "rotate(" + (getMeanAgle(d) * 180 / Math.PI - 90) + ")"
                        + "translate(" + r0 + "," + (-5 - 50 * (d.endAngle - d.startAngle)) + ")";
            })
            .style("fill", function (d) {
                return d3.rgb(CurrentChordNodes[oldMapping[d.index]].textcolor).darker();
            })
            .style("font-size", function (d) {
                return 9 + 100 * (d.endAngle - d.startAngle);
            })
            .text(function (d) {
                return CurrentChordNodes[oldMapping[d.index]].label;
            });



    function getMeanAgle(d) {
        return d.startAngle + (d.endAngle - d.startAngle) / 2;
    }

    /** Returns an event handler for fading a given chord group. */
    function fade(opacity) {
        return function (g, i) {
            d3.select("#tooltip")
                    .style("visibility", "visible")
                    .html(groupTip(CurrentChordNodes[i], false))
                    .style("top", function () {
                        return (d3.event.pageY - 500) + "px"
                    })
                    .style("left", function () {
                        return (d3.event.pageX - 250) + "px";
                    });

            if (opacity === 1)
                d3.select("#tooltip").style("visibility", "hidden");

            svg.selectAll("path.chord")
                    .filter(function (d) {
                        return d.source.index !== i && d.target.index !== i;
                    })
                    .transition()
                    .style("opacity", opacity);
        };
    }
    function chordTip(d) {
        var p = d3.format(".1%"), q = d3.format(",.2r")
        return "Chord Info:<br/>"
                + d.sname + " → " + d.tname
                + ": " + p(d.svalue) + "<br/>"
                + d.tname + " → " + d.sname
                + ": " + p(d.tvalue) + "<br/>";
    }

    function groupTip(d, links) {
        var RetStr = "";
        var linksAmount = 0;

        for (var j = 0; j < CurrentChordLinks.length; j++) {
            var link = CurrentChordLinks[j];
            if ((link.source === d.id) && link.weight >= similarityThreshold / 100.0) {
                RetStr += link.desc + " [" + link.weight + "]<br/>";
                linksAmount++;
            }
        }
        if (links)
            RetStr = "Label : " + d.label + " - " + linksAmount + " Links <br/>" + RetStr;
        else
            RetStr = "Label : " + d.label + " - " + linksAmount + " Links <br/>";

        return RetStr;
    }

    function chordClick(d, i)
    {
        d3.select("#chordDett")
                .style("visibility", "visible")
                .html(groupTip(CurrentChordNodes[i], true));
    }
}

function getAmountLinks(n) {
    var linksAmount = 0;
    for (var j = 0; j < CurrentChordLinks.length; j++) {
        var link = CurrentChordLinks[j];
        if ((link.source == n || link.target == n) && link.weight >= similarityThreshold / 100.0) {
            linksAmount++;
        }
    }
    return linksAmount;
}

function showInformation(language) {
    var url = "http://en.wikipedia.org/wiki/" + language + "_language";
    var n = CurrentNodeHash[language];
    $('#language_information').html(CurrentChordNodes[n].desc);
}


