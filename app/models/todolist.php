<?php

class ToDoList extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->loadTable('todolist');
    }

    public function getTagId($tag)
    {
        $result = $this->findBy('name', $tag);
        return empty($result) ? 0 : $result['id'];
    }
}