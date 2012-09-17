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

    public function getOrCreateTag($name)
    {
        $data = array('name' => $name);
        $tag = $this->find($data);
        if (empty($tag)) {
            $tagId = $this->insert($data);
            $tag = array_merge(array('id' => $tagId), $data);
        }

        return $tag;
    }

    public function getTagSuggestion(array $data)
    {
        $listId = $data['list'];
        $begin = $data['q'];
        $limit = $data['limit'];
        ($limit >= 1) || $limit = 8;
        $result = $this->executeQuery("SELECT `name`, `id` FROM {$this->db->dbprefix('tags')}
                INNER JOIN {$this->db->dbprefix('tag2task')} ON `id`=`tag_id`
                    WHERE `list_id`='{$listId}'
                        AND name LIKE '{$this->db->escape_like_str($begin)}'
                            GROUP BY `tag_id` ORDER BY name LIMIT {$limit}");
        $s = '';
        foreach ($result AS $row) {
            $s .= "{$row['name']}|{$row['id']}\n";
        }
    }
}