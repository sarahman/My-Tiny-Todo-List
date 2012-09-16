<?php

class Tag extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->loadTable('tags');
    }

    public function getTagId($tag)
    {
        $result = $this->findBy('name', $tag);
        return empty($result) ? 0 : $result['id'];
    }
}