<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;

class ApiController extends Controller {
	public function __construct() {
		$this->mongoClient = DB::getMongoClient();
	}

	public function index() {
		$dbs = $this->getDbList();
		foreach ($dbs as $key => $db) {
			$dbs[$key]['collection'] = $this->getCollectionList($db['name']);
		}
		return [
			'databases' => $dbs,
		];
	}

	public function getCollectionList($db, $detail = false) {
		$db = $this->mongoClient->selectDB($db);
		if ($detail) {
			$collections = [];
			foreach ($db->getCollectionNames() as $collection) {
				$stats         = $db->command(array('collStats' => $collection));
				$collections[] = array_merge(['name' => $collection], $stats);
			}
			return $collections;
		}
		return $db->getCollectionNames();
	}

	public function getDbList() {
		return $this->mongoClient->listDBs()['databases'];
	}

	public function getDocumentList($db, $collection, $page = 1, $limit = 10) {
		$page       = $page - 1;
		$connection = $this->createTemporaryConnection($db);
		$docsObjs   = $connection->collection($collection)->skip($page * $limit)->limit($limit)->get();
		$count      = $connection->collection($collection)->count();
		$objs       = [];
		foreach ($docsObjs as $obj) {
			$objs[] = [
				'string' => json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
				'data'   => $obj,
			];
		}
		return [
			'items'   => $objs,
			'count'   => $count,
			'page_no' => (int) ($count / $limit),
		];
	}

	public function deleteDocument($db, $collection, $id) {
		$doc = $this->createTemporaryConnection($db)
			->collection($collection)
			->where('_id', $id)
			->delete();
		return $this->responseSuccess();
	}

	public function renameCollection($db, $collection_from, $collection_to) {
		$result = $this->mongoClient->admin->command(array(
			"renameCollection" => "$db.$collection_from",
			"to"               => "$db.$collection_to",
		));
		if ($result['ok']) {
			return $this->responseSuccess();
		}
		return $this->responseFail();
	}

	public function dropCollection($db, $collection) {
		$result = $this->mongoClient->$db->$collection->drop();
		if ($result['ok']) {
			return $this->responseSuccess();
		}
		return $this->responseFail();
	}

	protected function createTemporaryConnection($db) {
		$default            = config('database.default');
		$config             = config('database.connections.' . $default);
		$config['database'] = $db;
		config()->set([
			'database.connections.temp' => $config,
		]);
		return DB::connection('temp');
	}
}
