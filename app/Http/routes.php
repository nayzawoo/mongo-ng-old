<?php

use App\MongoAdmin\Models\Server;

get('/test', function() {
    $r = (new Server(DB::getMongoClient(), app('db')))['admin1']['constituencys']
        ->search(["town" => ["ပူတာအို"]]);
    dd($r);
});

get('/', ['as' => 'home', 'uses' => 'HomeController@home']);

get('/auth/login', [
    'as'   => 'postLogin',
    'uses' => 'Auth\AuthController@getLogin',
]);

Route::any('/auth/logout', [
    'as'   => 'postLogin',
    'uses' => 'Auth\AuthController@getLogout',
]);

post('/auth/login', [
	'as'   => 'postLogin',
	'uses' => 'Auth\AuthController@postLogin',
]);


/**
 * ========================
 * ======= API ============
 * ========================
 */
get('api', [
    'as'  => 'api',
    'uses' => 'ApiController@index',
]);

get('api/get_collections/{db}/{detail}', [
    'uses' => 'ApiController@getCollectionList',
]);

get('api/get_documents/{db}/{collection}/{page?}/{limit?}', [
    'uses' => 'ApiController@getDocumentList',
]);

get('api/find_document/{db}/{collection}/{id}', [
    'uses' => 'ApiController@findDocument',
]);

post('api/search_document/{db}/{collection}/', [
    'uses' => 'ApiController@searchDocument',
]);


delete('api/document/{db}/{collection}/{document}', [
    'uses' => 'ApiController@deleteDocument',
]);

delete('api/collection/{db}/{collection}', [
    'uses' => 'ApiController@dropCollection',
]);

delete('api/db/{db}', [
    'uses' => 'ApiController@dropDb',
]);

put('api/collection/{db}/{collection_from}/{collection_to}', [
    'uses' => 'ApiController@renameCollection',
]);

put('api/db/{db}/{to}', [
    'uses' => 'ApiController@renameDatabase',
]);