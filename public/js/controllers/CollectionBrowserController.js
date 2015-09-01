app = angular.module('MongoApp');

app.controller('CollectionBrowserController', function ($scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
    var started = false;
    var currentDatabase = $stateParams.db_name;
    var currentCollection = $stateParams.col_name;
    var limit = 20;
    $scope.collections = [];
    $scope.documents = [];
    $scope.currentPage = $stateParams.page ? $stateParams.page : 1;
    $scope.paginationCount = 20;

    $scope.deleteDocument = function (doc) {
        var id = doc.data._id.$id;
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this document!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#DD6B55"
        }, function () {
            deleteDocument(currentDatabase, currentCollection,
                id);
        });
    };

    $scope.editDocument = function (doc) {
        var model = $modal.open({
            templateUrl: baseUrl + '/static/edit-document.html',
            controller: 'DocumentEditorController',
            backdrop: false,
            animation: true,
            size: 'lg',
            resolve: {
                item: function () {
                    return doc;
                },
                model: model
            }
        });
    };

    $scope.pageChanged = function () {
        if (!started) {
            return;
        }
        getDocument($stateParams.db_name, $stateParams.col_name,
            $scope.currentPage, limit);
        $state.transitionTo('collections', {
            db_name: currentDatabase,
            col_name: currentCollection,
            page: $scope.currentPage
        }, {
            notify: false
        });
    };

    /**
     * Private Methods
     */

    function getDocument(dbName, colName, page, limit) {
        api.getDocumentList(dbName, colName, page, limit)
            .success(function (documents) {
                $scope.currentPage = page;
                _setDocument(documents);
            })
            .error(function (error) {
                MongoApp.errorAlert(error);
            });
    }

    $scope.findDocumentById = function (query) {
        if (query == '') {
            refresh();
            return;
        }
        findDocumentById($stateParams.db_name, $stateParams.col_name, query);
    };

    $scope.searchDocument = function (query) {
        api.searchDocument($stateParams.db_name, $stateParams.col_name, query)
            .success(function (data) {
                $scope.currentPage = 1;
                //console.log(data);
                _setDocument(data);
            })
            .error(function (error) {
                MongoApp.errorAlert(error);
            });
    }

    function findDocumentById(dbName, colName, id) {
        api.findDocumentById(dbName, colName, id)
            .success(function (data) {
                $scope.currentPage = 1;
                if (!data.error) {
                    _setDocument(data);
                } else {
                    errorAlert(data);
                }
            })
            .error(function (error) {
                MongoApp.errorAlert(error);
            });
    }

    function _setDocument(documents) {
        started = true;
        for (var i = 0; i < documents.items.length; i++) {
            documents.items[i].json = MongoApp.helpers.decodeMson(
                documents.items[i].json);
        }
        $scope.documents = documents;
    }

    function refresh() {
        getDocument($stateParams.db_name, $stateParams.col_name,
            $stateParams.page, limit);
    }

    function deleteDocument(db, col, id) {
        api.deleteDocument(db, col, id)
            .success(function (data) {
                if (data.success) {
                    swal({
                        title: "Deleted!",
                        text: "Document has been deleted.",
                        type: "success",
                        timer: 1300
                    });
                    refresh();
                } else {
                    MongoApp.errorAlert(data);
                }
            }).error(function (error) {
                MongoApp.errorAlert(error);
            });
    }

    if ($stateParams.search) {

    } else {
        getDocument($stateParams.db_name, $stateParams.col_name,
            $stateParams.page, limit);
    }
});
