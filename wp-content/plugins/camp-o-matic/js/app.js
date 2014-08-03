'use strict';


var campomatic = angular.module('campomatic', [
    'ngRoute',
    'ngResource',
    'ngCookies',
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
            when( '/login', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/login.html',
                controller: 'LoginCtrl'
            }).
            when('/ask', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/ask.html',
                controller: 'AskCtrl'
            }).
            otherwise({
                redirectTo: '/login'
            });
        $sceProvider.enabled(false);
    }
);

var campomaticControllers = angular.module('campomaticControllers', []);

campomaticControllers.controller('SessionListCtrl', ['$scope', 'SessionService',
    function($scope, SessionService) {
        $scope.Sessions = SessionService.query();
    }
]);

campomaticControllers.controller('SingleSessionCtrl', ['$scope', 'SessionSingle',
    function($scope, SessionSingle) {
        $scope.SessionsSingle = SessionSingle.getPost( post_id );
    }
]);

campomaticControllers.controller('RegisterCtrl', ['$scope',
    function($scope) {
    }
]);

campomaticControllers.controller('LoginCtrl', ['$scope',
    function($scope) {
    }
]);

campomaticControllers.controller('AskCtrl', ['$scope',
    function($scope) {
    }
]);

var campomaticServices = angular.module('campomaticServices', ['ngResource']);

campomaticServices.factory('UserService', ['$resource', '$cookies',
    function($resource, $cookies){
        return {
            isLogged : function() {

            },
            Auth : function(username, pw) {

            }
        };
    }
]);

campomaticServices.factory('SessionService', ['$resource',
    function($resource){
        return $resource('/wp-json/posts?type[]=com_session', {}, {
            query: {method:'GET', params:{context : 'view'}, isArray:true}
        });
    }
]);

//Get the post content yo
campomaticServices.factory('SessionSingle', ['$resource',
    function($resource){
        return {
            getPost : function( post_id ) {
                $resource('/wp-json/posts/' + post_id , {}, {
                    query: {method:'GET', params:{context : 'view'}, isArray:true}
                });
            }
        }
    }
]);