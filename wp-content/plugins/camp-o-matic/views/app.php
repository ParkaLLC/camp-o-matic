<?php
$site_title = get_bloginfo( 'name' );
$home_url = get_home_url();
?>
<!DOCTYPE html>
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
	</script>

</head>

<body ng-controller="UserCtrl">

<div id="header"><!--Global header-->
	<div class="container">
		<div id="header-left" class="col-md-8">
			<h3>Camp-O-Matic: <?php echo $site_title; ?></h3>
		</div>
		<div id="header-right" class="col-md-4">
			<a class="button-white" href="<? echo $home_url; ?>">Return to <?php echo $site_title; ?></a>
		</div>
	</div>
</div><!-- End Global header-->


<div id="mainView" ng-view>
	<p>oh hai</p>
</div>


<div id="footer"><!--Global footer-->

</div>

</body>

</html>