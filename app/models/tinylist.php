<?php

class TinyList extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->loadTable('lists');
    }

    public function get($conditions = array())
    {
        if (!empty($conditions['isLogged'])) {
            $conditions['published'] = 1;
        }

        unset($conditions['isLogged']);

        $result = $this->findAll($conditions, '*', 'ow ASC, id ASC');

        $t = array();
        $t['total'] = 0;
        foreach($result AS $row) {
            $t['total']++;
            $t['list'][] = $this->prepareList($row);
        }

        return $t;
    }

    public function add(array $data)
    {
        $CI = & get_instance();
        $CI->load->helper('database');
        $data['uuid'] = generateUUID();
        $data['name'] = str_replace(array('"',"'",'<','>','&'),array('','','','',''),$data['name']);
        $data['d_created'] = $data['d_edited'] = time();

        return $this->insert($data);
    }

    public function modify(array $data)
    {
        $data['name'] = str_replace(array('"',"'",'<','>','&'),array('','','','',''),$data['name']);
        $data['d_edited'] = time();

        return $this->update($data, $data['list']);
    }

    public function delete(array $data)
    {
        $escapedId = $this->db->escape($data[$this->primaryKey]);
        $this->db->trans_start();
        $this->db->query("DELETE FROM {$this->db->dbprefix('lists')} WHERE `id`={$escapedId}");
        $this->db->query("DELETE FROM {$this->db->dbprefix('tag2task')} WHERE `list_id`={$escapedId}");
        $this->db->query("DELETE FROM {$this->db->dbprefix('todolist')} WHERE `list_id`={$escapedId}");
        $this->db->trans_complete();
    }

    public function getUserListsSimple()
    {
        $result = $this->findAll(null, 'id, name', 'id ASC');
        $a = array();

        foreach ($result AS $row) {
            $a[$row['id']] = $row['name'];
        }

        return $a;
    }

    public function checkPublishedListExist($listId = null)
    {
        if (empty($listId)) {
            return false;
        }

        $result = $this->find(array($this->primaryKey => $listId, 'published' => 1));
        return !empty($result);
    }

    function checkListExist($listId = null)
    {
        $result = $this->findCount(array('id' => $listId));
        return !empty($result);
    }

    public function prepareList($row)
    {
        $taskview = (int)$row['taskview'];
        return array(
            'id' => $row['id'],
            'name' => htmlarray($row['name']),
            'sort' => (int)$row['sorting'],
            'published' => $row['published'] ? 1 : 0,
            'showCompl' => $taskview & 1 ? 1 : 0,
            'showNotes' => $taskview & 2 ? 1 : 0,
            'hidden' => $taskview & 4 ? 1 : 0,
        );
    }
}