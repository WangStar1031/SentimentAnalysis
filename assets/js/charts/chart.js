var graphWidth = 0, graphHeight = 0;

var hideUnrelated = true;

var similarityThresholdMin = 100;
var similarityThresholdMax = 0;
var similarityThreshold = 30;

//adjust the scala of the slider
function adjustSlider(sim) {
    if (sim * 100 > similarityThresholdMax) {
        similarityThresholdMax = sim * 100;
    } else if (sim * 100 < similarityThresholdMin) {
        similarityThresholdMin = sim * 100;
    }
}

function loadCharts() {

    if (d3.select("#graph") != null) {
        d3.select("#graph").remove();
    }
    graphWidth = $('#graphHolder').width();
    graphHeight = 600; //$('#graphHolder').height();
}

