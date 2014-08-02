<?php global $current_user; ?>
<!DOCTYPE html>
<html lang="en" ng-app="campomatic">
<head>
    <meta charset="utf-8" />
    <script src="<?php echo plugins_url( 'js/angular.min.js' , dirname(__FILE__) ); ?>"></script>
    <script src="<?php echo plugins_url( 'js/angular-route.min.js' , dirname(__FILE__) ); ?>"></script>
    <script src="<?php echo plugins_url( 'js/angular-resource.min.js' , dirname(__FILE__) ); ?>"></script>
    <script src="<?php echo plugins_url( 'js/app.js' , dirname(__FILE__) ); ?>"></script>
</head>
<body ng-controller="UserController">
    <div id="mainView" ng-view>
        <p>oh hai</p>
    </div>
</body>
</html>