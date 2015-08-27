app = angular.module('MongoApp');

app.controller('MainController', function($rootScope, $scope, $state, api, $filter, $parse) {
    $rootScope.dbs = [];
    $rootScope.currentDb = null;
    $rootScope.currentCol = null;
    var _lastGoodResult = '';

    $rootScope.toPrettyJSON = function(objRaw, tabWidth) {
        return JSON.stringify(objRaw);
        // var obj = {};
        // try {
        //     obj = $parse(objStr)({});
        // } catch (e) {
        //     console.error('toPrettyJSON error');
        //     return _lastGoodResult;
        // }
        // var result = JSON.stringify(obj, null, Number(tabWidth));
        // _lastGoodResult = result;
        // return result;
    };

    $scope.browseDB = function(db_name) {
        // alert('df');
        $state.go("db", {
            db_name: db_name
        });
    };

    /**
     * Private Methods
     */
    function init() {
        index();
    }

    function index() {
        console.log('dddddd');
        api.index()
            .success(function(dbs) {
                $rootScope.dbs = dbs.databases;
                setTimeout(function() {
                    $('#side-menu').metisMenu();
                }, 50);
            })
            .error(function(error) {
                console.log(error);
                swal("Oops...", 'Unable to load data', "error");
            });
    }

    init();
});

app.controller('DatabaseBrowserController', function($rootScope, $scope, $stateParams, api) {
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
            confirmButtonColor: "#DD6B55",
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
            if (inputValue === false || inputValue === col) return false;
            if (inputValue === "") {
                swal.showInputError("You need to write something!");
                return false;
            }
            renameCollection(db, col, inputValue);
        });
    };

    function renameCollection(db, col_from, col_to) { 
        api.renameCollection(db, col_from, col_to)
            .success(function(data) {
                if (data.success) {
                    swal({title: "Success!",text: "Collection has been renamed.",type: "success",timer: 1300});
                    getCollection($stateParams.db_name);
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
                    swal({title: "Deleted!",text: "Collection has been deleted.",type: "success",timer: 1300});
                    getCollection($stateParams.db_name);
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

app.controller('CollectionBrowserController', function($scope, $rootScope, $stateParams, api, $state, $location, $timeout, $modal) {
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
            confirmButtonColor: "#DD6B55",
        }, function() {
            deleteDocument($scope.currentDb, $scope.currentCol, id);
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
        getDocument($stateParams.db_name, $stateParams.col_name, $scope.currentPage, limit);
        $state.transitionTo('collections', {
            db_name: $scope.currentDb,
            col_name: $scope.currentCol,
            page: $scope.currentPage
        }, {
            notify: false
        });
    };

    /**
     * Private methods
     */
    function highlight() {
        $('#documents').find('pre').each(function(index, el) {
            if ($(el).find('span').length) {
                return;
            }
            Prism.highlightElement(el);
        });
    }

    function getDocument(dbName, colName, page, limit) {
        api.getDocumentList(dbName, colName, page, limit)
            .success(function(documents) {
                $scope.currentPage = page;
                started = true;
                // for (var i = documents.items.length - 1; i >= 0; i--) {
                //     documents.items[i] = 
                // }
                $scope.documents = documents;
                $timeout(function() {
                    highlight();
                }, 5);
                $timeout(function() {
                    highlight();
                }, 100);
            })
            .error(function(error) {
                console.log(error);
                swal("Oops...", 'Unable to load data', "error");
            });
    }


    function refresh() {
        getDocument($stateParams.db_name, $stateParams.col_name, $stateParams.page, limit);
    }


    function deleteDocument(db, col, id) {
        api.deleteDocument(db, col, id)
            .success(function(data) {
                if (data.success) {
                    swal({
                        title: "Deleted!",
                        text: "Document has been deleted.",
                        type: "success",
                        timer: 1300,
                    });
                    refresh();
                } else {
                    swal("Oops...", 'Document Not Found', "error");
                }
            }).error(function(error) {
                swal("Oops...", 'Error', "error");
            });
    }

    getDocument($stateParams.db_name, $stateParams.col_name, $stateParams.page, limit);
});


app.controller('DocumentEditorController', function($scope, $rootScope, $stateParams, api, $state, $modalInstance, item) {
    $scope.temp = null;

    setTimeout(function() {
        $('.CodeMirror').each(function(i, el) {
            el.CodeMirror.refresh();
        });
    }, 5);

    $scope.editorOptions = {
        lineWrapping: true,
        lineNumbers: true,
        keyMap: "sublime",
        tabSize: 4,
        mode: "application/ld+json",
        // readOnly: 'nocursor',
        autofocus: true,
        scrollbarStyle: "simple",
        theme: "material",
        foldGutter: true,
        lint: true,
        extraKeys: {
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                $('.modal-dialog').toggleClass('fullscreen');
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                $('.modal-dialog').removeClass('fullscreen');
            }
        },
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter", "CodeMirror-lint-markers"],
    };

    $scope.item = item;

    // .on('shown.bs.modal', function() {
    //     console.log('show');
    //     // initialize codemirror here
    // });

    $scope.ok = function() {
        $modalInstance.close();
    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
});