app = angular.module('MongoApp');

app.controller('CollectionBrowserController', function(
    $scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
    $scope.currentDb = $stateParams.db_name;
    $rootScope.currentCol = $stateParams.col_name;
    $scope.collections = [];
    $scope.documents = [];
    // $scope.currentPage = 1;
    $scope.currentPage = $stateParams.page ? $stateParams.page : 1;
    var started = false;
    $scope.paginationCount = 20;
    var limit = $scope.paginationCount;


    $scope.deleteDocument = function(doc) {
        var id = doc.data._id.$id;
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this document!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#DD6B55"
        }, function() {
            deleteDocument($scope.currentDb, $scope.currentCol,
                id);
        });
    };

    $scope.editDocument = function(doc) {
        var model = $modal.open({
            templateUrl: 'static/edit-document.html',
            controller: 'DocumentEditorController',
            backdrop: false,
            animation: true,
            size: 'lg',
            resolve: {
                item: function() {
                    return doc;
                },
                model: model
            }
        });
    };

    $scope.pageChanged = function() {
        if (!started) {
            return;
        }
        getDocument($stateParams.db_name, $stateParams.col_name,
            $scope.currentPage, limit);
        $state.transitionTo('collections', {
            db_name: $scope.currentDb,
            col_name: $scope.currentCol,
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
            .success(function(documents) {
                $scope.currentPage = page;
                started = true;

                $scope.documents = documents;
                for (var i = 0; i < documents.items.length; i++) {
                    documents.items[i].json = MongoApp.helpers.decodeMson(
                        documents.items[i].json);
                }
            })
            .error(function(error) {
                console.log(error);
                swal("Oops...", 'Unable to load data', "error");
            });
    }


    function refresh() {
        getDocument($stateParams.db_name, $stateParams.col_name,
            $stateParams.page, limit);
    }


    function deleteDocument(db, col, id) {
        api.deleteDocument(db, col, id)
            .success(function(data) {
                if (data.success) {
                    swal({
                        title: "Deleted!",
                        text: "Document has been deleted.",
                        type: "success",
                        timer: 1300
                    });
                    refresh();
                } else {
                    swal("Oops...", 'Document Not Found', "error");
                }
            }).error(function(error) {
                swal("Oops...", 'Error', "error");
            });
    }

    getDocument($stateParams.db_name, $stateParams.col_name,
        $stateParams.page, limit);
});