app = angular.module('MongoApp');

app.factory('api', function($http) {

    var baseurl = apiUrl;
    var dataFactory = {};

    function _url(url) {
        var pieces = [baseurl].concat(url);
        return pieces.join('/');
    }

    dataFactory.index = function () {
        return $http.get(baseurl);
    };

    dataFactory.getCollectionList = function (db) {
        return $http.get(_url(['get_collections',db, '1']));
    };

    dataFactory.getDocumentList = function (db, collection, page, limit) {
        page = page ? page : 1;
        limit = limit ? limit : 0;
        return $http.get(_url(['get_documents', db, collection, page,limit]));
    };

    dataFactory.deleteDocument = function (db, col, doc) {
        return $http.delete(_url(['document',db, col, doc]));
    };

    dataFactory.findDocumentById = function (db, col, id) {
        return $http.get(_url(['find_document',db, col, id]));
    };

    dataFactory.searchDocument = function (db, col, queryObj) {
        return $http.post(_url(['search_document',db, col]), {query: queryObj});
    };

    dataFactory.dropCollection = function (db, col) {
        return $http.delete(_url(['collection', db, col]));
    };

    dataFactory.dropDb = function (db) {
        return $http.delete(_url(['db', db]));
    };

    dataFactory.renameCollection = function (db, col_from, col_to) {
        return $http.put(_url(['collection',db, col_from, col_to]));
    };

    dataFactory.renameDatabase = function (from,to) {
        return $http.put(_url(['db', from, to]));
    };

    return dataFactory;
});
