<?php
require_once('../config/config.php');
require_once(DOCUMENT_ROOT . '/class/connection.class.php');
require_once(DOCUMENT_ROOT . '/class/common.class.php');
// require_once(DOCUMENT_ROOT . '/class/sql.class.php');
require_once(DOCUMENT_ROOT . '/class/utility.class.php');
// require_once(DOCUMENT_ROOT . '/class/intelligence_db.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
// $objUtils = new Utility();
// $objSql = new Sql();
//print_r($_POST);  

$TipoCloudSelezionato = 'Init';
$SelectedGraphPage = "WordCloud";

$GraphPage = [["WordCloud", "Word Cloud"], ["Chord", "Chord"], ["Network", "Network"], ["LineChart", "LineChart"], ["Pie", "Pie"]];

// $rowskeyword = $objSql->SelectArray($objSql->Query('tipo_keyword', 'GROUP BY Keyword ORDER BY Keyword'));
// $rowslingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));

$IdRicerca = -1;

$addScriptHead = '
        <link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- CALENDAR -->
        <link href="../assets/plugins/timepicker/css/bootstrap-timepicker.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- SELECT -->
        <link href="../assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- Intelligence -->
    ';

echo $objCommon->head($addScriptHead);
echo '</head>';
echo '<body class="">';
// echo $objCommon->topBar();
// echo '<div class="page-container row-fluid container-fluid">';
// // echo $objCommon->menuLeft();
// echo '<section id="main-content" class=" ">';
// echo '<section class="wrapper main-wrapper row" style=\'\'>';
// echo $objCommon->locationBar();
//OTHER SCRIPTS INCLUDED ON THIS PAGE
?>
<?php
$addScriptPage = '
    <script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
         ';
echo $objCommon->jsInclude($addScriptPage);
$srcFromName = $_GET['FromScreenName'];
$srcToName = $_GET['ToScreenName'];
$nCount = $_GET['Count'];
?>
<style type="text/css">
	.avatars{ margin: auto; margin-top: 20px; position: relative;}
	img{ width: 100px; height: 100px; border-radius: 50%; border: 3px solid white;}
	p{ text-align: center; }
	.fromAvatar, .toAvatar{ width: 100px; position: absolute; z-index: 1;}
	.fromAvatar{ left: 0px; top: 0px; }
	.toAvatar{ right: 0px; top: 0px;}
	@media screen and (max-width: 480px) { 
		.fromAvatar, .toAvatar { margin: auto; }
	}
	section .content-body { padding: 20px ! important; border: none;}
	section.box{margin: 10px 0;}
	body{
		/*background-color: white;*/
	}
	#presets a { border-left: solid #666 1px; padding: 0 10px; }
	#presets a.first { border-left: none; }
	#keyword { width: 300px; }
	#fetcher { width: 500px; }
	#keyword, #go { font-size: 1.5em; }
	#text { width: 100%; height: 100px; }
	p.copy { font-size: small; }
	#form { font-size: small; position: relative; display: none;}
	hr { border: none; border-bottom: solid #ccc 1px; }
	a.active { text-decoration: none; color: #000; font-weight: bold; cursor: text; }
	#angles line, #angles path, #angles circle { stroke: #666; }
	#angles text { fill: #333; }
	#angles path.drag { fill: #666; cursor: move; }
	#angles { text-align: center; margin: 0 auto; width: 350px; }
	#angles input, #max { width: 42px; }
	#vis{ width: 100%; padding: 0px;}
	/*svg{ border: 1px solid #aaa;}*/
</style>
</style>
<script type="text/javascript" src="../assets/js/charts/chart.js"></script>
<body>
<div class="row avatars container">
	<div class="fromAvatar">
		<img src="https://twitter.com/<?= $srcFromName ?>/profile_image?size=original">
		<p><?= $srcFromName ?></p>
	</div>
	<div class="row">
		<div class="col-lg-2"></div>
	        <section class="box col-lg-8">
	            <div class="content-body">    
	                <div class="row">
	                    <div class="col-lg-5 col-md-6 col-sm-5 col-xs-6">
	                        <div class="form-group">
	                            <label class="form-label" style="font-size: 1.5em;">Focus Cloud </label>&nbsp&nbsp&nbsp
	                            <select style="font-size: 1.3em;">
	                            	<option>Word</option>
	                            	<option>ScreenName</option>
	                            	<option>HashTag</option>
	                            </select>
	                        </div>
	                    </div>

	                    <!-- <div class="col-lg-1 col-md-1"></div> -->

	                    <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6">
	                        <button type="button" name="action" value="search" id="submit" class="btn btn-success btn-block">Show</button>
	                    </div>

	                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
	                        <button type="button" name="action" value="search" id="download" class="btn btn-success btn-block">Download Images</button>
	                    </div>
	                </div>
	            </div>
	        </section>
		<div class="col-lg-2"></div>
	</div>
	<div class="toAvatar">
		<img src="https://twitter.com/<?= $srcToName ?>/profile_image?size=original">
		<p><?= $srcToName ?></p>
	</div>
</div>
<form name="search" id="search" class="container">

    <div class="col-xs-12">
        <section class="box">
			<div id="vis"></div>
        </section>
    </div>
</form>


<form id="form">
  <p style="position: absolute; right: 0; top: 0" id="status"></p>

  <div style="text-align: center">
    <div id="presets"></div>
    <div id="custom-area">
      <p><label for="text">Paste your text below!</label>
      <p><textarea id="text" onchange="changed()">
      </textarea>
      <button id="go" type="submit">Go!</button>
    </div>
  </div>

  <hr>

  <div style="float: right; text-align: right">
    <p><label for="max">Number of words:</label> <input type="number" value="250" min="1" id="max">
    <p><label for="per-line"><input type="checkbox" id="per-line"> One word per line</label>
    <p><label>Download:</label>
      <button id="download-svg">SVG</button>
  </div>

  <div style="float: left">
    <p><label>Spiral:</label>
      <label for="archimedean"><input type="radio" name="spiral" id="archimedean" value="archimedean" checked="checked"> Archimedean</label>
      <label for="rectangular"><input type="radio" name="spiral" id="rectangular" value="rectangular"> Rectangular</label>
    <p><label for="scale">Scale:</label>
      <label for="scale-log"><input type="radio" name="scale" id="scale-log" value="log" checked="checked"> log n</label>
      <label for="scale-sqrt"><input type="radio" name="scale" id="scale-sqrt" value="sqrt"> √n</label>
      <label for="scale-linear"><input type="radio" name="scale" id="scale-linear" value="linear"> n</label>
    <p><label for="font">Font:</label> <input type="text" id="font" value="Impact">
  </div>

  <div id="angles">
    <p><input type="number" id="angle-count" value="5" min="1"> <label for="angle-count">orientations</label>
      <label for="angle-from">from</label> <input type="number" id="angle-from" value="-60" min="-90" max="90"> °
      <label for="angle-to">to</label> <input type="number" id="angle-to" value="60" min="-90" max="90"> °
  </div>
  <hr style="clear: both">
</form>
</body>

<script src="../assets/js/d3.min.js?"></script>
<script src="../assets/js/cloud_w.min.js?"></script>
<script type="text/javascript" src="../assets/js/saveSvgAsPng.js"></script>

<script type="text/javascript">
	var strFromName = $(".fromAvatar p").html();
	var strToName = $(".toAvatar p").html();

	$("#download").on("click", function(){
		saveSvgAsPng(document.getElementsByTagName("svg")[0], strFromName + "_" + strToName + "_" + $("select").val() + ".png", {backgroundColor: "white"});
	});
	$("#submit").on("click", function(){
		var strKind = $("select").val();
		console.log(strKind);
		switch( strKind){
			case 'Word': drawWords(); break;
			case 'ScreenName': drawScreenName(); break;
			case 'HashTag': drawHashtag(); break;

		}
	});
	function setDimention(){
		var svgWidth = $("#vis").outerWidth();
		var svgHeight = window.innerHeight - $("svg").offset().top-20;
		var nDimention = svgWidth > svgHeight ? svgHeight : svgWidth;
		var isWide = svgWidth > svgHeight ? true : false;
		w = nDimention;
		h = nDimention;
		if( isWide ){
			var nPadding = (svgWidth - nDimention) / 2;
			$("#vis").css("padding-left",nPadding+"px").css("padding-right",nPadding+"px");
		} else{
			var nPadding = (svgHeight - nDimention) / 2;
			$("#vis").css("padding-top",nPadding+"px").css("padding-bottom",nPadding+"px");
		}
		$("svg").attr("width", nDimention);
		$("svg").attr("height", nDimention);
	}
	function drawWords(){
		setDimention();
		$.ajax({
			method: "POST",
			url: '../class/intelligence_db.php',
			data: { getWord: "getWord", FromScreenName: strFromName, ToScreenName: strToName}
		}).done( function(msg){
			parseText( msg);
		});
	}
	function drawScreenName(){
		setDimention();
		$.ajax({
			method: "POST",
			url: '../class/intelligence_db.php',
			data: { getScreenName: "getScreenName", FromScreenName: strFromName, ToScreenName: strToName}
		}).done( function(msg){
			parseText( msg);
		});
	}
	function drawHashtag(){
		setDimention();
		$.ajax({
			method: "POST",
			url: '../class/intelligence_db.php',
			data: { getHashtag: "getHashtag", FromScreenName: strFromName, ToScreenName: strToName}
		}).done( function(msg){
			console.log(msg);
			parseText( msg);
		});
	}
	setDimention();
	// parseText(" ");
	// drawWords();
</script>