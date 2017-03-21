<?php

namespace App\Http\Controllers;

use App\MongoAdmin\Json\Json;
use App\MongoAdmin\Models\Server;
use DB;
use App\Libs\Response\ApiResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponse;

    protected $client;

    protected $server;

    public function __construct()
    {
        $this->client = DB::getMongoClient();
        $this->server = new Server($this->client, app('db'));
    }

    public function index()
    {
        $databases = $this->getDbList();
        foreach ($databases as $key => $db) {
            $collections = $this->getCollectionList($db['name']);
            $databases[$key]['collections'] = $collections['collections'];
            $databases[$key]['stats'] = $collections['db_stats'];
        }

        return compact('databases');
    }

    /**
     * @param $db
     * @return array
     */
    public function getDbStats($db)
    {
        $stats = $this->server[$db]->getStats();
        // dd($stats);
        $_size = $stats['storageSize'] + $stats['indexSize'];
        return array_merge($stats, compact('_size'));
    }

    public function getDbList()
    {
        return $this->server->listDbs();
    }

    public function getCollectionList($db)
    {
        $collections = $this->server[$db]->listCollections();
        $db_stats = $this->getDbStats($db);
        return compact('collections', 'db_stats');
    }

    public function getDocumentList($db, $collection, $page = 1, $limit = 1)
    {
        $result = $this->server[$db][$collection]->listDocuments($page, $limit)->toJson();
        return $result;
    }


    public function renameCollection($db, $from, $to)
    {
        $result = $this->server[$db][$from]->rename($to);
        return $result ? $this->responseSuccess() : $this->responseBadRequest();
    }

    public function renameDatabase($db, $to)
    {
        if (!$this->checkDbExists($db)) return $this->responseErr("db_not_found");
        $result = $this->server[$db]->renameDatabase($to);
        if ($result['ok']) {
            return $this->responseSuccess();
        }

        return $this->responseBadRequest("Error");
    }

    public function dropDb($db)
    {
        $result = $this->server[$db]->drop();
        if ($result['ok']) {
            return $this->responseSuccess();
        }

        return $this->responseFail();
    }

    public function dropCollection($db, $collection)
    {
        $result = $this->server[$db][$collection]->drop();
        if ($result['ok']) {
            return $this->responseSuccess();
        }

        return $this->responseBadRequest("Error");
    }

    public function deleteDocument($db, $collection, $id)
    {
        $this->server[$db][$collection]->deleteDocument($id);
        return $this->responseSuccess();
    }

    public function findDocument($db, $collection, $id)
    {

        $result = $this->server[$db][$collection]->find($id)->toJson();

        return $result;
    }

    public function searchDocument($db, $collection, Request $request)
    {
        $query = JSON::decode($request->get('query'));
        $result = $this->server[$db][$collection]->search($query, 30)->toJson();
        return $result;
    }

}
