var app = angular.module('MongoApp');

app.controller('DatabaseBrowserController', function($rootScope, $scope,
                                                     $stateParams, api) {
    var dbName = $stateParams.db_name;
    $scope.currentDb = dbName;
    $scope.currentCol = null;
    $scope.collections = [];

    $scope.dropCollection = function(db, col) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this collection!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#DD6B55"
        }, function() {
            dropCollection(db, col);
        });
    };

    $scope.renameCollection = function(db, col) {
        swal({
            title: "Rename Collection",
            text: "Collection Name",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "Collection Name",
            inputValue: col
        }, function(inputValue) {
            if (inputValue === false || inputValue === col)
                return false;
            if (inputValue === "") {
                swal.showInputError(
                    "You need to write something!");
                return false;
            }
            renameCollection(db, col, inputValue);
        });
    };

    function renameCollection(db, col_from, col_to) {
        api.renameCollection(db, col_from, col_to)
            .success(function(data) {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: "Collection has been renamed.",
                        type: "success",
                        timer: 1300
                    });
                    getCollection($stateParams.db_name);
                    //event rename controller
                    return;
                }
                swal("Oops...", 'Collection not found!', "error");
            })
            .error(function(error) {
                swal("Oops...", 'Error', "error");
            });
    }

    function dropCollection(db, col) {
        api.dropCollection(db, col)
            .success(function(data) {
                if (data.success) {
                    swal({
                        title: "Deleted!",
                        text: "Collection has been deleted.",
                        type: "success",
                        timer: 1300
                    });
                    getCollection($stateParams.db_name);
                    $rootScope.refreshSidebar();
                    return;
                }
                swal("Oops...", 'Collection not found!', "error");
            })
            .error(function(error) {
                swal("Oops...", 'Error', "error");
            });
    }

    function getCollection(dbName) {
        api.getCollectionList(dbName)
            .success(function(data) {
                $scope.collections = data.collections;
                $scope.db_stats = data.db_stats;
            })
            .error(function(error) {
                console.log(error);
                swal("Oops...", 'Unable to load data', "error");
            });
    }
    getCollection($stateParams.db_name);
});