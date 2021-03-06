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
        $scope.showSubHeader = false;
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
                    $scope.showSubHeader = true;
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
campomaticControllers.controller('SingleSessionCtrl',
    ['$scope', 'SessionService', '$routeParams', '$resource', '$interval', '$http', 'QuestionService', '$cacheFactory',
    function($scope, SessionService, $routeParams, $resource, $interval, $http, QuestionService, $cacheFactory) {

        $scope.Auth();
        $scope.questionOrder = 'meta.total_votes';
        $scope.Questions = [];
        $scope.refreshQuestions = function() {
            QuestionService.QuestionList.query(
                { session_id : $routeParams.session_id, 'foobar': new Date().getTime() },
                function (s) {
                    $scope.Questions = s;
                }
            );
        };
        $scope.SessionsSingle = SessionService.SingleSession.get({ session_id : $routeParams.session_id },
            function() {
                // we will initiate the heartbeat once we have information about the session
                session_version = $scope.SessionsSingle.meta.version;
                $scope.refreshQuestions();
                var heartbeatURL = "/wp-content/uploads/campomatic-hb/" + $scope.SessionsSingle.ID + ".txt";
                var heartbeat = $interval(
                    function() {
                        $http({method:"GET", url : heartbeatURL, params: { 'foobar': new Date().getTime() } }).success(
                            function(data) {
                                if( data != session_version ) {
                                    $scope.refreshQuestions();
                                    session_version = data;
                                    console.log( 'heartbeat updated' );
                                } else {
                                    console.log( 'no update needed' );
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
                    $scope.showClose = true;
                    $scope.closeMessage = 'Done';
                    $scope.showForm = true;
                    $scope.question = '';
                    if(s.error) {
                        $scope.showSuccess = false;
                        alert(s.message);
                    } else {
                        $scope.successMessage = s.message;
                        session_version = s.session_version;
                        $scope.refreshQuestions();
                    }
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

campomaticControllers.controller('QuestionCtrl',[ '$scope', 'QuestionService',
    function( $scope, QuestionService ) {

        $scope.removeQuestion = function() {
            QuestionService.SingleQuestion.remove({ question_id  : $scope.question.ID },
                function(s) {
                    if(s.error) {
                        alert(s.message);
                    } else {
                        session_version = s.session_version;
                        $scope.Questions = $scope.refreshQuestions();
                    }
                }
            );
        };

        $scope.showRemoveButton = function() {
            if( $scope.user.is_admin ) {
                return true;
            }

            if( $scope.question.author.ID == $scope.user.ID ) {
                return true;
            }
            return false;
        };

        $scope.iconClass = function() {
            var iconClass = "fa fa-heart";
            if( $scope.question.meta.voted ) {
                iconClass += " voted";
            }
            return iconClass;
        };

        $scope.upvote = function() {

            var vote_direction = "up";
            if( $scope.question.meta.voted ) {
                vote_direction = "down";
            }

            var data = {
                "ID" : $scope.question.ID,
                "vote_direction" : vote_direction,
                "user" : $scope.user.ID,
            };
            QuestionService.Upvote.save( data, function(s) {
                session_version = s.session_version;
                $scope.Questions = $scope.refreshQuestions();
            } );
        };

        $scope.iconTitle = function() {
            var title = "I need to know the answer to this question!!";
            if( $scope.question.meta.voted ) {
                title = "I changed my mind. This is a dumb question.";
            }
            return title;
        };
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
            SessionList : $resource('/wp-json/posts?type=wcb_session&_wp_json_nonce=' + nonce  +
            '&filter[posts_per_page]=-1&filter[orderby]=meta_value_num&filter[meta_key]=_wcpt_session_time' +
            '&filter[meta_query][0][key]=_wcpt_session_type&filter[meta_query][0][value]=session'),
            SingleSession : $resource('/wp-json/posts/:session_id',{_wp_json_nonce : nonce})
        };
    }
]);

campomaticServices.factory('QuestionService', ['$resource',
    function($resource){
        return {
            QuestionList : $resource('/wp-json/posts?type=happiness&_wp_json_nonce=' + nonce  +
            '&filter[posts_per_page]=-1&filter[meta_key]=_campomatic_session_id&filter[meta_value]=:session_id'),
            AddQuestion : $resource('/wp-json/campomatic/ask',{_wp_json_nonce : nonce}),
            SingleQuestion : $resource('/wp-json/campomatic/question/:question_id',{_wp_json_nonce : nonce}),
            Upvote : $resource('/wp-json/campomatic/upvote/',{_wp_json_nonce : nonce}),
        };
    }
]);