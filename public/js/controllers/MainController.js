app = angular.module('MongoApp');

app.controller('MainController', function ($rootScope, $scope, $state, $location, api, $timeout) {
    $scope.dbs = [];
    $scope.currentDb = null;
    $scope.currentCol = null;
    $scope.sideBarAnimation = true;

    $rootScope.$on("db.drop", function (e, db) {
        $scope.dbs = _.without($scope.dbs, _.findWhere($scope.dbs, {name: db}));
    });

    $rootScope.$on("db.rename", function (e, data) {
        var index = _.findIndex($scope.dbs, {name: data.from});
        if (_.isNumber(index)) {
            $scope.dbs[index].name = data.to;
        }
    });

    $rootScope.$on('$stateChangeStart',function(event, toState, toParams, fromState, fromParams){
          switch (toState.name) {
              case 'db':
                  $scope.currentDb = toParams.db_name;
                  break;
              case 'collections':
                  $scope.currentDb = toParams.db_name;
                  $scope.currentCol = toParams.col_name;
                  break;
          }
    });

    $scope.listDatabase = function () {
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
            $scope.listDatabase();
        }
    };
    init();
});

