<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $tableName;
    protected $primaryKey = 'id';

    private $fields = array();
    private $numRows = null;
    private $insertId = null;
    private $affectedRows = null;
    private $returnArray = true;

    public function loadTable($table, $primaryKey = 'id')
    {
        $this->tableName = $table;
        $this->fields = $this->db->list_fields($table);
        $this->primaryKey = $primaryKey;
    }

    public function findAll($conditions = null, $fields = '*', $order = null, $start = 0, $limit = null)
    {
        if (!empty ($conditions)) {
            if (is_array($conditions)) {
                $this->db->where($conditions);
            } else {
                $this->db->where($conditions, null, false);
            }
        }

        if (!empty ($fields)) {
            $this->db->select($fields);
        }

        if (!empty ($order)) {
            $this->db->order_by($order);
        }

        if (!empty($limit)) {
            $this->db->limit($limit, $start);
        }

        $query = $this->db->get($this->tableName);
        $this->numRows = $query->num_rows();

        return ($this->returnArray) ? $query->result_array() : $query->result();
    }

    public function find($conditions = null, $fields = '*', $order = null)
    {
        $data = $this->findAll($conditions, $fields, $order, 0, 1);

        return (empty ($data)) ? false : $data[0];
    }

    public function field($conditions = null, $name, $fields = '*', $order = null)
    {
        $data = $this->findAll($conditions, $fields, $order, 0, 1);

        if (!empty ($data)) {
            $row = $data[0];
            if (isset($row[$name])) {
                return $row[$name];
            }
        }

        return false;
    }

    public function findCount($conditions = null)
    {
        $data = $this->findAll($conditions, 'COUNT(*) AS count', null, 0, 1);

        return (empty ($data)) ? false : $data[0]['count'];
    }

    public function insert($data = null)
    {
        if (empty ($data)) {
            return false;
        }

        $data = $this->removeNonAttributes($data);

        $this->db->insert($this->tableName, $data);
        $this->insertId = $this->db->insert_id();

        return $this->insertId;
    }

    public function update($data = null, $id = null)
    {
        if ($data == null) {
            return false;
        }

        $data = $this->removeNonAttributes($data);

        if (empty ($id)) {
            $this->db->insert($this->tableName, $data);
            $this->insertId = $this->db->insert_id();
            return $this->insertId;
        } else {
            $this->db->where($this->primaryKey, $id);
            $this->db->update($this->tableName, $data);
            $this->affectedRows = $this->db->affected_rows();
            return $id;
        }
    }

    protected function updateByField($data = array(), $field = null)
    {
        if (empty ($data) || empty ($field)) {
            return false;
        }

        $data = $this->removeNonAttributes($data);

        if (empty ($field)) {
            $this->db->insert($this->tableName, $data);
            $this->insertId = $this->db->insert_id();
            return $this->insertId;
        }

        if (is_array($field)) {
            foreach($field AS $current) {
                if (!empty ($data[$current])) {
                    $this->db->where($current, $data[$current]);
                }
            }
        } else {
            $this->db->where($field, $data[$field]);
        }

        $this->db->update($this->tableName, $data);
        $this->affectedRows = $this->db->affected_rows();
        return true;
    }

    public function remove($id = null)
    {
        if (empty ($id)) {
            return false;
        }

        return $this->db->delete($this->tableName, array($this->primaryKey => $id));
    }

    protected function removeByField($data = array(), $field = null)
    {
        if (empty ($data) || empty ($field)) {
            return false;
        }

        $data = $this->removeNonAttributes($data);

        if (is_array($field)) {
            foreach($field AS $current) {
                if (!empty ($data[$current])) {
                    $this->db->where($current, $data[$current]);
                }
            }
        } else {
            $this->db->where($field, $data[$field]);
        }

        $this->db->delete($this->tableName);
        return true;
    }

    public function __call ($method, $args)
    {
        $watch = array('findBy','findAllBy');

        foreach ($watch as $found) {
            if (stristr($method, $found)) {
                $field = strtolower(str_replace($found, '', $method));
                return $this->$found($field, $args);
            }
        }
    }

    public function findBy($field, $value)
    {
        $where = array($field => $value);
        return $this->find($where);
    }

    public function findAllBy($field, $value)
    {
        $where = array($field => $value);
        return $this->findAll($where);
    }

    public function executeQuery($sql)
    {
        $query = $this->db->query($sql);
        return ($this->returnArray) ? $query->result_array() : $query->result();
    }

    public function getLastQuery()
    {
        return $this->db->last_query();
    }

    public function getInsertString($data)
    {
        return $this->db->insert_string($this->tableName, $data);
    }

    public function getUpdateString($data, $where)
    {
        return $this->db->update_string($this->tableName, $data, $where);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getNumRows()
    {
        return $this->numRows;
    }

    public function getInsertId()
    {
        return $this->insertId;
    }

    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function setReturnArray($returnArray)
    {
        $this->returnArray = $returnArray;
    }

    protected function removeNonAttributes($data = array())
    {
        foreach ($data as $key => $value) {
            if (array_search($key, $this->fields) === false) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}