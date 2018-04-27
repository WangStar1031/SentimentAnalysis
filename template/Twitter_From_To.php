<?php
header('Content-Type: text/html; charset=utf-8');
require_once('../config/config.php');
require_once(DOCUMENT_ROOT . '/class/connection.class.php');
require_once(DOCUMENT_ROOT . '/class/common.class.php');
require_once(DOCUMENT_ROOT . '/class/sql.class.php');
require_once(DOCUMENT_ROOT . '/class/utility.class.php');

$objCommon = new Common();

$TipoCloudSelezionato = 'Init';
$SelectedGraphPage = "WordCloud";

$GraphPage = [["WordCloud", "Word Cloud"], ["Chord", "Chord"], ["Network", "Network"], ["LineChart", "LineChart"], ["Pie", "Pie"]];

$IdRicerca = -1;
?>

<meta charset="ISO-8859-1">
<?php
$addScriptHead = '
        <link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="../assets/css/googleOopenSan.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="../assets/css/Roboto.css" rel="stylesheet" type="text/css" media="screen"/>
    ';

echo $objCommon->head($addScriptHead);
echo '</head>';
// header('Content-Type: text/html; charset=UTF-8');
echo '<body class="">';
?>
<!-- https://fonts.googleapis.com/css?family=Open+Sans:100,400,300,500,600,700
https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700 -->
<?php
$addScriptPage = '
    <script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
         ';
echo $objCommon->jsInclude($addScriptPage);
$srcFromName = $_GET['FromScreenName'];
$srcToName = $_GET['ToScreenName'];

$objSql = new Sql();
$sql = "SELECT IdTweet, Keyword, TweetTimeStampStringa, TweetText, ScreenName FROM AnalisiTweets where IdTweet in ( SELECT IdTweet FROM Relations where FromScreenName = '$srcFromName' and ToScreenName = '$srcToName' );";
// $sql = "CALL sp_GetFromTo_Tweet('$srcFromName', '$srcToName')";
// $sql = "CALL sp_GetFromTo_Tweet_Tweet('$srcFromName', '$srcToName')";
$rows = $objSql->SelectArray($sql);
?>
<style type="text/css">
	.EmbeddedTweet-tweet { padding: 20px 20px 11.6px;}
	.sharpCls{ color: #cc0000; }
	.avatars{ margin: auto; margin-top: 20px; position: relative;}
	img{ width: 100px; height: 100px; border-radius: 50%; border: 3px solid white;}
	p{ text-align: center; }
	.fromAvatar, .toAvatar{ width: 100px; z-index: 1;}
	.fromAvatar{ float: left; }
	.toAvatar{ float: right; }
	table{ width: 100%; }
	body{ background-color: white; }
	.leftTd{text-align: left; width: 25%;}
	/*.rightTd{text-align: right; width: 25%;}*/
	.centerTd{ border: 1px solid #eee; width: 74%;}
	table tr th{ text-align: center ! important; background-color: #e8e8e8; padding-top: 20px; padding-bottom: 20px; color: black;}
	table tr td{ padding: 15px;}
	.smallImg{ width: 46px; height: 46px; float: left;}
	.smallName{ line-height: 50px; font-weight: bold; padding-left: 70px; color: black;}
	.smallName:hover{ color: #c00; }
	.leftJustify, .rightJustify{ width: 70%; }
	.leftJustify{ float: left; }
	.rightJustify{ float: right; }
</style>
</style>
<script type="text/javascript" src="../assets/js/charts/chart.js"></script>
<body>
<div class="row avatars container">
	<div class="row">
		<div class="fromAvatar">
			<img src="https://twitter.com/<?= $srcFromName ?>/profile_image?size=original">
			<p><?= $srcFromName ?></p>
		</div>
		<div class="toAvatar">
			<img src="https://twitter.com/<?= $srcToName ?>/profile_image?size=original">
			<p><?= $srcToName ?></p>
		</div>		
	</div>
	<div class="row col-lg-12">
		<table>
			<tr>
				<th class="leftTd">Keyword<br>Date<br>Id Tweet</th>
				<th class="centerTd">Tweet</th><!-- 
				<th class="rightTd">Keyword<br>Date<br>Id Tweet</th> -->
			</tr>
	<?php
		// for($i = 0; $i < 10; $i++){
		for($i = 0; $i < count($rows); $i++){
			$IdTweet = $rows[$i]['IdTweet'];
			$Keyword = $rows[$i]['Keyword'];
			$TweetTimeStampStringa = $rows[$i]['TweetTimeStampStringa'];
			$TweetText = $rows[$i]['TweetText'];
			$ScreenName = $rows[$i]['ScreenName'];
	?>
			<tr>
				<td class="leftTd">
					<?php
					if( $srcFromName == $ScreenName){
						echo $Keyword . "<br/>" . $TweetTimeStampStringa . "<br/>Id:" . $IdTweet;
					}
					?>						
				</td>
				<td class="centerTd">
			<?php
				echo '<div class="tweet ';
				// if( $srcFromName == $ScreenName){ echo 'leftJustify'; }
				// else echo 'rightJustify';
				echo '" id="'.$IdTweet.'">';
				echo '<div hidden>';
				echo '<h4>Tweet not available on line.</h4>';
				echo '<br />';
				echo '<h5>Stored Text:</h5>';
				echo '<div class="time">'.$TweetTimeStampStringa.'</div>';
				echo '<label style="background-color: white; border: solid; border-color: black; border-radius: 5px; border-width: 1px; padding: 5px;">' . $TweetText.'</label>';
				echo '</div>';
				echo '</div>';

					// echo "<div class='";
					// if( $srcFromName == $ScreenName){
					// 	echo "leftJustify'>";
					// } else{
					// 	echo "rightJustify'>";
					// }
					// $text = parsingTwitter($TweetText, $TweetTimeStampStringa);
					// echo str_replace("@", " ", $text);
					// echo "</div>";
			 ?>
			 	</td><!-- 
				<td class="rightTd">
					<?php
					if( $srcFromName != $ScreenName){
						echo $Keyword . "<br/>" . $TweetTimeStampStringa . "<br/>Id:" . $IdTweet;
					}
					?>
				</td> -->
			</tr>
	<?php
			// echo $i . " : " . $IdTweet . " " . $Keyword . " " . $TweetTimeStampStringa ." " . $TweetText . " " . $ScreenName;
			// echo "********************<br/><br/>";
		}
	?>
		</table>
	</div>
</div>

</body>

<script type="text/javascript" src="../assets/js/tweet/widgets.js"></script>

<script type="text/javascript">
	var strAlphaNumber = 'abcdefghijklmnopqrstuvwxyz_1234567890';
	var arrMonthes = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	function getScreenName( _strContents){
		console.log( _strContents);
		var nStartPos = _strContents.indexOf('@');
		var strName = '';
		for( var i = 0; i < _strContents.length; i++){
			var strMid = _strContents.substring( i, 1+i);
			console.log("strMid : " + strMid);
			if( strAlphaNumber.indexOf( strMid ) == -1){
				return strName;
			}
			strName += strMid;
		}
		return '';
	}
	function parseTimeStamp( _strTime){
		var arrDateTime = _strTime.split(" ");
		var _date = arrDateTime[0];
		var _time = arrDateTime[1];
		var arrDate = _date.split("-");
		var arrTime = _time.split(":");
		var strTime = ( arrTime[0] * 1 >= 12 ? arrTime[0] * 1 - 12 : arrTime[0]) + ':' + arrTime[1] + ( arrTime[0] * 1 >= 12 ? 'PM' : 'AM');
		var strDate = arrMonthes[ arrDate[1] * 1] + ' ' + arrDate[2] + ', ' + arrDate[0];
		return strTime + ' - ' + strDate;
	}
	function parseTweetContents( _strContents, _strTime){
		var nStartPos = _strContents.indexOf('@');
		var strName = getScreenName( _strContents.substring(nStartPos + 1));
		var strTweetContents = _strContents.substring( nStartPos + strName.length + 2);
		// strTweetContents = strTweetContents.split('@').join('');//.replaceAll( '@', '');
		var strHtml = "<div style='border-radius: 5px; border: 1px solid #ddd;margin-right:16px;'><div class='EmbeddedTweet-tweet'><div class='row col-lg-12'><a style='text-decoration: none;' target='_blank' href='https://twitter.com/"+strName+"'><img class='smallImg' src='https://twitter.com/" + strName + "/profile_image?size=original'><div class='smallName'>" + strName + "</div></a></div><div class='row col-lg-12'>" + strTweetContents + "</div><div class=Tweet-metadata dateline>"+parseTimeStamp(_strTime)+"</div></div><div>";
		return strHtml;
	}
    var tweets = $(".tweet");
    $(tweets).each(function (t, tweet) {
        var id = $(this).attr('id');
        twttr.widgets.createTweet(
            id, tweet,
            {
                conversation: 'none', // or all
                cards: 'hidden', // or visible 
                linkColor: '#cc0000', // default is blue
                theme: 'light'    // or dark
            }).then(function (result) {
            	// console.log(result);
	            if (result === undefined) {
	            	console.log("undefined");
	                // tweet.find("label[for =oldtweet]").removeAttr('hidden');
	                tweet.firstChild.hidden = false;
	                console.log(tweet);
	                var strContents = $("#"+id).find("label").eq(0).html();
	                var strTime = $("#"+id).find(".time").html();
	            	// console.log(strContents);
	            	var strHtml = parseTweetContents( strContents, strTime);
	            	console.log(strHtml);
	            	$("#"+id).html(strHtml);
            }
        });
        ;
    });
</script>

<!-- SELECT ScreenName, NAME, DataRegistrazioneUser, StatusesCount, FollowersCount, FriendsCount, Description, location, PlaceName, PlaceFullName, PlaceCountry, ProfileImageURLHTTPS, ProfileBannerURL, FavouritesCount, ListedCount, TweetTimeStampStringa FROM AnalisiTweets WHERE ScreenName = 'antonio_bordin' LIMIT 1 -->