app = angular.module('MongoApp');

app.factory('api', function($http) {

    var baseurl = '/api';
    var dataFactory = {};

    dataFactory.index = function () {
        
        return $http.get(baseurl);
    };

    dataFactory.getCollectionList = function (db) {
        return $http.get(baseurl + '/get_collections/' + db + '/1');
    };

    dataFactory.getDocumentList = function (db, collection, page, limit) {
        page = page ? page : 1;
        limit = limit ? limit : 0;
        return $http.get(baseurl +'/get_documents/' + db + '/' + collection + "/" + page + '/' + limit);
    };

    dataFactory.deleteDocument = function (db, col, doc) {
        return $http.delete(baseurl +'/document/'+db+'/'+col+"/"+doc);
    };

    dataFactory.dropCollection = function (db, col) {
        return $http.delete(baseurl +'/collection/'+db+'/'+col);
    };

    dataFactory.dropDb = function (db) {
        return $http.delete(baseurl +'/db/'+ db);
    };

    dataFactory.renameCollection = function (db, col_from, col_to) {
        return $http.put(baseurl +'/collection/'+db+'/'+col_from+'/'+col_to);
    };

    dataFactory.renameDatabase = function (from,to) {
        return $http.put(baseurl +'/db/'+from+'/'+to);
    };

    return dataFactory;
});
