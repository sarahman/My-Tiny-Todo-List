<?php

class ToDoList extends My_Model
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

    protected function prepareList($row)
    {
        $taskview = (int)$row['taskview'];
        return array(
            'id' => $row['id'],
            'name' => $this->htmlarray($row['name']),
            'sort' => (int)$row['sorting'],
            'published' => $row['published'] ? 1 : 0,
            'showCompl' => $taskview & 1 ? 1 : 0,
            'showNotes' => $taskview & 2 ? 1 : 0,
            'hidden' => $taskview & 4 ? 1 : 0,
        );
    }

    protected function htmlarray($a, $exclude=null)
    {
        $this->htmlarray_ref($a, $exclude);
        return $a;
    }

    protected function htmlarray_ref(&$a, $exclude=null)
    {
        if(!$a) return;
        if(!is_array($a)) {
            $a = htmlspecialchars($a);
            return;
        }
        reset($a);
        if($exclude && !is_array($exclude)) $exclude = array($exclude);
        foreach($a as $k=>$v)
        {
            if(is_array($v)) $a[$k] = htmlarray($v, $exclude);
            elseif(!$exclude) $a[$k] = htmlspecialchars($v);
            elseif(!in_array($k, $exclude)) $a[$k] = htmlspecialchars($v);
        }
        return;
    }
}