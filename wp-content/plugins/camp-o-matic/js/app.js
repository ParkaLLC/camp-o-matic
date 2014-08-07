'use strict';

var base_url = '/campomatic/#/';

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
                controller: 'SingleSessionCtrl'
            }).
            when( '/register', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/register.html',
                controller: 'RegisterCtrl'
            }).
            when( '/login', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/login.html',
                controller: 'LoginCtrl'
            }).
            when('/ask', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/ask.html',
                controller: 'AskCtrl'
            }).
            when('/connect/:connection_id', {
                templateUrl: '/wp-content/plugins/camp-o-matic/views/connect.html',
                controller: 'ConnectionCtrl'
            }).
            otherwise({
                redirectTo: '/sessions'
            });
        $sceProvider.enabled(false);
    }
);

var campomaticControllers = angular.module('campomaticControllers', []);

campomaticControllers.controller('UserCtrl', ['$scope',
    function($scope) {
        $scope.base_url = base_url;
    }
]);

campomaticControllers.controller('SessionListCtrl', ['$scope', 'SessionService',
    function($scope, SessionService) {
        $scope.Sessions = SessionService.SessionList.query();
    }
]);

campomaticControllers.controller('SingleSessionCtrl', ['$scope', 'SessionService', '$routeParams',
    function($scope, SessionService, $routeParams) {
        $scope.SessionsSingle = SessionService.SingleSession.query({ session_id : $routeParams.session_id });
        $scope.SessionQuestions = SessionService.SessionQuestion.query({ session_id : $routeParams.session_id });
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

campomaticControllers.controller('ConnectionCtrl', ['$scope', '$routeParams',
    function($scope, $routeParams) {
        $scope.id = $routeParams.connection_id;
    }
]);

var campomaticServices = angular.module('campomaticServices', ['ngResource']);

campomaticServices.factory('UserService', ['$resource', '$cookies',
    function($resource, $cookies){

        return {
            UserList : 'heyo'
        };
    }
]);

campomaticServices.factory('SessionService', ['$resource',
    function($resource){
        return {
            SessionList : $resource('/wp-json/posts?type[]=com_session', {}, {
                query: {method:'GET', params:{context : 'view'}, isArray:true}
            }),
            SingleSession : $resource('/wp-json/posts/:session_id', {}, {
                query: {method:'GET', params:{context : 'view'}, isArray:false}
            }),
            SessionQuestion : $resource('/wp-json/posts/:session_id/comments', {}, {
                query: {method:'GET', params:{context : 'view'}, isArray:true }
            })
        };
    }
]);