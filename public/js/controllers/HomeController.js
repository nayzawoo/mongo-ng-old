app = angular.module('MongoApp');

app.controller('HomeController', function ($rootScope, $scope, $state, api) {
    $scope.browseDB = function (db_name) {
        $state.go("db", {
            db_name: db_name
        });
    };

    $scope.dropDb = function (db) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this database!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#DD6B55"
        }, function () {
            dropDb(db);
        });
    };

    $scope.renameDatabase = function (from) {
        swal({
            title: "Rename Database",
            text: "Database Name",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "Database Name",
            showLoaderOnConfirm: true,
            inputValue: from
        }, function (to) {
            if (to === false || to == from) {
                swal.showInputError("Wrong!");
                return false;
            }
            if (to === "") {
                swal.showInputError(
                    "You need to write something!");
                return false;
            }
            renameDatabase(from, to);
        });
    };

    function renameDatabase(from, to) {
        api.renameDatabase(from, to)
            .success(function (data) {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: "Database has been renamed.",
                        type: "success",
                        timer: 600
                    });
                    $rootScope.$emit("db.rename", {from: from, to: to});
                    return;
                }
                swal("Oops...", 'Error!', "error");
            })
            .error(function (error) {
                swal("Oops...", 'Error', "error");
            });
    }

    function index() {
        api.index()
            .success(function (dbs) {
                $scope.$parent.dbs = dbs.databases;
                setTimeout(function () {

                }, 50);
            })
            .error(function (error) {
                swal("Oops...", 'Unable to load data', "error");
            });
    }

    function dropDb(db) {
        api.dropDb(db.name)
            .success(function (dbs) {
                if (dbs.success) {
                    swal({
                        title: "Success!",
                        type: "success",
                        timer: 1300
                    });
                    $rootScope.$emit("db.drop", db.name);
                    return;
                }
                swal("Oops...", 'Error!', "error");
            })
            .error(function (error) {
                swal("Oops...", 'Error!', "error");
            });
    }

    function init() {
        index();
    }

    init();
});