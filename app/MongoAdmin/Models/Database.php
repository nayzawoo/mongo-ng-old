<?php namespace App\MongoAdmin\Models;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Connection;
use Mockery\CountValidator\Exception;

/**
 * Class Database
 */
class Database implements \ArrayAccess
{
    protected $connection;

    protected $mongoDatabase;

    private $server;

    public $name;

    protected $db;

    function __construct(Server $server, Connection $connection, $name)
    {
        $this->server = $server;
        $this->name = $name;
        $this->connection = $connection;
    }

    public function drop() {
        return $this->getClient()->{$this->name}->drop();
    }

    public function listCollectionNames() {
        return $this->getMongoDb()->getCollectionNames();
    }

    /**
     * List Collections Name and Stats
     * @return array
     */
    public function listCollections() {
        $collections = [];
        foreach ($this->listCollectionNames() as $collection) {
            $stats         = $this->getMongoDb()->command(array('collStats' => $collection));
            $collections[] = array_merge([
                'name' => $collection
            ], $stats);
        }
        return $collections;
    }

    public function getCollection($name) {
        $queryBuilder = $this->connection->collection($name);
        return new Collection($this, $queryBuilder, $name);
    }

    public function renameDatabase($to) {
        $result = $this->copyDatabase($to);
        $oldName = $this->name;
        if ($result['ok']) {
            return $this->getClient()->{$oldName}->drop();
        }
        throw new Exception;
    }

    public function copyDatabase($to) {
        $dbDefault = config('database.default');
        return $this->getClient()->admin->command([
            'copydb' => 1,
            // 'fromhost' => config('database.connections.'. $dbDefault . '.host'),
            'fromdb' => $this->name,
            'todb' => $to
        ]);
    }

    public function getStats() {
        return $this->getMongoDb()->command(['dbStats' => 1]);
    }

    public function getMongoCollections() {
        return $this->getMongoDb()->listCollections();
    }

    public function getMongoDb() {
        if (!$this->db) {
            $this->db = $this->getClient()->selectDB($this->name);
        }
        return $this->db;
    }

    public function getClient() {
        return $this->server->getClient();
    }

    public function offsetExists($offset)
    {
        return in_array($offset, $this->listCollectionNames());
    }

    public function offsetGet($offset)
    {
        return $this->getCollection($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception;
    }

    public function offsetUnset($offset)
    {
        return $this->getCollection($offset)->drop();
    }
}
