'use strict';


var campomatic = angular.module('campomatic', [
    'ngRoute',
    'campomaticControllers',
    'campomaticServices'
]);

campomatic.config(
    function($routeProvider, $sceProvider) {
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
        $sceProvider.enabled(false);
    }
);

var campomaticControllers = angular.module('campomaticControllers', []);

campomaticControllers.controller('UserController', ['$scope',
    function($scope) {
        $scope.userLoaded = false;
    }
]);

campomaticControllers.controller('SessionListCtrl', ['$scope', 'SessionService',
    function($scope, SessionService) {
        $scope.Sessions = SessionService.query();
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

var campomaticServices = angular.module('campomaticServices', ['ngResource']);

campomaticServices.factory('UserService', ['$resource',
    function($resource){
        return $resource('/wp-json/users/me', {}, {
            query: {method:'GET', params:{context : 'view'}, isArray:true}
        });
    }
]);

campomaticServices.factory('SessionService', ['$resource',
    function($resource){
        return $resource('/wp-json/posts?type[]=com_session', {}, {
            query: {method:'GET', params:{context : 'view'}, isArray:true}
        });
    }
]);