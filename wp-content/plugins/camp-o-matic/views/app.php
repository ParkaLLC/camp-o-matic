<?php global $current_user; ?>
<!DOCTYPE html>
<html lang="en" ng-app="campomatic">
<head>
    <meta charset="utf-8" />
    <script src="<?php echo plugins_url( 'js/angular.min.js' , dirname(__FILE__) ); ?>"></script>
    <script src="<?php echo plugins_url( 'js/angular-route.min.js' , dirname(__FILE__) ); ?>"></script>
    <script src="<?php echo plugins_url( 'js/app.js' , dirname(__FILE__) ); ?>"></script>
	<link rel="stylesheet" href="<?php echo plugins_url( 'css/sessions.css' , dirname(__FILE__) ); ?>">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/questions.css">
</head>
<body>
    <div id="mainView" ng-view>
        <p>oh hai</p>
    </div>
</body>
</html>