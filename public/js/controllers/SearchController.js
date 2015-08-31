app = angular.module('MongoApp');

app.controller('SearchController', function ($scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
    $scope.query = null;
    $scope.editorAsForm = false;
    $scope.editor = null;
    $scope.editorOptions = {
        keyMap: "sublime",
        tabSize: 4,
        mode: "application/ld+json",
        theme: "default",
        scrollbarStyle: null,
        extraKeys: {
            "Enter": function(e) {
                if ($scope.editorAsForm) {
                    // Code Editor
                    $scope.editor.replaceSelection("\n" ,"end");
                } else {
                    // Or Form
                    $scope.$parent.find($scope.query);
                }
            }
        }
    };

    $scope.codemirrorLoaded = function(_editor){
        // Editor part
        var _doc =  _editor.getDoc();
        $scope.editor = _editor;
    };

    $scope.find = function () {
        $scope.$parent.find($scope.query);
    };
});

