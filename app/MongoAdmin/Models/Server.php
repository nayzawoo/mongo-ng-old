<?php namespace App\MongoAdmin\Models;


use Illuminate\Database\DatabaseManager;
use Jenssegers\Mongodb\Connection;
use Exception;

class Server implements \ArrayAccess
{
    protected $client;

    protected $db;

    protected $dbs;

    protected $connection;
    /**
     * @var DatabaseManager
     */
    private $dbManager;

    public function __construct(\MongoClient $client, DatabaseManager $dbManager)
    {
        $this->client = $client;
        $this->dbManager = $dbManager;
    }

    /**
     * @return array
     */
    public function listDbs()
    {
        return $this->dbs = $this->client->listDBs()['databases'];
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getDb($name) {
        $this->createTemporaryConnection($name);
        return $this->db = new Database($this, $this->connection, $name);
    }

    /**
     * @param $db
     * @return Connection
     */
    protected function createTemporaryConnection($db) {
        $this->mergeConfig($db);
        return $this->connection =  $this->dbManager->connection('temp');
    }

    protected function  mergeConfig($db)
    {
        $default            = config('database.default');
        $config             = config('database.connections.' . $default);
        $config['database'] = $db;
        return config()->set([
            'database.connections.temp' => $config,
        ]);
    }

    public function offsetExists($name)
    {
        foreach ($this->listDbs() as $db) {
            if ($db['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $offset
     * @return Database
     */
    public function offsetGet($offset)
    {
        return $this->getDb($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception;
    }


    public function offsetUnset($offset)
    {
        $this->getDb($offset)->drop();
    }
}
