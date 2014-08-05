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
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
    <div id="mainView" ng-view>
        <p>oh hai</p>
    </div>
</body>
</html>