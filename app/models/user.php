<?php

class User extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->loadTable('tbllecturer', 'LectID');
    }

    public function validateUser($data)
    {
        $query = "SELECT `LectID`, `Name` FROM `{$this->tableName}`
                  WHERE `username`='{$data['username']}' AND `password`='{$data['password']}'";
        $result = $this->db->query($query)->first_row('array');
        return $result ? $result : false;
    }

    public function getUserName($userId)
    {
        $query = "SELECT `Name` FROM `{$this->tableName}`
                  WHERE `LectID`='{$userId}'";
        $result = $this->db->query($query)->first_row('array');
        return $result ? $result['Name'] : false;
    }
}