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


campomaticControllers.controller('UserCtrl', ['$scope', 'UserService',
    function($scope, UserService) {
        $scope.base_url = base_url;
        $scope.modalShown = false;
        $scope.toggleModal = function() {
            $scope.modalShown = !$scope.modalShown;
            var d = document.getElementById("campomaticBody");
            if( $scope.modalShown ) {
                d.className = "noScroll";
            } else {
                d.className = "";
            }
        };
        $scope.Auth = function() {
            $scope.showLoading = true;
            $scope.showMain = false;
            UserService.Auth.get({}, function(s){
                if(s.error) {
                    window.location = base_url + 'login';
                    return false;
                } else {
                    $scope.showLoading = false;
                    $scope.showMain = true;
                    $scope.user = s.message;
                    console.log( $scope.user );
                    return true;
                }
            });
        };
        $scope.ReverseAuth = function() {
            $scope.showLoading = true;
            $scope.showMain = false;
            UserService.Auth.get({}, function(s){
                if(s.error) {
                    $scope.showLoading = false;
                    $scope.showMain = true;
                    return true;
                } else {
                    window.location = base_url;
                    return false;
                }
            });
        };
    }
]);

campomaticControllers.controller('SessionListCtrl', ['$scope', 'SessionService',
    function($scope, SessionService) {
        $scope.Auth();
        $scope.Sessions = SessionService.SessionList.query();
    }
]);

/*
Single Session Controller
 */
campomaticControllers.controller('SingleSessionCtrl', ['$scope', 'SessionService', '$routeParams', '$resource', '$interval', '$http', 'QuestionService',
    function($scope, SessionService, $routeParams, $resource, $interval, $http, QuestionService) {
        $scope.Auth();
        $scope.SessionsSingle = SessionService.SingleSession.get({ session_id : $routeParams.session_id },
            function() {
                // we will initiate the heartbeat once we have information about the session
                $scope.version = $scope.SessionsSingle.meta.version;
                $scope.Questions = QuestionService.QuestionList.query();
                var heartbeatURL = "/wp-content/uploads/campomatic-hb/" + $scope.SessionsSingle.ID + ".txt";

                var heartbeat = $interval(
                    function() {
                        $http.get(heartbeatURL).success(
                            function(data) {
                                if( data != $scope.version ) {
                                    console.log('update needed!!');
                                } else {
                                    console.log('everything is static');
                                }
                            }
                        );
                    },
                    5000
                );
            }
        );

    }
]);


/*
 Register Controller
 */
campomaticControllers.controller('RegisterCtrl', ['$scope', 'UserService',
    function($scope, UserService) {
        $scope.ReverseAuth();
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
                       window.location = home_url + '/campomatic/';
                   }
               }
           );
        }
    }
]);

campomaticControllers.controller('LoginCtrl', ['$scope', 'UserService',
    function($scope, UserService) {
        $scope.ReverseAuth();
        $scope.showForm = true;
        $scope.showSuccess = false;
        $scope.showError = false;
        $scope.errorMessage = '';
        $scope.successMesage = '';
        $scope.submit = function() {
            $scope.showForm = false;
            $scope.showSuccess = true;
            $scope.successMesage = 'Checking our records. Playing a jaunty tune.';
            var data = {
                'email': $scope.email
            };
            UserService.GetLogin.save(data,
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

/*
Ask Controller
 */
campomaticControllers.controller('AskCtrl', ['$scope', 'QuestionService',
    function($scope, QuestionService ) {
        $scope.showForm = true;
        $scope.showSuccess = false;
        $scope.showError = false;
        $scope.errorMessage = '';
        $scope.successMessage = '';
        $scope.closeMessage = 'Nevermind';
        $scope.showClose = true;
        $scope.submit = function() {
            $scope.showForm = false;
            $scope.showSuccess = true;
            $scope.successMessage = 'Asking...';
            $scope.showClose = false;
            var data = {
                'question': $scope.question,
                'session_id' : $scope.SessionsSingle.ID
            };
            QuestionService.AddQuestion.save(data,
                function(s) {
                    $scope.successMessage = "Boom! Question asked.";
                    $scope.showClose = true;
                    $scope.closeMessage = 'Done';
                }
            );
        }
    }
]);

campomaticControllers.controller('ConnectionCtrl', ['$scope', '$routeParams', 'UserService',
    function($scope, $routeParams, UserService) {

            $scope.connectionLoading = true;
            $scope.showError = false;
            $scope.showSuccess = false;
            $scope.errorMessage = '';
            $scope.successMesage = '';
            UserService.Login.save( { key : $routeParams.connection_id },
                function(s) {
                    if(s.error ) {
                        $scope.connectionLoading = false;
                        $scope.showError = true;
                        $scope.showSuccess = false;
                        $scope.errorMessage = s.message;
                    } else {
                        $scope.connectionLoading = false;
                        $scope.showError = false;
                        $scope.showSuccess = true;
                        $scope.successMesage = s.message;
                        window.location = home_url + '/campomatic/';
                    }
                }
            );
    }
]);

var campomaticServices = angular.module('campomaticServices', ['ngResource']);


campomaticServices.factory('UserService', ['$resource', '$cookies',
    function($resource, $cookies){

        return {
            Register : $resource('/wp-json/campomatic/register'),
            Login : $resource('/wp-json/campomatic/login'),
            GetLogin : $resource('/wp-json/campomatic/get_login'),
            Auth : $resource('/wp-json/campomatic/auth',{_wp_json_nonce : nonce})
        };
    }
]);

campomaticServices.factory('SessionService', ['$resource',
    function($resource){
        return {
            SessionList : $resource('/wp-json/posts?type=wcb_session&_wp_json_nonce=' + nonce  + '&filter[posts_per_page]=-1&filter[orderby]=meta_value_num&filter[meta_key]=_wcpt_session_time&filter[meta_query][0][key]=_wcpt_session_type&filter[meta_query][0][value]=session'),
            SingleSession : $resource('/wp-json/posts/:session_id',{_wp_json_nonce : nonce})
        };
    }
]);

campomaticServices.factory('QuestionService', ['$resource',
    function($resource){
        return {
            QuestionList : $resource('/wp-json/posts?type=happiness&_wp_json_nonce=' + nonce  +
            '&filter[posts_per_page]=-1'),
            AddQuestion : $resource('/wp-json/campomatic/ask',{_wp_json_nonce : nonce})
        };
    }
]);
