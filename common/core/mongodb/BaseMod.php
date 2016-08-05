<?php
namespace core\mongodb;

use core\JObject;
use \MongoDB\Driver\BulkWrite as Bulk;
use \MongoDB\Driver\Query;
use \MongoDB\Driver\Command;
use \MongoDB\BSON\Regex;

/**
 * 基础封装,还需详细测试完善,暂不推荐使用,  如有需要建议,先结合需求进行简要测试 或 参阅相关文档,进行封装完善后使用
 * 参考博文 http://my.oschina.net/jsk/blog/644287
 * MongoDB PHP Library https://github.com/mongodb/mongo-php-library
 * PHP Manual http://php.net/manual/en/set.mongodb.php
 * Class BaseMod
 * @package core\mongodb
 */
class BaseMod extends JObject
{
    //--单例模式Start
    private static $instance;

    /**
     * @param string $diy
     * @return BaseMod
     */
    public static function getInstance($diy = '')
    {
        $class = get_called_class();
        $uniKey = md5($class . $diy);
        if (empty(self::$instance[$uniKey])) {
            self::$instance[$uniKey] = new $class();
        }
        return self::$instance[$uniKey];
    }

    private function __construct() { }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //--单例模式End

    protected $tbName;
    protected $dbName;

    protected $select = [];
    protected $where = [];
    protected $limit = 0;
    protected $offset = 0;
    protected $sort = []; 

    /**
     * 增加单条记录
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function insert(array $data)
    {
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        $bulk->insert($data);
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getInsertedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 添加多条记录
     * @param array $rows
     * @return int
     * @throws \Exception
     */
    public function batchInsert(array $rows)
    {
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        foreach ($rows as $row) {
            $bulk->insert($row);
        }
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getInsertedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 删除记录  谨慎使用,会删除符合条件的所有记录
     * @param bool $one  delete One Record filtered by where
     * @return int
     * @throws \Exception
     */
    public function delete($one = false)
    {
        $options = ['limit' => $one];
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        $bulk->delete($this->where, $options);
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getDeletedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 以数组形式操作某列 插入值
     * @param string $column
     * @param mixed $value  强类型  如: 可同时插入  2  "2"
     * @param bool $batch 批量
     * @param bool $skipIfExist 插入值若存在 则跳过
     * @param bool $all  影响所有行
     * @return int
     * @throws \Exception
     */
    public function push(string $column, $value , $batch = false, $skipIfExist = true, $all = false)
    {
        $options = ['upsert' => false, 'multi' => $all];
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        if ($batch && is_array($value)) {
            $value = ['$each'=>$value];
        }
        if ($skipIfExist) {
            $bulk->update($this->where, ['$addToSet' => [$column => $value]], $options);
        } else {
            $bulk->update($this->where, ['$push' => [$column => $value]], $options);
        }
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getModifiedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 以数组形式操作某列 移除值
     * @param string $column
     * @param mixed $value
     * @param bool $all  影响所有行
     * @return int
     * @throws \Exception
     */
    public function pull(string $column, $value, $all = false)
    {
        $options = ['upsert' => false, 'multi' => $all];
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        $bulk->update($this->where, ['$pull' => [$column => $value]], $options);
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getModifiedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 更新记录
     * @param array $data ['col1'=>'val1', 'col2'=>2 , ...]
     * @param bool $all update All Record filtered by where
     * @param string $operator such as $set $unset $push $pop $pull etc.
     * $operator view https://docs.mongodb.com/manual/reference/operator/update/
     * @param bool $insertIfNotFound insert data if filter find none documents
     * @return int
     * @throws \Exception
     */
    public function update($data, $all = true, $operator = '$set', $insertIfNotFound = false)
    {
        $options = ['upsert' => $insertIfNotFound, 'multi' => $all];
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        $bulk->update($this->where, [$operator => $data], $options);
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            if ($insertIfNotFound) {
                return $result->getModifiedCount() + $result->getUpsertedCount();
            } else {
                return $result->getModifiedCount();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 更新记录统计数量
     * @param array $data [ 'col1' => 1 ,'col2'=> -1 ]  value mast integer
     * @param bool $all update all counters filtered by $this->where
     * @return int
     * @throws \Exception
     */
    public function updateCounters($data, $all = true)
    {
        $options = ['upsert' => false, 'multi' => $all];
        $mongo = Connection::getInstance($this->getDbName());
        $bulk = new Bulk(['ordered' => true]);
        $bulk->update($this->where, ['$inc' => $data], $options);
        try {
            $result = $mongo->executeBulkWrite(
                $this->getDbName() . '.' . $this->getTbName(),
                $bulk
            );
            return $result->getModifiedCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询单条记录
     * @return mixed
     * @throws \Exception
     */
    public function one()
    {
        $filter = [];
        $options = ['limit' => 1];
        if ($this->select) {
            $options['projection'] = $this->select;
        }
        if ($this->offset) {
            $options['skip'] = $this->offset;
        }
        if ($this->sort) {
            $options['sort'] = $this->sort;
        }
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeQuery(
            $this->getDbName() . '.' . $this->getTbName(),
            new Query($filter, $options)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return current($cursor->toArray());
    }

    /**
     * 查询多条记录
     * @return array
     * @throws \Exception
     */
    public function all()
    {
        $options = [];
        if ($this->select) {
            $options['projection'] = $this->select;
        }
        if ($this->limit) {
            $options['limit'] = $this->limit;
        }
        if ($this->offset) {
            $options['skip'] = $this->offset;
        }
        if ($this->sort) {
            $options['sort'] = $this->sort;
        }
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeQuery(
            $this->getDbName() . '.' . $this->getTbName(),
            new Query($this->where, $options)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return $cursor->toArray();
    }

    /**
     * 根据条件查询某列的 唯一值, limit offset 无效
     * @param string $key
     * @return array|bool|null
     * @throws \Exception
     */
    public function distinct(string $key)
    {
        $options = [
            'distinct' => $this->getTbName(),
            'key' => $key,
        ]; 
        if ($this->where) {
            $options['query'] = $this->where;
        }
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeCommand(
            $this->getDbName(),
            new Command($options)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $result = $cursor->toArray();
        if (isset($result[0]['ok'])) {
            if ($result[0]['ok'] == 1) {
                return $result[0]['values'];
            } else {
                return false;
            }
        } else {
            return null;
        }
    }
    
    /**
     * @return int|bool|null
     * @throws \Exception
     */
    public function count()
    {
        $options = [
            'count' => $this->getTbName(),
            'query' => $this->where,
        ];

        if ($this->limit) {
            $options['limit'] = $this->limit;
        }
        if ($this->offset) {
            $options['skip'] = $this->offset;
        } 
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeCommand(
            $this->getDbName(),
            new Command($options)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $result = $cursor->toArray();
        if (isset($result[0]['ok'])) {
            if ($result[0]['ok'] == 1) {
                return intval($result[0]['n']);
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    /**
     * 查询条件 推荐使用此方法添加,  注意: 部分条件  如模糊搜索 会覆盖同字段已有查询条件
     * @param string $column
     * @param string $filter  operators from mongodb ,or key in operatorMap + [like, regex]
     * $filter = like or regex
     * $value =
     *     'a' (same as sql like '%a%' )  '^a' (same as sql like 'a%')  ...  use default flag i to ignore case
     *     or
     *     [
     *       pattern => '^a b$ ...'
     *       flag => 'i' (ignore case,match A or a)  or  '' (match case)
     *     ]
     * @param mixed $value
     * @return $this
     */
    public function addFilter($column, $filter, $value)
    {
        $operatorMap = [ //otherwise use like or regex
            'and' => '$and',
            'or' => '$or',
            'in' => '$in',
            'not in' => '$nin',
            '>' => '$gt',
            '<' => '$lt',
            '>=' => '$gte',
            '<=' => '$lte',
            '!=' => '$ne',
            '<>' => '$ne',
            '=' => '$eq',
            '==' => '$eq',
        ]; 
        if (isset($operatorMap[strtolower($filter)])) {
            $filter = $operatorMap[strtolower($filter)];
            $this->where[$column][$filter] = $value;
        } else if (in_array(strtolower($filter), ['like', 'regex'])) {
            if (is_string($value)) {
                $regex = new Regex($value, 'i');
            } else {
                $regex = new Regex($value['pattern'], $value['flag']);
            }
            $this->where[$column] = $regex;
        } else {
            $this->where[$column][$filter] = $value;
        }
        return $this;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function where(array $filter)
    {
        $this->where = $filter;
        return $this;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function andWhere(array $filter)
    {
        if (!$this->where) {
            $this->where = $filter;
        } elseif (!isset($this->where['$or'])) {
            foreach ($filter as $op => $val) {
                $this->where[$op] = $val;
            }
        } else {
            $this->where = [
                '$and' => [
                    $this->where,
                    $filter
                ]
            ];
        }
        return $this;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function orWhere(array $filter)
    {
        if (!$this->where) {
            $this->where = $filter;
        } else {
            $this->where = [
                '$or' => [
                    $this->where,
                    $filter
                ]
            ];
        }
        return $this;
    }

    /**
     * @param array $attr
     * include columns  eg: [ 'name'=>1, 'age'=>1]
     * or
     * exclude columns  eg: [ 'sex'=>0 ]
     * @return $this
     */
    public function select(array $attr)
    {
        $this->select = $attr;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offset = intval($offset);
        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function sort(array $sort)
    {
        $this->sort = $sort;
        return $this;
    }
    
    public function getTbName()
    {
        if ($this->tbName) return $this->tbName;
        $ca = explode('\\TB', get_called_class());
        $this->tbName = isset($ca[1]) ? strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $ca[1])) : 'TableNotExist';
        return $this->tbName;
    }

    public function setTbName($tableName)
    {
        $this->tbName = $tableName;
    }

    public function getDbName()
    {
        if ($this->dbName) return $this->dbName;
        return 'common';
    }

    public function setDbName($databaseName)
    {
        $this->dbName = $databaseName;
    }

    public function exec(array $command)
    {
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeCommand(
            $this->getDbName(),
            new Command($command)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return $cursor->toArray();
    }

    public function query($filter, $options = [])
    {
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeQuery(
            $this->getDbName() . '.' . $this->getTbName(),
            new Query($filter, $options)
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return $cursor->toArray();
    }

    public function getDatabases()
    {
        $mongo = Connection::getInstance($this->getDbName());
        $cursor = $mongo->executeCommand(
            'admin',
            new Command(['listDatabases' => 1])
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return $cursor->toArray();
    }

    public function getCollections($db = '')
    {
        $db = $db ? : $this->getDbName();
        $mongo = Connection::getInstance($db);
        $cursor = $mongo->executeCommand(
            $db,
            new Command(['listCollections' => 1])
        );
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        return $cursor->toArray();
    }

}