<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
require(DOCUMENT_ROOT . '/class/utility.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objSql = new Sql();
$objUtils = new Utility();
//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptHead = '<link href="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>';
echo $objCommon->head($addScriptHead);
echo '</head>';
echo '<body class="">';
// echo $objCommon->topBar();
echo '<div class="page-container row-fluid container-fluid">';
// echo $objCommon->menuLeft();
echo '<section id="main-content" class=" ">';
echo '<section class="wrapper main-wrapper row" style=\'\'>';
// echo $objCommon->locationBar();

if (isset($_GET['FromScreenName']))  {
    $ScreenNameNoChi = $_GET['FromScreenName'];
    $ScreenName = "@" . $ScreenNameNoChi;

    $Sql = "SELECT ScreenName, NAME, DataRegistrazioneUser, StatusesCount, FollowersCount, FriendsCount, Description, location, PlaceName, PlaceFullName, PlaceCountry, ProfileImageURLHTTPS, ProfileBannerURL, FavouritesCount, ListedCount, TweetTimeStampStringa FROM AnalisiTweets WHERE ScreenName = '" . $ScreenNameNoChi . "' LIMIT 1";

    $SqlConteggio = "SELECT ScreenName, COUNT(*) as NumeroTweet FROM AnalisiTweets WHERE ScreenName = '" . $ScreenNameNoChi . "' GROUP BY ScreenName";
    $Tweets = $objSql->SelectArray($objSql->Query('', $Sql));
    $Conteggi = $objSql->SelectArray($objSql->Query('', $SqlConteggio));
    $Testata = $Tweets[0];
    $Conteggio = $Conteggi[0];
    $Sql = "SELECT IdTweet, Keyword, TweetTimeStampStringa, TweetText, ScreenName FROM AnalisiTweets where IdTweet in ( SELECT IdTweet FROM Relations where FromScreenName = '".$ScreenNameNoChi."' );";
    $Tweets = $objSql->SelectArray($objSql->Query('', $Sql));

}
$rowsLingue = $objSql->SelectArray($objSql->Query('tipo_lingua', 'ORDER BY idCodiceLingua ASC'));
?>
<style>
	#main-content { margin-left: 0px ! important; display: block; }
	#tweet { width: 400px !important; }
	#tweet iframe { border: none !important; box-shadow: none !important; }
	.main-wrapper { margin-top: 0px !important}
	.EmbeddedTweet-tweet { padding: 20px 20px 11.6px 20px; border-radius: 5px; border: 1px solid #ddd;}
	.sharpCls{ color: #cc0000; }
	.avatars{ margin: auto; margin-top: 20px; position: relative;}
	p{ text-align: center; }
	.fromAvatar{ width: 100px; z-index: 1;}
	.fromAvatar{ float: left; }
	table{ width: 100%; }
	body{ background-color: white; }
	.leftTd{text-align: left; width: 25%;padding: 15px}
	.rightTd{text-align: right; width: 25%;}
	.centerTd{ border: 1px solid #eee; width: 49%;}
	table tr th{ text-align: center ! important; background-color: #e8e8e8; padding-top: 20px; padding-bottom: 20px; color: black;}
	table tr td{ padding: 15px;}
	.smallImg{ width: 46px; height: 46px; float: left; border-radius: 50%;}
	.smallName{ line-height: 50px; font-weight: bold; padding-left: 70px; color: black;}
	.smallName:hover{ color: #c00; }
	.leftJustify, .rightJustify{ width: 70%; }
	.leftJustify{ float: left; }
	.rightJustify{ float: right; }
</style>

<link rel="stylesheet" href="../assets/css/Twitter.css">
<link rel="stylesheet" href="../assets/css/Twitter2.css">

<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-12">
            <div class="ProfileCanopy-header">
                <div class="ProfileCanopy-headerBg">
                    <img alt="" src="<?php echo $Testata["ProfileBannerURL"] ?>">
                </div>
                <div class="ProfileCanopy-avatar">
                    <div class="ProfileAvatar">
                        <a class="ProfileAvatar-container u-block js-tooltip profile-picture"
                           href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>"
                           title="<?php echo $ScreenNameNoChi; ?>"
                           data-resolved-url-large="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=original"
                           data-url="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=original"
                           target="_blank"
                           rel="noopener">
                            <img class="ProfileAvatar-image " src="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=original" alt="<?php echo $ScreenNameNoChi; ?>">
                        </a>
                    </div>

                </div>
            </div>

            <div class="ProfileCanopy-navBar u-boxShadow">
                <div class="">
                    <div class="Grid">
                        <div class="Grid-cell u-size1of3 u-lg-size1of4">
                            <div class="ProfileCanopy-card" role="presentation">
                                <div class="ProfileCardMini">
                                    <a class="ProfileCardMini-avatar profile-picture js-tooltip"
                                       href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>"
                                       title="<?php echo $ScreenNameNoChi; ?>"
                                       data-resolved-url-large="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=normal"
                                       data-url="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=normal"
                                       target="_blank"
                                       rel="noopener">
                                        <img class="ProfileCardMini-avatarImage" alt="<?php echo $ScreenNameNoChi; ?>" src="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/profile_image?size=normal" >
                                    </a>
                                    <div class="ProfileCardMini-details">
                                        <div class="ProfileNameTruncated account-group">
                                            <div class="u-textTruncate u-inlineBlock">
                                                <a class="fullname ProfileNameTruncated-link u-textInheritColor js-nav" href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>"  data-aria-label-part>
                                                    <?php echo $ScreenNameNoChi; ?></a></div><span class="UserBadges"></span>
                                        </div>
                                        <div class="ProfileCardMini-screenname">
                                            <a href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>" class="ProfileCardMini-screennameLink u-linkComplex js-nav u-dir" dir="ltr">
                                                <span class="username u-dir" dir="ltr">@<b class="u-linkComplex-target"><?php echo $ScreenNameNoChi; ?></b></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="Grid-cell u-size2of3 u-lg-size3of4">
                            <div class="ProfileCanopy-nav">
                                <div class="ProfileNav" role="navigation" data-user-id="13727692">
                                    <ul class="ProfileNav-list">
                                        <li class="ProfileNav-item ProfileNav-item--tweets is-active">
                                            <a class="ProfileNav-stat ProfileNav-stat--link u-borderUserColor u-textCenter js-tooltip js-nav u-textUserColor" title="<?php echo $Conteggio["NumeroTweet"] ?> Tweet" data-nav="tweets"
                                               tabindex=0
                                               >
                                                <span class="ProfileNav-label" aria-hidden="true">Tweet</span>
                                                <span class="ProfileNav-value"  data-count=<?php echo $Conteggio["NumeroTweet"] ?> data-is-compact="true"><?php echo $objUtils->CZero($Conteggio["NumeroTweet"]); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="ProfileNav-item ProfileNav-item--following">
                                            <a class="ProfileNav-stat ProfileNav-stat--link u-borderUserColor u-textCenter js-tooltip js-nav u-textUserColor" title="<?php echo $objUtils->CZero($Testata["FriendsCount"]); ?> following" data-nav="following"
                                               href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/following"
                                               >
                                                <span class="ProfileNav-label" aria-hidden="true">Following</span>
                                                <span class="u-hiddenVisually">Following</span>
                                                <span class="ProfileNav-value" data-count=<?php echo $Testata["FriendsCount"] ?> data-is-compact="false"><?php echo $objUtils->CZero($Testata["FriendsCount"]); ?></span>
                                            </a>
                                        </li>
                                        <li class="ProfileNav-item ProfileNav-item--followers">
                                            <a class="ProfileNav-stat ProfileNav-stat--link u-borderUserColor u-textCenter js-tooltip js-nav u-textUserColor" title="<?php echo $objUtils->CZero($Testata["FollowersCount"]); ?> follower" data-nav="followers"
                                               href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/followers"
                                               >
                                                <span class="ProfileNav-label" aria-hidden="true">Follower</span>
                                                <span class="u-hiddenVisually">Follower</span>
                                                <span class="ProfileNav-value" data-count=<?php echo $Testata["FollowersCount"] ?> data-is-compact="false"><?php echo $objUtils->CZero($Testata["FollowersCount"]); ?></span>
                                            </a>
                                        </li>
                                        <li class="ProfileNav-item ProfileNav-item--favorites" data-more-item=".ProfileNav-dropdownItem--favorites">
                                            <a class="ProfileNav-stat ProfileNav-stat--link u-borderUserColor u-textCenter js-tooltip js-nav u-textUserColor" title="<?php echo $objUtils->CZero($Testata["FavouritesCount"]); ?> Like" data-nav="favorites"
                                               href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/likes"
                                               >
                                                <span class="ProfileNav-label" aria-hidden="true">Like</span>
                                                <span class="u-hiddenVisually">Like</span>
                                                <span class="ProfileNav-value" data-count=<?php echo $Testata["FavouritesCount"] ?> data-is-compact="true"><?php echo $objUtils->CZero($Testata["FavouritesCount"]); ?></span>
                                            </a>
                                        </li>
                                        <li class="ProfileNav-item ProfileNav-item--lists" data-more-item=".ProfileNav-dropdownItem--lists">
                                            <a class="ProfileNav-stat ProfileNav-stat--link u-borderUserColor u-textCenter js-tooltip  js-nav u-textUserColor" title="<?php echo $objUtils->CZero($Testata["ListedCount"]); ?> lists" data-nav="all_lists" 
                                               href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>/lists">
                                                <span class="ProfileNav-label" aria-hidden="true">Lists</span>
                                                <span class="u-hiddenVisually">Lists</span>
                                                <span class="ProfileNav-value" data-is-compact="false"><?php echo $objUtils->CZero($Testata["ListedCount"]); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-2">
            <div class="ProfileHeaderCard">
                <h1 class="ProfileHeaderCard-name">
                    <a href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>" class="ProfileHeaderCard-nameLink u-textInheritColor js-nav"><?php echo isset( $Testata["Name"]) ? $Testata["Name"] : ""; ?></a>
                </h1>

                <h2 class="ProfileHeaderCard-screenname u-inlineBlock u-dir" dir="ltr">
                    <a class="ProfileHeaderCard-screennameLink u-linkComplex js-nav" href="https://twitter.com/<?php echo $ScreenNameNoChi; ?>">
                        <span class="username u-dir" dir="ltr">@<b class="u-linkComplex-target"><?php echo $ScreenNameNoChi; ?></b></span>
                    </a>
                </h2>

                <p class="ProfileHeaderCard-bio u-dir" dir="ltr"><?php echo $Testata["Description"] ?></p>

                <div class="ProfileHeaderCard-location ">
                    <span class="Icon Icon--geo Icon--medium"></span>
                    <span class="ProfileHeaderCard-locationText u-dir" dir="ltr">
                        <?php echo $Testata["location"] ?>

                    </span>
                </div>


                <div class="ProfileHeaderCard-joinDate">
                    <span class="Icon Icon--calendar Icon--medium"></span>
                    <span class="ProfileHeaderCard-joinDateText js-tooltip u-dir" dir="ltr" title="">Signed since <?php echo date_format(date_create($Testata["DataRegistrazioneUser"]), 'F Y') ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <br>
            <table class="table table-striped dt-responsive display" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Keyword<br>Tweet Date<br>Language</th>
                        <th>Tweet</th>
                    </tr>
                </thead>
                <tbody>
        <?php
			for($i = 0; $i < count($Tweets); $i++){
				$IdTweet = $Tweets[$i]['IdTweet'];
				$Keyword = $Tweets[$i]['Keyword'];
				$TweetTimeStampStringa = $Tweets[$i]['TweetTimeStampStringa'];
				$TweetText = $Tweets[$i]['TweetText'];
				$ScreenName = $Tweets[$i]['ScreenName'];
		?>
				<tr>
					<td class="leftTd">
						<?php
							echo $Keyword . "<br/>" . $TweetTimeStampStringa . "<br/>Id:" . $IdTweet;
						?>
					</td>
					<td class="centerTd">
				<?php
					echo '<div class="tweet ';
					echo '" id="'.$IdTweet.'">';
					echo '<div hidden>';
					echo '<h4>Tweet not available on line.</h4>';
					echo '<br />';
					echo '<h5>Stored Text:</h5>';
					echo '<div class="time">'.$TweetTimeStampStringa.'</div>';
					echo '<label style="background-color: white; border: solid; border-color: black; border-radius: 5px; border-width: 1px; padding: 5px;">' . $TweetText.'</label>';
					echo '</div>';
					echo '</div>';
				 ?>
				 	</td>
				</tr>
				<?php
			}
				?>
				</tbody>
            </table>
        </div>
    </div>
</div>

<?php
echo '</section>';
echo '</section>';
echo '<div class="chatapi-windows "></div>';
echo '</div>';
?>

<?php
//OTHER SCRIPTS INCLUDED ON THIS PAGE
$addScriptPage = '<script src="../assets/js/editablegrid-2.1.0-b25.js"></script>
<script src="../assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
';
echo $objCommon->jsInclude($addScriptPage);
?>
<script type="text/javascript" src="../assets/js/tweet/widgets.js"></script>
<!--<script type="text/javascript" charset="utf-8" async="" src="https://platform.twitter.com/js/tweet.0208435ebbf1bf42bb6971d1bc6165a0.js"></script>-->
<script>
	var strAlphaNumber = 'abcdefghijklmnopqrstuvwxyz_1234567890';
	var arrMonthes = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	function getScreenName( _strContents){
		console.log( _strContents);
		var nStartPos = _strContents.indexOf('@');
		var strName = '';
		for( var i = 0; i < _strContents.length; i++){
			var strMid = _strContents.substring( i, 1+i);
			console.log("strMid : " + strMid);
			if( strAlphaNumber.indexOf( strMid.toLowerCase() ) == -1){
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
		var strHtml = "<div style=' width: 500px; margin-right:16px;'><div class='EmbeddedTweet-tweet'><div class='row col-lg-12'><a style='text-decoration: none;' target='_blank' href='https://twitter.com/"+strName+"'><img class='smallImg' src='https://twitter.com/" + strName + "/profile_image?size=original'><div class='smallName'>" + strName + "</div></a></div><div class='row col-lg-12'>" + strTweetContents + "</div><div class=Tweet-metadata dateline>"+parseTimeStamp(_strTime)+"</div></div><div>";
		return strHtml;
	}
    var tweets = jQuery(".tweet");
    jQuery(tweets).each(function (t, tweet) {

        var id = jQuery(this).attr('id');


        twttr.widgets.createTweet(
                id, tweet,
                {
                    conversation: 'none', // or all
                    cards: 'hidden', // or visible 
                    linkColor: '#cc0000', // default is blue
                    theme: 'light'    // or dark
                }).then(function (result) {
            if (result === undefined) {
                //tweet.find("label[for =oldtweet]").removeAttr('hidden');
                tweet.firstChild.hidden = false;
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
    function AddToSet(id, txtTweet, Lingua)
    {
        var W = $("#W_" + id).val();

        var self = this;



        $.ajax({
            url: '../ajax/data_Retraining_Management.php?action=insert',
            type: 'POST',
            dataType: "html",
            data: {
                //tablename: self.editableGrid.name,
                CodiceLingua: Lingua,
                TipoSet: 'I',
                TipoRiga: 'T',
                Peso: parseInt(W),
                Tweet: txtTweet
            },
            success: function (response)
            {
                if (response === "ok") {
                    alert('Tweet added to Training Set.');
                } else
                    alert("error insert" + response);
            },
            error: function (XMLHttpRequest, textStatus, exception) {
                alert("Ajax failure\n");
            },
            async: true
        });
    }

    function showAddForm() {
        if ($("#addform").is(':visible'))
            $("#addform").hide();
        else
        {
            $("#addform").show();
        }
    }

</script>


<?php
echo '</body>';
echo '</html>';
?>
