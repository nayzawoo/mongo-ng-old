<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\MongoAdmin\Json\Json;
use App\MongoAdmin\Models\Server;
use DB;

class ApiController extends Controller
{

    protected $client;

    protected $server;

    public function __construct()
    {
        $this->client = DB::getMongoClient();
        $this->server = new Server($this->client, app('db'));
    }

    public function index()
    {
        $databases = $this->server->listDbs();
        foreach ($databases as $key => $db) {
            $databases[$key]['collection'] = $this->server[$db['name']]->listCollectionNames();
        }
        return compact('databases');
    }

    public function getCollectionList($db)
    {
        $collections = $this->server[$db]->listCollections();
        $db_stats = $this->server[$db]->getStats();
        return compact('collections', 'db_stats');
    }

    public function getDbList()
    {
        return $this->server->listDbs();
    }

    public function getDocumentList($db, $collection, $page = 1, $limit = 10)
    {
        $result = $this->server[$db][$collection]->listDocuments($page, $limit);
        $items = [];
        foreach ($result['documents'] as $document) {
            $items[] = [
                'json' => Json::encodeReadable($document),
                'data' => $document,
            ];
        }

        return [
            'items' => $items,
            'count' => $result['count'],
            'page' => $result['page'],
            'page_no' => (int)($result['count'] / $limit),
        ];
    }

    public function deleteDocument($db, $collection, $id)
    {
        $this->server[$db][$collection]->deleteDocument($id);
        return $this->responseSuccess();
    }

    public function renameCollection($db, $from, $to)
    {
        $result = $this->server[$db][$from]->rename($to);
        return $result ? $this->responseSuccess() : $this->responseFail();
    }

    public function dropCollection($db, $collection)
    {
        $result = $this->server[$db][$collection]->drop();
        if ($result['ok']) {
            return $this->responseSuccess();
        }
        return $this->responseFail();
    }

    public function test()
    {
    }
}
