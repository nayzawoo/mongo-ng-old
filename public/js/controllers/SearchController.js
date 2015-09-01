app = angular.module('MongoApp');

app.controller('SearchController', function ($scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
    $scope.search.query = '';
    $scope.editorAsForm = true;
    $scope.editor = null;
    $scope.invalid = false;
    $scope.editorOptions = {
        keyMap: "sublime",
        tabSize: 4,
        mode: "application/ld+json",
        theme: "default",
        scrollbarStyle: null,
        extraKeys: {
            "Enter": function(e) {

                if ($scope.editorAsForm) {
                    // Form
                    $scope.find();
                } else {
                    // Editor
                    $scope.editor.replaceSelection("\n" ,"end");
                }
            }
        }
    };

    $scope.codemirrorLoaded = function(_editor){
        var _doc =  _editor.getDoc();
        $scope.editor = _editor;
    };

    $scope.find = function () {
        if (getQuery() === '') {
            $scope.$parent.findDocumentById('');
            return;
        }
        if (isMongoId(getQuery())) {
            $scope.$parent.findDocumentById(getQuery());
        } else {
            try {
                var query = parseQurey();
                if (query === '{}') {
                    return;
                }
                $scope.$parent.searchDocument(query);
                return;
            } catch (err) {
                if (err.message.indexOf('parse error') != -1) {
                    $scope.$apply(function() {
                        $scope.invalid  = true;
                    });
                    $timeout(function() {
                        $scope.invalid  = false;
                    }, 1500);
                    return;
                }
            }
        }
    };

    function getQuery() {
        if (!$scope.search.query) {
            return '';
        }
        return $scope.search.query.trim();
    }

    function parseQurey() {
        return JSON.stringify(GenghisJSON.parse(getQuery()));
    }

    function isMongoId(str) {
        return !(_.isNull(str.match(/^[0-9a-fA-F]{24}$/)));
    }
});

