<?php

class Common extends connection {

    function __construct() {
        
    }

    function head($addScriptHead) {
        $str = '<!DOCTYPE html>
<html class=" ">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>' . CONFIG_NAME_WEBSITE . '</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="../assets/images/favicon.png" type="image/x-icon" />    <!-- Favicon -->
    <link rel="apple-touch-icon-precomposed" href="../assets/images/apple-touch-icon-57-precomposed.png">	<!-- For iPhone -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/apple-touch-icon-114-precomposed.png">    <!-- For iPhone 4 Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/apple-touch-icon-72-precomposed.png">    <!-- For iPad -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/apple-touch-icon-144-precomposed.png">    <!-- For iPad Retina display -->




    <!-- CORE CSS FRAMEWORK - START -->
    <link href="../assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/fonts/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
    <!-- CORE CSS FRAMEWORK - END -->
    
' . $addScriptHead . '
    


    <!-- CORE CSS TEMPLATE - START -->
    <link href="../assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/responsive.css" rel="stylesheet" type="text/css"/>
    <!-- CORE CSS TEMPLATE - END -->';
        return $str;
    }

    function topBar() {
        $str = '<!-- START TOPBAR -->
<div class=\'page-topbar \'>
    <div class=\'logo-area\'>

    </div>
    <div class=\'quick-area\'>
        <div class=\'pull-left\'>
            <ul class="info-menu left-links list-inline list-unstyled">
                <li class="sidebar-toggle-wrap">
                    <a href="#" data-toggle="sidebar" class="sidebar_toggle">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>';

        $str .= $this->topBarAlert();
        $str .= '  
                
                <li class="hidden-sm hidden-xs searchform">
                    
                </li>
            </ul>
        </div>';
        $str .= $this->topBarUser();
        $str .= '
        	
    </div>

</div>
<!-- END TOPBAR -->';

        return $str;
    }

    function topBarAlert() {
        $str = '<li class="notify-toggle-wrapper">
                    <a href="#" data-toggle="dropdown" class="toggle">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-accent">3</span>
                    </a>
                    <ul class="dropdown-menu notifications animated fadeIn">
                        <li class="total">
                            <span class="small">
                                You have <strong>3</strong> new notifications.
                                <a href="javascript:;" class="pull-right">Mark all as Read</a>
                            </span>
                        </li>
                        <li class="list">

                            <ul class="dropdown-menu-list list-unstyled ps-scrollbar">
                                                                    <li class="unread available"> 
                                        <a href="javascript:;">
                                            <div class="notice-icon">
                                                <i class="fa fa-check"></i>
                                            </div>
                                            <div>
                                                <span class="name">
                                                    <strong>26 Successful searches</strong>
                                                    <span class="time small">15 mins ago</span>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="unread away"> 
                                        <a href="javascript:;">
                                            <div class="notice-icon">
                                                <i class="fa fa-envelope"></i>
                                            </div>
                                            <div>
                                                <span class="name">
                                                    <strong>45 Pending searches</strong>
                                                    <span class="time small"></span>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class=" busy"> <!-- available: success, warning, info, error -->
                                        <a href="javascript:;">
                                            <div class="notice-icon">
                                                <i class="fa fa-times"></i>
                                            </div>
                                            <div>
                                                <span class="name">
                                                    <strong>12 Failed searches</strong>
                                                    <span class="time small"></span>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                   
                                    
                                   
                                    

                            </ul>

                        </li>
<!--
                        <li class="external">
                            <a href="javascript:;">
                                <span>Read All Notifications</span>
                            </a>
                        </li>-->
                    </ul>
                </li>';

        return $str;
    }

    function topBarUser() {
        $str = '<div class=\'pull-right\'>
            <ul class="info-menu right-links list-inline list-unstyled">
                <li class="profile">
                    <a href="#" data-toggle="dropdown" class="toggle">
                        <img src="../data/profile/profile.jpg" alt="user-image" class="img-circle img-inline">
                        <span>Administrator<i class="fa fa-angle-down"></i></span>
                    </a>
                    <ul class="dropdown-menu profile animated fadeIn">
                       
                        <li>
                            <a href="Profile.php">
                                <i class="fa fa-user"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="#help">
                                <i class="fa fa-info"></i>
                                Help
                            </a>
                        </li>
                        <li class="last">
                            <a href="index.php?action=logout">
                                <i class="fa fa-lock"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="chat-toggle-wrapper">
                   
                </li>
            </ul>			
        </div>	';

        return $str;
    }

    function menuLeft() {
        $str = '<div class="page-sidebar fixedscroll">
    <div class="page-sidebar-wrapper" id="main-menu-wrapper"> 

        <div class="profile-info row">

            <div class="profile-image col-xs-4">
                <a href="Profile.php">
                    <img alt="" src="../data/profile/profile.jpg" class="img-responsive img-circle">
                </a>
            </div>

            <div class="profile-details col-xs-8">

                <h3>
                    <a href="Profile.php">Administrator</a>

    
                    <span class="profile-status online"></span>
                </h3>

                <p class="profile-title">Administrator</p>

            </div>

        </div>
        <ul class=\'wraplist\'>	

            <li class=\'menusection\'>Main</li>
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Dashboard.php") ? "class=\"open\"" : "") . ' > 
                    <a  href="Dashboard.php">
                    <i class="fa fa-dashboard"></i>
                    <span class="title">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class=\'menusection\'>Services</li>
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "New Search.php") ? "class=\"open\"" : "") . '> 
                    <a href="New Search.php">
                    <i class="fa fa-folder-open"></i>
                    <span class="title">New Search</span>
                        </a>
                    </li>
                    
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Storage Charts.php") ? "class=\"open\"" : "") . '> 
                    <a href="Storage Charts.php">
                    <i class="fa fa-puzzle-piece"></i>
                    <span class="title">Storage Charts</span>
                        </a>
                    </li>
                    
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Stop Word.php") ? "class=\"open\"" : "") . '> 
                    <a href="Stop Word.php">
                    <i class="fa fa-bar-chart"></i>
                    <span class="title">Stop Word</span>
                        </a>
                    </li>

                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Export To CSV.php") ? "class=\"open\"" : "") . '> 
                    <a href="Export To CSV.php">
                    <i class="fa fa-file-excel-o"></i>
                    <span class="title">Export To CSV</span>
                        </a>
                    </li>

                    <li class=\'menusection\'>Re-training</li>
                     <li ' . ((basename($_SERVER['PHP_SELF']) == "Supplementary Retraining.php") ? "class=\"open\"" : "") . '> 
                    <a href="Supplementary Retraining.php">
                    <i class="fa fa-thumbs-up"></i>
                    <span class="title">Supplementary Training Set</span>
                        </a>
                    </li>
                    
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Re-training Execution.php") ? "class=\"open\"" : "") . '> 
                    <a href="Re-training Execution.php">
                    <i class="fa fa-thumbs-up"></i>
                    <span class="title">Re-training Execution</span>
                        </a>
                    </li>
                    
                    <li ' . ((basename($_SERVER['PHP_SELF']) == "Re-training Monitoring.php") ? "class=\"open\"" : "") . '> 
                    <a href="Re-training Monitoring.php">
                    <i class="fa fa-thumbs-up"></i>
                    <span class="title">Re-training Monitoring</span>
                        </a>
                    </li>
        </ul>
    </div>
</div>';

        return $str;
    }

    function locationBar() {
        $str = '<div class=\'col-xs-12\'>';
        $str .= '<div class="page-title">';
        $str .= '<div class="pull-left">';
        $str .= '<h1 class="title">' . basename($_SERVER['PHP_SELF'], ".php") . '</h1>';
        $str .= '</div>';
        $str .= '<div class="pull-right hidden-xs">';
        $str .= '<ol class="breadcrumb">';
        $str .= '<li><a href="Dashboard.php"><i class="fa fa-home"></i>Dashboard</a></li>';
        if (basename($_SERVER['PHP_SELF']) <> 'Dashboard.php')
            $str .= '<li class="active"><strong>' . basename($_SERVER['PHP_SELF'], ".php") . '</strong></li>';
        $str .= ' </ol>';
        $str .= '</div></div></div><div class="clearfix"></div>';

        return $str;
    }

    function jsInclude($addScriptPage) {
        $str = '<!-- CORE JS FRAMEWORK - START -->
<script src="../assets/js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="../assets/js/jquery.easing.min.js" type="text/javascript"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script>
<script src="../assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>
<script>window.jQuery||document.write(\'<script src="../assets/js/jquery-1.11.2.min.js"><\/script>\');</script>
<!-- CORE JS FRAMEWORK - END -->
' . $addScriptPage . '
<!-- CORE TEMPLATE JS - START -->
<script src="../assets/js/scripts.js" type="text/javascript"></script>
<!-- END CORE TEMPLATE JS - END -->
';

        return $str;
    }

    function modal() {
        $str = '<div class="modal" id="section-settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog animated bounceInDown">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Section Settings</h4>
            </div>
            <div class="modal-body">
                Body goes here...
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-success" type="button">Save changes</button>
            </div>
        </div>
    </div>
</div>';

        return $str;
    }

}
