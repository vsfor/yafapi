<?php
namespace core\mysqldb;
use core\JObject;
use vendor\jeen\JLog;
use vendor\yiihelpers\Inflector;

class BaseMod extends JObject
{
    //--单例模式Start
    private static $instance;

    /**
     * @param string $diy
     * @return BaseMod
     */
    public static function getInstance($diy = ''){
        $class = get_called_class();
        $uniKey = md5($class . $diy);
        if(empty(self::$instance[$uniKey])){
            self::$instance[$uniKey] = new $class();
        }
        return self::$instance[$uniKey];
    }
    private function __construct() { }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //--单例模式End


    protected $tbName;
    protected $dbName;

    protected $select='*';
    protected $where='';
    protected $params=[]; //以 :param 占位符为标准   统一禁用 ? 占位符
    protected $limit=0;
    protected $offset=0;
    protected $order='';
    protected $group='';
    protected $distinct=false;

    protected $sql='';
    protected $lastsql='';

    /**
     * 增加单条记录
     * @param array $data
     * @return bool|string
     */
    public function insert($data)
    {
        $cols = array_keys($data);
        $sql = "INSERT INTO "
            . $this->getTbName()
            . " (`" . implode("`, `", $cols) . "`) "
            . "VALUES (:" . implode(", :", $cols) . ") ";
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($data as $k => $v) {
            if ($v === false) {
                $v = 0;
            } elseif ($v === null) {
                $v = 'NULL';
            }
            $stmt->bindValue(":{$k}", $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if ($res) {
            return Connection::getInstance($this->getDbName())->lastInsertId();
        }
        return false;
    }

    /**
     * 增加多条记录
     * @param array $columns
     * @param array $rows
     * @return bool|int
     */
    public function batchInsert($columns, $rows)
    {
        $values = [];
        foreach ($rows as $row) {
            $vs = [];
            foreach ($row as $i => $value) {
                if (is_string($value)) {
                    $value = $this->quoteValue($value);
                } elseif ($value === false) {
                    $value = 0;
                } elseif ($value === null) {
                    $value = 'NULL';
                }
                $vs[] = $value;
            }
            $values[] = '(' . implode(', ', $vs) . ')';
        }
        $sql = "INSERT INTO "
            . $this->getTbName()
            . " (`" . implode("`, `", $columns) . "`) "
            . "VALUES " . implode(', ', $values);
        unset($columns);
        unset($values);
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        unset($sql);
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if ($res) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 删除记录
     * @param string $condition
     * @param array $params
     * @return bool|int
     */
    public function delete($condition, $params = [])
    {
        $sql = "DELETE FROM "
            . $this->getTbName()
            . " WHERE $condition";
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, $this->datatype($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if ($res) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 更新记录
     * @param array $data
     * @param string $condition  不可包含 ? 占位符
     * @param array $params  必须使用 :param 占位符
     * @return bool|int
     */
    protected function update($data, $condition = '', $params = [])
    {
        $sets = [];
        $vals = [];
        foreach ($data as $col=>$val) {
            $sets[] = "`$col` = :new_{$col}_val";
            $vals[":new_{$col}_val"] = $val;
        }
        if (empty($sets)) return 0;

        $sql = "UPDATE "
            . $this->getTbName()
            . " SET " . implode(", ", $sets)
            . (($condition) ? " WHERE $condition" : "");
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);

        foreach ($vals as $k => $v) {
            $stmt->bindValue($k, $v, $this->datatype($v));
        }
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, $this->datatype($v));
        }

        $res = $stmt->execute();
        if ($res) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 更新记录统计数量
     * @param array $data
     * @param string $condition  不可包含 ? 占位符
     * @param array $params  必须使用 :param 占位符
     * @return bool|int
     */
    protected function updateCounters($data, $condition = '', $params = [])
    {
        $sets = [];
        foreach ($data as $col=>$val) {
            $val = intval($val);
            if ($val != 0) {
                $sets[] = "`$col` = (`$col`+($val))";
            }
        }
        if (empty($sets)) return 0;

        $sql = "UPDATE "
            . $this->getTbName()
            . " SET " . implode(", ", $sets)
            . (($condition) ? " WHERE $condition" : "");
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, $this->datatype($v));
        }

        $res = $stmt->execute();
        if ($res) {
            return $stmt->rowCount();
        }
        return false;
    }

    public function one()
    {
        $this->limit(1);
        $sql = $this->getSql();
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($this->params as $k => $v) {
            $stmt->bindValue($k, $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if($res) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return false;

    }

    public function all()
    {
        $sql = $this->getSql();
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($this->params as $k => $v) {
            $stmt->bindValue($k, $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if($res) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function count($q='*')
    {
        $sql =  "SELECT COUNT(";
        if($this->distinct) {
            $this->sql .= " DISTINCT ";
        }
        $sql .= $q .") FROM ".$this->getTbName();
        if($this->where) {
            $sql .= " WHERE {$this->where}";
        }
        if($this->group) {
            $sql .= " GROUP BY {$this->group}";
        }

        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($this->params as $k => $v) {
            $stmt->bindValue($k, $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if($res) {
            return $stmt->fetchColumn(0);
        }
        return 0;
    }

    public function scalar()
    {
        $sql = $this->getSql();
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($this->params as $k => $v) {
            $stmt->bindValue($k, $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if($res) {
            return $stmt->fetchColumn(0);
        }
        return false;
    }

    public function select($attr)
    {
        if(is_string($attr)) {
            $this->select = $attr;
        } else if(is_array($attr)) {
            $this->select = '`'.implode('`,`',$attr).'`';
        }
        $this->sql = '';
        return $this;
    }

    public function distinct($value=true)
    {
        $this->distinct = boolval($value);
        $this->sql = '';
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = intval($limit);
        $this->sql = '';
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = intval($offset);
        $this->sql = '';
        return $this;
    }

    /**
     * @param string $condition
     * @param array $params
     * @return $this
     */
    public function where($condition,$params=[])
    {
        $this->where = $condition;
        $this->addParams($params);
        $this->sql = '';
        return $this;
    }

    /**
     * @param string $condition
     * @param array $params
     * @return $this
     */
    public function andWhere($condition,$params=[])
    {
        if($this->where == '') {
            $this->where = $condition;
        } else {
            $this->where = "({$this->where}) AND ($condition)";
        }
        $this->addParams($params);
        $this->sql = '';
        return $this;
    }

    /**
     * @param string $condition
     * @param array $params
     * @return $this
     */
    public function orWhere($condition,$params=[])
    {
        if($this->where == '') {
            $this->where = $condition;
        } else {
            $this->where = "({$this->where}) OR ($condition)";
        }
        $this->addParams($params);
        $this->sql = '';
        return $this;
    }

    /**
     * @param string $orderBy
     * @return $this
     */
    public function orderBy($orderBy)
    {
        if(is_string($orderBy)) {
            $this->order = $orderBy;
            $this->sql = '';
        }
        return $this;
    }

    /**
     * @param string|array $groupBy
     * @return $this
     */
    public function groupBy($groupBy)
    {
        if(is_string($groupBy)) {
            $this->group = "$groupBy";
        } else if(is_array($groupBy)) {
            $this->group = '`'.implode('`,`',$groupBy).'`';
        } else {
            $this->group = '';
        }
        $this->sql = '';
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    protected function addParams($params)
    {
        if (!empty($params)) {
            if (empty($this->params)) {
                $this->params = $params;
            } else {
                foreach ($params as $name => $value) {
                    if (is_int($name)) {
                        $this->params[] = $value;
                    } else {
                        $this->params[$name] = $value;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getSql()
    {
        if($this->sql) return $this->sql;
        $this->sql .=  "SELECT ";
        if($this->distinct) {
            $this->sql .= " DISTINCT ";
        }
        $this->sql .= $this->select ." FROM ".$this->getTbName();
        if($this->where) {
            $this->sql .= " WHERE {$this->where}";
        }
        if($this->group) {
            $this->sql .= " GROUP BY {$this->group}";
        }
        if($this->order) {
            $this->sql .= " ORDER BY {$this->order}";
        }
        if($this->limit) {
            $this->sql .= " LIMIT ".$this->limit;
        }
        if($this->offset) {
            $this->sql .= $this->limit ? " OFFSET ".$this->offset : " LIMIT {$this->offset},2147483647";
        }
        return $this->sql;
    }

    /**
     * @param mixed $str
     * @return string
     */
    protected function quoteValue($str)
    {
        $str = str_replace("\\","\\\\",$str);
        $str = str_replace("\"","\\\"",$str);
        return "'".str_replace("'","\\'",$str)."'";
    }

    /**
     * @param mixed $param
     * @return int
     */
    protected function dataType($param)
    {
        if (is_bool($param)) {
            return \PDO::PARAM_BOOL;
        } else if (is_int($param)) {
            return \PDO::PARAM_INT;
        } else if (is_null($param)) {
            return \PDO::PARAM_NULL;
        } else {
            return \PDO::PARAM_STR;
        }
    }

    protected function getLastInsertId()
    {
        return Connection::getInstance($this->getDbName())->lastInsertId();
    }

    protected function startTrans()
    {
        return Connection::getInstance($this->getDbName())->beginTransaction();
    }

    protected function commit()
    {
        return Connection::getInstance($this->getDbName())->commit();
    }

    protected function rollBack()
    {
        return Connection::getInstance($this->getDbName())->rollBack();
    }

    /**
     * @return string
     */
    public function getRawSql()
    {
        if(!$this->sql) $this->sql = $this->getSql();
        if(empty($this->params)) {
            return $this->sql;
        }
        $params = [];
        foreach ($this->params as $name => $value) {
            if (is_string($name) && strncmp(':', $name, 1)) {
                $name = ':' . $name;
            }
            if (is_string($value)) {
                $params[$name] = $this->quoteValue($value);
            } elseif (is_bool($value)) {
                $params[$name] = ($value ? 'TRUE' : 'FALSE');
            } elseif ($value === null) {
                $params[$name] = 'NULL';
            } elseif (!is_object($value) && !is_resource($value)) {
                $params[$name] = $value;
            }
        }
        if (!isset($params[1])) {
            return strtr($this->sql, $params);
        }
        $sql = '';
        foreach (explode('?', $this->sql) as $i => $part) {
            $sql .= (isset($params[$i]) ? $params[$i] : '') . $part;
        }

        return $sql;
    }

    public function getTbName()
    {
        if($this->tbName) return $this->tbName;
        $ca = explode('\\TB',get_called_class());
        $this->tbName = isset($ca[1]) ? strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $ca[1])) : 'TableNotExist';
        return $this->tbName;
    }

    public function setTbName($tableName)
    {
        $this->tbName = $tableName;
    }

    public function getDbName()
    {
        if($this->dbName) return $this->dbName;
        return 'common';
    }

    public function setDbName($databaseName)
    {
        $this->dbName = $databaseName;
    }

    public function exec($sql)
    {
        return Connection::getInstance($this->getDbName())->exec($sql);
    }

    public function query($sql, $params = [])
    {
        $stmt = Connection::getInstance($this->getDbName())->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, $this->dataType($v));
        }
        try {
            $res = $stmt->execute();
        } catch(\PDOException $e) {
            throw $e;
        }
        if($res) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return false;
    }

}