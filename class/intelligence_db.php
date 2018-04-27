<?php

	require_once('../config/config.php');
	require_once(DOCUMENT_ROOT . '/class/connection.class.php');
    require_once(DOCUMENT_ROOT . '/class/sql.class.php');
	function db_connect()
    {
        $mysqli = new mysqli(CONFIG_SERVER, CONFIG_DB_USER, CONFIG_DB_PWD, CONFIG_DB);
        return $mysqli;
    }

    function db_close( $mysqli)
    {
        $mysqli->close();
    }
	function insertRelations( $strScreenName, $nLevels, $StartDate, $EndDate){
		$objSql = new Sql();
		$sql = "CALL sp_InsertRelations('$strScreenName', $nLevels, '$StartDate', '$EndDate', @p4)";
        $mysqli = new mysqli(CONFIG_SERVER, CONFIG_DB_USER, CONFIG_DB_PWD, CONFIG_DB);
		$mysqli = db_connect();
		// echo $sql;
		$mysqli->query($sql);
		// $sql = "SELECT count(*) into RecordNumber FROM Relations;";
		$sql = "SELECT count(*) AS RecordNumber FROM relations;";
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['insertRelations'])){
		$strScreenName = $_POST['strScreenName'];
		$nLevels = $_POST['nLevels'];
		$StartDate = $_POST['StartDate'];
		$EndDate = $_POST['EndDate'];
		$rows = insertRelations( $strScreenName, $nLevels, $StartDate, $EndDate);
		echo $rows[0]['RecordNumber'];
	}
	function getRelations( $isFirst, $ScreenName, $nLevels, $nConversations, $StartDate, $StartTime, $EndDate, $EndTime){
		$objSql = new Sql();
		if( $isFirst == true){
			$sql = "CALL sp_InsertRelations($ScreenName, $nLevels, $StartDate, $EndDate)";
			// $objSql->SelectArray($sql);
		}
		// $sql = "CALL sp_GetGraphData(99, 999)";
		$sql = "CALL sp_GetGraphData($nLevels,$nConversations)";
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if (isset($_POST['getRelations'])) {
		$nLevels = $_POST['nLevels'];
		if($nLevels == 0) $nLevels = 3;
		$nConversations = $_POST['nConversations'];
		$strScreenName = "";
		if( isset($_POST['strScreenName']))
			$strScreenName = $_POST['strScreenName'];
		$isFirst = false;
		if( isset($_POST['isFirst'])){
			$isFirst = $_POST['isFirst'];
		}
		$StartDate = '0000';
		if( isset($_POST['StartDate'])){
			$StartDate = $_POST['StartDate'];
		}
		$EndDate = '9999';
		if( isset($_POST['EndDate'])){
			$EndDate = $_POST['EndDate'];
		}
		if( $nConversations == 0) $nConversations = 1;
		$rows = getRelations( $isFirst, $strScreenName, $nLevels,$nConversations,$StartDate,0,$EndDate,0);
		$contents = "";
		$contents .= "source,target,value\n";
		for($i = 0; $i < count($rows); $i++){
			$contents .= $rows[$i]['FromScreenName'].",".$rows[$i]['ToScreenName'].",".$rows[$i]['NumbersTweet']."\n";
		}
		file_put_contents(DOCUMENT_ROOT.'/temp/temp.csv', $contents);
		echo "Y";
	}
	function getWord( $FromScreenName, $ToScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' and ToScreenName = '$ToScreenName' ) AND AT = FALSE AND Hashtag = FALSE AND URL = FALSE AND Word NOT IN ( SELECT StopWord FROM StopWords ) GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		// echo $sql;
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getWord'])){
		$FromScreenName = $_POST['FromScreenName'];
		$ToScreenName = $_POST['ToScreenName'];
		$rows = getWord($FromScreenName, $ToScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
	function getScreenName( $FromScreenName, $ToScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' and ToScreenName = '$ToScreenName' ) AND AT = true GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		// echo $sql;
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getScreenName'])){
		$FromScreenName = $_POST['FromScreenName'];
		$ToScreenName = $_POST['ToScreenName'];
		$rows = getScreenName($FromScreenName, $ToScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
	function getHashtag( $FromScreenName, $ToScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' and ToScreenName = '$ToScreenName' ) AND Hashtag = true GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		// echo $sql;
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getHashtag'])){
		$FromScreenName = $_POST['FromScreenName'];
		$ToScreenName = $_POST['ToScreenName'];
		$rows = getHashtag($FromScreenName, $ToScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
	function getIndividualWord( $FromScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' or ToScreenName = '$FromScreenName' ) AND AT = FALSE AND Hashtag = FALSE AND URL = FALSE AND Word NOT IN ( SELECT StopWord FROM StopWords ) GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getIndividualWord'])){
		$FromScreenName = $_POST['FromScreenName'];
		$rows = getIndividualWord($FromScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
	function getIndividualtScreenName( $FromScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' or ToScreenName = '$FromScreenName' ) AND AT = true GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getIndividualtScreenName'])){
		$FromScreenName = $_POST['FromScreenName'];
		$rows = getIndividualtScreenName($FromScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
	function getIndividualHashtag( $FromScreenName){
		$objSql = new Sql();
		$sql = "SELECT Word, SUM(WordCount) AS Conteggio FROM AnalisiTweetsWordCount WHERE IdTweet IN ( SELECT IdTweet FROM Relations where FromScreenName = '$FromScreenName' or ToScreenName = '$FromScreenName' ) AND Hashtag = true GROUP BY Word ORDER BY Conteggio DESC LIMIT 50";
		
		$rows = $objSql->SelectArray($sql);
		return $rows;
	}
	if( isset($_POST['getIndividualHashtag'])){
		$FromScreenName = $_POST['FromScreenName'];
		$rows = getIndividualHashtag($FromScreenName);
		$contents = "";
		for( $i = 0; $i < count($rows); $i ++){
			for($j = 0; $j < intval($rows[$i]['Conteggio']); $j++){
				$contents .= "'".$rows[$i]['Word']."' ";
			}
		}
		echo $contents;
	}
?>
