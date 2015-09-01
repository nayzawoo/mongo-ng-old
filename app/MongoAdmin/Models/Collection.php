<?php namespace App\MongoAdmin\Models;

use App\MongoAdmin\Exceptions\MongoAdminException;
use App\MongoAdmin\Json\Json;
use Illuminate\Contracts\Support\Jsonable;
use Jenssegers\Mongodb\Query\Builder;

class Collection implements Jsonable
{
    private $database;

    private $queryBuilder;

    private $name;

    protected $data;

    protected $jsonFormat = [];

    function __construct(Database $database, Builder $queryBuilder, $name)
    {
        $this->database = $database;
        $this->queryBuilder = $queryBuilder;
        $this->name = $name;
        $this->collection = $this->database->getMongoDb()->{$this->name};
    }

    public function drop()
    {
        return $this->database->getMongoDb()->{$this->name}->drop();
    }

    public function listDocuments($page = 1, $limit = 10)
    {
        $page = (int)($page < 0 ? 0 : $page);
        $limit = $limit < 1 ? 10 : $limit;
        $skip = ($page - 1) * $limit;
        $documents = iterator_to_array(
            $this->collection
                ->find()
                ->limit($limit)
                ->skip($skip),
            false);
        $count = $this->collection->count();

        $this->data = compact("documents", "count", "page", "limit");
        return $this;
    }

    public function rename($to)
    {
        if ($this->database->offsetExists($to)) {
            throw new MongoAdminException('coll_already_exists');
        }
        $result = $this->database->getClient()->admin->command(array(
            "renameCollection" => $this->database->name . "." . $this->name,
            "to" => $this->database->name . "." . $to,
        ));
        if ($result['ok']) {
            return true;
        } elseif(str_contains($result['errmsg'], 'invalid collection name')) {
            throw new MongoAdminException('invalid_coll_name');
        }
        return $result['ok'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function find($id)
    {
        $result = $this->queryBuilder->find($id);
        $documents = $result ? [$result] : [];
        $this->data = [
            'documents' => $documents,
            'count' => 1,
            'page' => 1,
            'limit' => 1,
        ];
        return $this;
    }

    public function search($query, $limit = 30, $skip = 0)
    {
        $documents = iterator_to_array($this->collection
            ->find($query)
            ->limit($limit)
            ->skip($skip), false);
        $this->data = [
            'documents' => $documents,
            'count' => 1,
            'page' => 1,
            'limit' => 1,
        ];
        return $this;
    }

    public function deleteDocument($id)
    {
        return $this->queryBuilder
            ->where('_id', $id)
            ->delete();
    }

    public function updateDocument($id, $data)
    {
        return $this->queryBuilder
            ->where('_id', $id)
            ->update($data);
    }

    public function createDocument($data)
    {
        return $this->queryBuilder
            ->insert($data);
    }

    protected function formatDocuments($documents)
    {
        $items = [];
        foreach ($documents as $value) {
            $items[] = [
                'json' => JSON::encodeReadable($value),
                'data' => $value,
                'id' => $value['_id'],
            ];
        }
        return $items;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $this->data['documents'] = $this->formatDocuments($this->data['documents']);
        return $this->data;
    }
}
