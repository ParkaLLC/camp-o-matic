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


/*
Single Session Controller
 */
campomaticControllers.controller('SingleSessionCtrl', ['$scope', 'SessionService', '$routeParams',
    function($scope, SessionService, $routeParams) {
        $scope.SessionsSingle = SessionService.SingleSession.query({ session_id : $routeParams.session_id });
        $scope.SessionQuestions = SessionService.SessionQuestion.query({ session_id : $routeParams.session_id });
        $scope.modalShown = false;
        $scope.toggleModal = function() {
            $scope.modalShown = !$scope.modalShown;
        };
        console.log( $scope.SessionsSingle );
    }
]);


/*
 Register Controller
 */
campomaticControllers.controller('RegisterCtrl', ['$scope', 'UserService',
    function($scope, UserService) {
        $scope.showForm = true;
        $scope.showSuccess = false;
        $scope.showError = false;
        $scope.errorMessage = '';
        $scope.successMesage = '';
        $scope.submit = function() {
            $scope.showForm = false;
            $scope.showSuccess = true;
            $scope.successMesage = 'Creating your registration using love and flowers...';
            var data = {
                'name': $scope.name,
                'email': $scope.email,
                'twitter': $scope.twitter
            };
           UserService.Register.save(data,
               function(s) {
                   if(s.error) {
                       $scope.showForm = true;
                       $scope.showSuccess = false;
                       $scope.showError = true;
                       $scope.errorMessage = s.message;
                   } else {
                       $scope.successMesage = s.message;
                   }
               }
           );
        }
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
            Register : $resource('/wp-json/campomatic/register')
        };
    }
]);

campomaticServices.factory('SessionService', ['$resource',
    function($resource){
        return {
            SessionList : $resource('/wp-json/posts?type=wcb_session&_wp_json_nonce=' + nonce  + '&filter[posts_per_page]=-1&filter[orderby]=meta_value_num&filter[meta_key]=_wcpt_session_time&filter[meta_query][0][key]=_wcpt_session_type&filter[meta_query][0][value]=session'),
            SingleSession : $resource('/wp-json/posts/:session_id', {}, {
                query: {method:'GET', params:{context : 'view'}, isArray:false}
            }),
            SessionQuestion : $resource('/wp-json/posts/:session_id/comments', {}, {
                query: {method:'GET', params:{context : 'view'}, isArray:true }
            })
        };
    }
]);


//Modal stuff!
campomatic.directive('modalDialog', function() {
    return {
        restrict: 'E',
        scope: {
            show: '='
        },
        replace: true, // Replace with the template below
        transclude: true, // we want to insert custom content inside the directive
        link: function(scope, element, attrs) {
            scope.dialogStyle = {};
            if (attrs.width)
                scope.dialogStyle.width = attrs.width;
            if (attrs.height)
                scope.dialogStyle.height = attrs.height;
            scope.hideModal = function() {
                scope.show = false;
            };
        },
        templateUrl: '/wp-content/plugins/camp-o-matic/views/ask.html'
    };
});