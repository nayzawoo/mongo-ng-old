<?php namespace App\MongoAdmin\Models;

use Jenssegers\Mongodb\Query\Builder;

class Collection
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var Builder
     */
    private $queryBuilder;

    private $name;

    function __construct(Database $database, Builder $queryBuilder, $name)
    {
        $this->database = $database;
        $this->queryBuilder = $queryBuilder;
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function drop() {
        return $this->database->getMongoDb()->{$this->name}->drop();
    }

    public function listDocuments($page = 1, $limit = 10) {
        $page = (int) ($page < 0 ? 0 : $page);
        $skip = ($page - 1) * $limit;
        $documents = $this->queryBuilder->skip($skip)->limit($limit)->get();
        $count = $this->database->getMongoDb()->{$this->name}->count();
        return compact("documents", "count", "page");
    }

    public function rename($to) {
        $result = $this->database->getClient()->admin->command(array(
            "renameCollection" => $this->database->name.".".$this->name,
            "to"               => $this->database->name.".".$to,
        ));
        return $result['ok'];
    }

    public function getName() {
        return $this->name;
    }

    public function find($id) {
        return $this->queryBuilder->find($id);
    }

    public function search($query, $limit = 30, $skip = 0) {
        return $this->database->getMongoDb()
                    ->{$this->name}
                    ->find($query)
                    ->limit($limit)
                    ->skip($skip);
    }

//    public function search($array) {
//        $query = $this->queryBuilder;
//        foreach($array as $key => $value) {
//            if (is_array($value)) {
//                $query->where($key, 'all', $value);
//            }  else {
//                $query->where($key, $value);
//            }
//        }
//        return $query->limit(10)->get();
//    }

    public function deleteDocument($id) {
        return $this->queryBuilder
                    ->where('_id', $id)
                    ->delete();
    }

    public function updateDocument($id, $data) {
        return $this->queryBuilder
            ->where('_id', $id)
            ->update($data);
    }

    public function createDocument($data) {
        return $this->queryBuilder
            ->insert($data);
    }
}
