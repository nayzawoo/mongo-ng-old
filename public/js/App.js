/**
 * MongoApp Module
 *
 * Description
 */

var app = angular.module('MongoApp', [
    'ui.router',
    'ncy-angular-breadcrumb',
    'ui.bootstrap',
    'ui.bootstrap.tpls',
    'ngSanitize',
    'ui.codemirror',
    'angular-loading-bar'
]);

app.config(function(
    $stateProvider,
    $urlRouterProvider,
    $httpProvider,
    $breadcrumbProvider,
    $locationProvider,
    cfpLoadingBarProvider
) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    $locationProvider.html5Mode(false);
    cfpLoadingBarProvider.includeSpinner = false;
    // For any unmatched url, redirect to /state1
    $urlRouterProvider.otherwise("/");
    //
    // Now set up the states
    $stateProvider
        .state('home', {
            url: "/",
            // controller: "HomeController",
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

    //////////
    $breadcrumbProvider.setOptions({
        templateUrl: 'static/breadcrumb.html',
        // prefixStateName: 'Home',
    });
});

app.filter("decodeObj", function() {
    return function  (value) {
        value =  value.replace(/"`{{(.+)}}`"/g, '$1');
        return value.replace(/`,,`/g, '"');
    };
});

app.filter('readableSize', function() {
    return function readableSize(fileSizeInBytes) {
        // fileSizeInBytes = fileSizeInBytes * 0.125;
        var i = -1;
        var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
        do {
            fileSizeInBytes = fileSizeInBytes / 1024;
            i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
    };
});