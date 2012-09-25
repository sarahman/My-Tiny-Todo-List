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

    public function getTagCloud(array $data)
    {
        $sql = "SELECT `name`, `tag_id`, COUNT(`tag_id`) AS `tags_count` FROM `{$this->db->dbprefix('tag2task')}`
                INNER JOIN `{$this->db->dbprefix('tags')}` ON `tag_id`=`id`
                WHERE `list_id`='{$data['list']}' GROUP BY (`tag_id`) ORDER BY `tags_count` ASC";

        $result = $this->executeQuery($sql);
        $at = $ac = array();
        foreach ($result AS $row) {
            $at[] = array('name' => $row['name'], 'id' => $row['tag_id']);
            $ac[] = $row['tags_count'];
        }

        $tagCloud = array('total' => 0);
        $count = count($at);
        if (empty ($count)) {
            return ($tagCloud);
        }

        $qmax = max($ac);
        $qmin = min($ac);
        $grades = ($count >= 10) ? 10 : $count;
        $step = ($qmax - $qmin)/$grades;

        $CI = & get_instance();
        $CI->load->helper('array');

        foreach($at as $i=>$tag) {
            $tagCloud['cloud'][] = array(
                'tag'=>htmlarray($tag['name']),
                'id'=>(int)$tag['id'],
                'w'=> $this->getTagSize($qmin,$ac[$i],$step)
            );
        }

        $tagCloud['total'] = $count;
        return $tagCloud;
    }

    private function getTagSize($qmin, $q, $step)
    {
        if ($step == 0) return 1;
        $v = ceil(($q - $qmin)/$step);
        if($v == 0) return 0;
        else return $v-1;

    }
}