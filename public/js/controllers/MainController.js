app = angular.module('MongoApp');

app.controller('MainController', function ($rootScope, $scope, $state, api) {
    $scope.dbs = [];
    $rootScope.currentDb = null;
    $rootScope.currentCol = null;

    $rootScope.$on("db.drop", function (e, db) {
        $scope.dbs = _.without($scope.dbs, _.findWhere($scope.dbs, {name: db}));
    });

    $rootScope.$on("db.rename", function (e, data) {
        var db = _.findWhere($scope.dbs, {name: data.from});
        db.name = data.to;
        $scope.dbs[data.from].name = data.to;
    });
});

