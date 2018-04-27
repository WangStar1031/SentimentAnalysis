<?php
require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/common.class.php');
require(DOCUMENT_ROOT . '/class/utility.class.php');
require(DOCUMENT_ROOT . '/class/PHPMailer-master/PHPMailerAutoload.php');
$objConnection = new connection();
//$objConnection->sec_session_start();
$objCommon = new Common();
$objMail = new PHPMailer;
$objUtility = new Utility;

if (isset($_REQUEST['action']) && $_REQUEST['action'] <> '') {

    switch (strtoupper($_REQUEST['action'])) {
        case "REGISTER_CONFIRM":

            // <editor-fold defaultstate="collapsed" desc="****************** REGISTRAZIONE CONFERMA ">
            $objControl = $objConnection->Register($objMail, $objUtility, $_POST['user_email'], $_POST['user_password']);

            echo $objControl[0] . ' ' . $objControl[1];
            //if ($objControl[0] == 'ko')
            //  $objUser->LoginTrace('', 'REGISTER_CONFIRM ' . $objControl[2] . ' ' . $_POST['user_email'] . '');
            // </editor-fold>
            break;

        case "LOGIN_CONFIRM":
            // <editor-fold defaultstate="collapsed" desc="****************** LOGIN CONFERMA ">
            $objControl = $objConnection->Login(trim($_POST['user_email']), trim($_POST['user_password']));
            //if ($objControl[0] == 'ko')
            // </editor-fold>
            break;

        case "SENDPASSWORD_CONFIRM"://form recupera password
            // <editor-fold defaultstate="collapsed" desc="****************** RINVIO PASSWORD CONFERMA ">
            $objControl = $objConnection->SendPassword($objMail, $objUtility, $_POST['user_email']);
            // if ($objControl[0] == 'ko')
            //   $objUser->LoginTrace('', 'SENDPASSWORD_CONFIRM ' . $objControl[2] . ' ' . $_POST['user_email'] . '');
            // </editor-fold>
            break;

        default:
            break;
    }
}

echo $objCommon->head('');
echo '<body class="login_page">';
?>
<style>
    body.login_page {
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center top;

    }
</style>

<div class="container-fluid">
    <div class="login-wrapper row">
        <div id="login" class="login loginpage col-lg-offset-4 col-md-offset-3 col-sm-offset-3 col-xs-offset-0 col-xs-12 col-sm-6 col-lg-4">


<?php
switch (strtoupper(@$_REQUEST['action'])) {
    case "REGISTER":
        ?>

                    <h1><a href="#" title="Login Page" tabindex="-1">Iscriviti</a></h1>
                    <form name="loginform" id="loginform" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
                        <p>
                            <label for="user_login">ID<br />
                                <input placeholder="Your emai:" type="text" name="user_email" id="user_email" class="form-control" required  /></label>
                        </p>
                        <p>
                            <label for="user_pass">Password<br />
                                <input placeholder="Your password:" type="password" name="user_password" id="user_password" class="form-control" required /></label>
                        </p>

                        <p>
                            <label for="user_pass">Confirm password<br />
                                <input placeholder="Your password:" type="password" name="user_password_confirm" id="user_password_confirm" class="form-control" required /></label>
                        </p>

                        <p class="submit">

                            <button  type="submit" name="action" id="action" class="btn btn-accent btn-block" value="REGISTER_CONFIRM" >Register</button>
                        </p>
                        <p>


                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?action=SENDPASSWORD" >Retrieves access data</a><br>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" >Login</a><br>


                        </p>
                    </form>

        <?php
        break;



    case "SENDPASSWORD"://form recupera password
        ?>
                    <h1><a href="#" title="Login Page" tabindex="-1"></a></h1>
                    <form name="loginform" id="loginform" action="<?php echo basename($_SERVER['PHP_SELF']); ?>?action=LOGIN_CONFIRM" method="post">

                        <p>
                            <label for="user_login">ID<br />
                                <input placeholder="Your emai:" type="text" name="user_email" id="user_email" class="form-control" required  /></label>
                        </p>

                        <p class="submit">

                            <button  type="submit" name="action" id="action" class="btn btn-accent btn-block" value="SENDPASSWORD_CONFIRM" >Retrieves access data</button>
                        </p>
                        <p>



                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?action=REGISTER" >Register</a><br>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" >Login</a>

                        </p>
                    </form>
        <?php
        break;

    default:
        //<form name="loginform" id="loginform" action="<?php echo basename($_SERVER['PHP_SELF']);?action=LOGIN_CONFIRM" method="post">
        ?>
                    <h1><a href="#" title="Login Page" tabindex="-1">Login</a></h1>
                    <form name="loginform" id="loginform" action="Dashboard.php" method="post">
                        <p>
                            <label for="user_login">ID<br />
                                <input placeholder="Your emai:" type="text" name="user_email" id="user_email" class="form-control" required  /></label>
                        </p>
                        <p>
                            <label for="user_pass">Password<br />
                                <input placeholder="Your password:" type="password" name="user_password" id="user_password" class="form-control" required /></label>
                        </p>
                        <p class="submit">

                            <button  type="submit" name="action" id="action" class="btn btn-accent btn-block" value="login_confirm" >Login</button>
                        </p>
                        <p>


                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?action=SENDPASSWORD" >Retrieve Access Data</a><br>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?action=REGISTER" >Register!</a>

                        </p>
                    </form>
        <?php
        break;
}
?>
            <div><?php echo (isset($objControl) ? $objControl[2] : ""); ?></div>

        </div>
    </div>
</div>


<?php
echo $objCommon->jsInclude('');
echo '</body>';
echo '</html>';
?>

