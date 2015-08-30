/**
 * MongoApp Module
 *
 * Description
 */

var MongoApp = MongoApp || {};

var app = angular.module('MongoApp', [
    'ncy-angular-breadcrumb',
    'ui.bootstrap',
    'ui.bootstrap.tpls',
    'ngSanitize',
    'ui.codemirror',
    'ngAnimate',
    'ui.router',
    'angular-loading-bar',
    'anim-in-out',
    'LocalStorageModule'
]);

app.config(function ($stateProvider,
                     $urlRouterProvider,
                     $httpProvider,
                     $breadcrumbProvider,
                     $locationProvider,
                     cfpLoadingBarProvider,
                     localStorageServiceProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    $locationProvider.html5Mode(false);
    cfpLoadingBarProvider.includeSpinner = false;
    localStorageServiceProvider
        .setPrefix('mongo_admin')
        .setNotify(true, true);

    // Route List
    $urlRouterProvider.otherwise("/");
    $stateProvider
        .state('home', {
            url: "/",
            controller: "HomeController",
            templateUrl: "static/home.html",
            ncyBreadcrumb: {
                label: '<i class="fa fa-cubes fa-fw"></i> Databases'
            }
        })
        .state('db', {
            url: "/db/:db_name",
            templateUrl: "static/db.html",
            controller: "DatabaseBrowserController",
            ncyBreadcrumb: {
                label: '<i class="fa fa-database fa-fw"></i> {{currentDb}}',
                parent: 'home'
            }
        })
        .state('collections', {
            url: "/db/:db_name/:col_name?page",
            templateUrl: "static/collections.html",
            controller: "CollectionBrowserController",
            ncyBreadcrumb: {
                label: '<i class="fa fa-table fa-fw"></i> {{currentCol}}',
                parent: 'db'
            }
        });

    $breadcrumbProvider.setOptions({
        templateUrl: 'static/breadcrumb.html'
    });
});

MongoApp.delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

$('body').bind('DOMSubtreeModified', function (e) {
    if (e.target.innerHTML.length > 0) {
        MongoApp.delay(function () {
            console.log('material');
            $.material.init();
        }, 1000);
    }
});
