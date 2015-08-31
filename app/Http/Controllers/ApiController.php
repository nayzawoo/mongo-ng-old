<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\MongoAdmin\Json\Json;
use App\MongoAdmin\Models\Server;
use DB;
use App\Libs\Response\ApiResponse;

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
        $databases = $this->server->listDbs();
        foreach ($databases as $key => $db) {
            $databases[$key]['collection'] = $this->server[$db['name']]->listCollections();
            $databases[$key]['stats'] = $this->getDbStats($db['name']);
        }

        return compact('databases');
    }

    public function getDbStats($db) {
        $stats = $this->server[$db]->getStats();
        $_size = $stats['fileSize'] + $stats['indexSize'];
        return array_merge($stats, compact('_size'));
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
        $limit = $limit < 1 ? 10 : $limit;
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
            'page_no' => (int) ($result['count'] / $limit),
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

    public function renameDatabase($db, $to)
    {
        $result = $this->server[$db]->renameDatabase($to);
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

        return $this->responseFail();
    }

    public function dropDb($db)
    {
        $result = $this->server[$db]->drop();
        if ($result['ok']) {
            return $this->responseSuccess();
        }

        return $this->responseFail();
    }

    public function findDocument($db, $collection, $id)
    {
        if (!$this->checkId($id)) return $this->responseErr("invalid_id");
        if (!$this->checkDbExists($db)) return $this->responseErr("db_not_found");
        if (!$this->checkCollectionExists($db, $collection)) return $this->responseErr("coll_not_found");

        $result = $this->server[$db][$collection]->find($id);

        if(!$result) return $this->responseErr("doc_not_found");
        return ['items' => [
            [
                'json' => Json::encodeReadable($result),
                'data' => $result,
            ]
        ]];
    }

    //==================
    protected function  checkDbExists($db) {
        return in_array($db, array_pluck($this->getDbList(), 'name'));
    }

    protected function  checkCollectionExists($db, $collection) {
        if (!$this->checkDbExists($db)) {
            return false;
        }
        return in_array($collection, $this->server[$db]->listCollectionNames());
    }


    protected function checkId($id) {
        if (!preg_match("/^[0-9a-fA-F]{24}$/", $id)) {
            return false;
        }
        return true;
    }

    protected function responseErr($type) {
        $m = "Error";
        $code = 400;
        switch($type) {
            case "db_not_found":
                $m = "Database Not Found";
                $code = 404;
                break;
            case "coll_not_found":
                $m = "Collection Not Found";
                $code = 404;
                break;
            case "doc_not_found":
                $m = "Document Not Found";
                $code = 404;
                break;
            case "invalid_id":
                $m = "Invalid MongoId";
                $code = 400;
                break;
        }

        return $this->responseError($m, $code, [], $type);
    }
}
