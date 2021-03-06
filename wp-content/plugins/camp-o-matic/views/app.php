<?php
$site_title = get_bloginfo( 'name' );
$home_url = get_home_url();
global $current_user;
get_currentuserinfo();
?><!DOCTYPE html>
<html lang="en" ng-app="campomatic">
<head>
	<meta charset="utf-8" />
	<script src="<?php echo plugins_url( 'js/angular.min.js' , dirname(__FILE__) ); ?>"></script>
	<script src="<?php echo plugins_url( 'js/angular-route.min.js' , dirname(__FILE__) ); ?>"></script>
	<script src="<?php echo plugins_url( 'js/angular-resource.min.js' , dirname(__FILE__) ); ?>"></script>
	<script src="<?php echo plugins_url( 'js/angular-cookies.min.js' , dirname(__FILE__) ); ?>"></script>
	<script src="<?php echo plugins_url( 'js/app.js' , dirname(__FILE__) ); ?>"></script>
	<link rel="stylesheet" href="<?php echo plugins_url( 'css/style.css' , dirname(__FILE__) ); ?>">
	<link rel="stylesheet" href="<?php echo plugins_url( 'css/bootstrap.css' , dirname(__FILE__) ); ?>">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script>
		var nonce = '<?php echo wp_create_nonce('wp_json'); ?>';
        var base_url = '/campomatic/#/';
        var home_url = '<?php echo home_url(); ?>';
        var session_version = '0';
	</script>

</head>

<body ng-controller="UserCtrl" id="campomaticBody">

<div id="header"><!--Global header-->
	<div class="container">
		<div id="header-left" class="col-md-5">
			<h3>Camp-O-Matic: <?php echo $site_title; ?></h3>
		</div>
		<div id="header-right" class="col-md-7">
			<nav class="navbar navbar-default" role="navigation">
				<ul class="nav nav-pills navbar-right">
					<li><a href="<?php echo $home_url . '/campomatic/#/sessions' ?>">Sessions</a></li>
					<li><a href="<?php echo $home_url; ?>">Return to WordCamp Maine</a></li>
				</ul>
			</nav>
		</div>
	</div>
    <div id="sub-header-bar" ng-show="showSubHeader">
        <div class="container">
            <div id="sub-header-content" class="col-md-12">
                <span>Howdy <?php echo $current_user->display_name; ?></span> <span class="log-out"><a href="<?php echo wp_logout_url( CAMPOMATIC_URL ); ?>">Log out</a></span>
            </div>
        </div>
    </div>
</div><!-- End Global header-->
<div id="mainView" class="container" ng-view ng-show="showMain">
	<p>oh hai</p>
</div>

<div id="loading" ng-show="showLoading">
    <img src="<?php echo plugins_url( 'images/loading.gif' , dirname(__FILE__) ); ?>" />
</div>

<div id="footer"><!--Global footer-->

</div>

</body>

</html>