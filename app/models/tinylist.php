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
        $where = array();

        if ($conditions['isLogged']) {
            $where['published'] = 1;
        }

        $result = $this->findAll($where, '*', 'ow ASC, id ASC');

        $t = array();
        $t['total'] = 0;
        foreach($result AS $row) {
            $t['total']++;
            $t['list'][] = $this->prepareList($row);
        }

        return $t;
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