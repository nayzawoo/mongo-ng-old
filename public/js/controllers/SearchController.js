app = angular.module('MongoApp');

app.controller('SearchController', function(
    $scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
    $scope.editorOptions = {
        keyMap: "sublime",
        tabSize: 4,
       mode: "application/ld+json",
        theme: "default",
        scrollbarStyle: null
    };
});

