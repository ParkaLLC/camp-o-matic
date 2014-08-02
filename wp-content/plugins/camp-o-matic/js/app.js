'use strict';

/* App Module */

var campomatic = angular.module('campomatic', [
    'ngRoute',
    'campomaticControllers',
    'campomaticControllers'
]);

campomatic.config(
    function($routeProvider) {
        $routeProvider.
            when('/sessions', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/session_list.html',
                controller: 'SessionListCtrl'
            }).
            when('/session/:session_id', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/single_session.html',
                controller: 'SessionListCtrl'
            }).
            when( '/register', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/register.html',
                controller: 'SessionListCtrl'
            }).
            otherwise({
                redirectTo: '/sessions'
            });
    }
);

var campomaticControllers = angular.module('campomaticControllers', []);

campomaticControllers.controller('SessionListCtrl', ['$scope',
    function($scope) {
    }
]);

campomaticControllers.controller('SingleSessionCtrl', ['$scope',
    function($scope) {
    }
]);

campomaticControllers.controller('RegisterCtrl', ['$scope',
    function($scope) {
    }
]);


var compomaticServices = angular.module('compomaticServices', ['ngResource']);