var networkChart = {
    vis: null,
    nodes: [],
    labelAnchors: [],
    labelAnchorLinks: [],
    links: [],
    force: null,
    force2: null
};

function loadNetwork() {

    loadCharts();
    
    // clear network, if available
    if (networkChart.force !== null) {
        networkChart.force.stop();
    }
    if (networkChart.force2 !== null) {
        networkChart.force2.stop();
    }
    networkChart.nodes = [];
    networkChart.labelAnchors = [];
    networkChart.labelAnchorLinks = [];
    networkChart.links = [];
    
    drawNetwork();
}

function drawNetwork() {

    buildNetwork();

    $("#hint").html("Move the mouse over any language to show further information or click to grab the bubble around.");

    networkChart.vis = d3.select("#graphHolder").append("svg:svg").attr("id", "graph").attr("width", graphWidth).attr("height", graphHeight);

    networkChart.force =
            d3.layout.force()
            .size([graphWidth, graphHeight])
            .nodes(networkChart.nodes)
            .links(networkChart.links)
            .gravity(1)
            .linkDistance(function (x) {
                return 50; //(x.weight * (graphWidth / 2));
            }).charge(-1000)
            .linkStrength(function (x) {
                return x.weight * 10;
            });

    networkChart.force.start();

    // brings everything towards the center of the screen
    networkChart.force2 = d3.layout.force()
            .nodes(networkChart.labelAnchors).links(networkChart.labelAnchorLinks)
            .gravity(0).linkDistance(0).linkStrength(8).charge(-100).size([graphWidth, graphHeight]);
    networkChart.force2.start();

    var link = networkChart.vis.selectAll("line.link")
            .data(networkChart.links).enter()
            .append("svg:line").attr("class", "link")
            .style("stroke", function (d, i) {
                return d.color
            })
            .style("stroke-width", function (d, i) {
                return d.weight * 10;
            });

    var node =
            networkChart.vis.selectAll("g.node")
            .data(networkChart.force.nodes())
            .enter()
            .append("svg:g")
            .attr("id", function (d, i) {
                return d.label;
            })
            .attr("class", "node");

    node.append("svg:circle")
            .attr("id", function (d, i) {
                return "c_" + d.label;
            })
            .attr("r", function (d, i) {
                return d.size;
            })
            .style("fill", function (d, i) {
                return d.color;
            })
            .style("stroke", "#000")
            .style("stroke-width", 1);

    node.call(networkChart.force.drag);

    node.on("mouseover", function (d) {
        showInformation(d.label);
    });

    var anchorLink = networkChart.vis.selectAll("line.anchorLink")
            .data(networkChart.labelAnchorLinks);

    var anchorNode = networkChart.vis.selectAll("g.anchorNode")
            .data(networkChart.force2.nodes()).enter()
            .append("svg:g").attr("class", "anchorNode");

    anchorNode.append("svg:circle")
            .attr("id", function (d, i) {
                return "ct_" + d.node.label
            })
            .attr("r", 0).style("fill", "#FFF");

    anchorNode.append("svg:text")
            .attr("id", function (d, i) {
                return "t_" + d.node.label
            })
            .text(function (d, i) {
                return (i % 2 == 0) ? "" : (d.node.label);
            })
            .style("fill", function (d, i) {
                return d.node.textcolor;
            })
            .style("font-family", "Arial")
            .style("font-size", 12)
            .on("mouseover", function (d) {
                showInformation(d.node.label);
            });

    var updateLink = function () {
        this.attr("x1", function (d) {
            return d.source.x;
        }).attr("y1", function (d) {
            return d.source.y;
        }).attr("x2", function (d) {
            return d.target.x;
        }).attr("y2", function (d) {
            return d.target.y;
        });

    }

    var updateNode = function () {
        this.attr("transform", function (d) {
            return "translate(" + d.x + "," + d.y + ")";
        });

    }

    networkChart.force.on("tick", function () {
        networkChart.force2.start();
        node.call(updateNode);
        anchorNode.each(function (d, i) {
            if (i % 2 == 0) {
                d.x = d.node.x;
                d.y = d.node.y;
            } else {
                var b = this.childNodes[1].getBBox();
                var diffX = d.x - d.node.x;
                var diffY = d.y - d.node.y;
                var dist = Math.sqrt(diffX * diffX + diffY * diffY);
                var shiftX = b.width * (diffX - dist) / (dist * 2);
                shiftX = Math.max(-b.width, Math.min(0, shiftX));
                var shiftY = 5;
                this.childNodes[1].setAttribute("transform", "translate(" + shiftX + "," + shiftY + ")");
            }
        });
        anchorNode.call(updateNode);
        link.call(updateLink);
        anchorLink.call(updateLink);
    });

}

function buildNetwork() {

    var newMapping = [];
    var k = 0;
    for (var i = 0; i < nodesArray.length; i++) {
        var node = nodesArray[i];
        var draw = true;
        if (hideUnrelated) {
            if (getAmountLinks(i) == 0) {
                draw = false;
            }
        }
        if (draw) {
            newMapping[i] = k;
            networkChart.nodes.push(node);
            networkChart.labelAnchors.push({node: node});
            networkChart.labelAnchors.push({node: node});
            k++;
        } else {
            newMapping[i] = -1;
        }
    }

    for (var j = 0; j < linksArray.length; j++) {
        var link = linksArray[j];
        var sim = link.weight;
        adjustSlider(sim);

        // just draw the links if similarity is higher than the threshold
        // or the nodes exist
        if (sim >= similarityThreshold / 100.0 && newMapping[link.source] != -1 && newMapping[link.target] != -1) {
            var newLink = {source: newMapping[link.source], target: newMapping[link.target], weight: sim, color: link.color};
            networkChart.links.push(newLink);
        }
    }

    // link labels to circles
    for (var i = 0; i < networkChart.nodes.length; i++) {
        networkChart.labelAnchorLinks.push({source: i * 2, target: i * 2 + 1, weight: 1});
    }
}

function getAmountLinks(n) {
    var linksAmount = 0;
    for (var j = 0; j < linksArray.length; j++) {
        var link = linksArray[j];
        if ((link.source == n || link.target == n) && link.weight >= similarityThreshold / 100.0) {
            linksAmount++;
        }
    }
    return linksAmount;
}

function showInformation(language) {
    var url = "http://en.wikipedia.org/wiki/" + language + "_language";
    var n = nodesHash[language];
    $('#language_information').html(nodesArray[n].desc);
}