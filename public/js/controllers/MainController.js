app = angular.module('MongoApp');

app.controller('MainController', function ($rootScope, $scope, $state, $location, api, $timeout) {
    $scope.dbs = [];
    $rootScope.currentDb = null;
    $rootScope.currentCol = null;
    $rootScope.debut = true;

    $scope.sideBarAnimation = true;

    $rootScope.$on("db.drop", function (e, db) {
        $scope.dbs = _.without($scope.dbs, _.findWhere($scope.dbs, {name: db}));
    });

    $rootScope.$on("db.rename", function (e, data) {
        var db = _.findWhere($scope.dbs, {name: data.from});
        db.name = data.to;
        $scope.dbs[data.from].name = data.to;
    });

    $scope.listDatabase = function () {

            console.log('listDatabase');
            api.index()
                .success(function (dbs) {
                    $scope.sideBarAnimation = false;
                    $scope.dbs = dbs.databases;
                    $timeout(function() {
                        $scope.sideBarAnimation = true;
                    },1000);
                })
                .error(function (error) {
                    swal("Oops...", 'Unable to load data', "error");
                });
    }

    var init = function () {
        if($location.url() !== '/') {
            console.log('is not home page');
            $scope.listDatabase();
        }
    };
    init();
});

