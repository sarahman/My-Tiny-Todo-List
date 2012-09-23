<?php

class ToDoList extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->loadTable('todolist');
    }

    public function get($conditions = array())
    {
        $result = $this->findAll($conditions, '*');

        $temp = array();
        $temp['total'] = 0;
        foreach($result AS $row) {
            $temp['total']++;
            $temp['list'][] = $this->prepareTaskRow($row);
        }

        return $temp;
    }

    public function prepareTaskRow($row)
    {
        $CI = &get_instance();
        $dueA = prepare_duedate($row['duedate']);
        $formatCreatedInline = $formatCompletedInline = $CI->config->item('dateformatshort');
        if(date('Y') != date('Y',$row['d_created'])) $formatCreatedInline = $CI->config->item('dateformat2');
        if($row['d_completed'] && date('Y') != date('Y',$row['d_completed'])) $formatCompletedInline = $CI->config->item('dateformat2');

        $dCreated = timestampToDatetime($row['d_created']);
        $dCompleted = $row['d_completed'] ? timestampToDatetime($row['d_completed']) : '';

        $CI->load->helper('html');
        return array(
            'id' => $row['id'],
            'title' => escapeTags($row['title']),
            'listId' => $row['list_id'],
            'date' => htmlarray($dCreated),
            'dateInt' => (int)$row['d_created'],
            'dateInline' => htmlarray(formatTime($formatCreatedInline, $row['d_created'])),
            'dateInlineTitle' => htmlarray(sprintf($CI->lang->line('taskdate_inline_created'), $dCreated)),
            'dateEditedInt' => (int)$row['d_edited'],
            'dateCompleted' => htmlarray($dCompleted),
            'dateCompletedInline' => $row['d_completed'] ? htmlarray(formatTime($formatCompletedInline, $row['d_completed'])) : '',
            'dateCompletedInlineTitle' => htmlarray(sprintf($CI->lang->line('taskdate_inline_completed'), $dCompleted)),
            'compl' => (int)$row['compl'],
            'prio' => $row['prio'],
            'note' => nl2br(escapeTags($row['note'])),
            'noteText' => (string)$row['note'],
            'ow' => (int)$row['ow'],
            'tags' => htmlarray($row['tags']),
            'tags_ids' => htmlarray($row['tags_ids']),
            'duedate' => $dueA['formatted'],
            'dueClass' => $dueA['class'],
            'dueStr' => htmlarray($row['compl'] && $dueA['timestamp'] ? formatTime($formatCompletedInline, $dueA['timestamp']) : $dueA['str']),
            'dueInt' => date2int($row['duedate']),
            'dueTitle' => htmlarray(sprintf($CI->lang->line('taskdate_inline_duedate'), $dueA['formatted'])),
        );
    }

    public function completeTask(array $data)
    {
        $taskId = $data['id'];
        $compl = isset($data['compl']) ? 1 : 0;
        $task = $this->find(array($this->primaryKey => $taskId), 'list_id');
        $listId = $task['list_id'];
        $ow = $this->getMaximumOW($listId, $compl);
        $dateCompleted = $compl ? time() : 0;
        return $this->update(array('compl' => $compl, 'ow' => $ow, 'd_completed' => $dateCompleted, 'd_edited' => time()), $taskId);
    }

    public function deleteCompletedTask(array $data)
    {
        $this->db->trans_start();
        $this->db->query("DELETE FROM {$this->db->dbprefix('tag2task')} WHERE `task_id` IN (
                            SELECT id FROM {$this->db->dbprefix('todolist')} WHERE `list_id` = ? and `compl`='1')", array($data['list']));
        $this->db->query("DELETE FROM {$this->db->dbprefix('todolist')} WHERE `list_id` = ? and `compl`='1'", array($data['list']));
        $this->db->trans_complete();
    }

    public function add(array $data)
    {
        $title = $data['title'];
        $listId = $data['list'];
        $prio = 0;
        $tags = '';
        if ($this->config->item('smartsyntax') != 0) {
            $a = $this->parseSmartSyntax($title);
            if (empty($a)) { return false; }
            $title = $a['title'];
            $prio = $a['prio'];
            $tags = $a['tags'];
        }
        if ($title == '') return false;
        if ($this->config->item('autotag')) $tags .= ','.$data['tag'];

        $CI = &get_instance();
        $CI->load->helper('database');
        $data['uuid'] = generateUUID();
        $data['title'] = $title;
        $data['list_id'] = $listId;
        $data['ow'] = $this->getMaximumOW($listId, 0);
        $data['prio'] = $prio;
        $data['d_created'] = $data['d_edited'] = time();
        $this->db->trans_start();
        $taskId = $this->insert($data);
        $this->dealsWithTags($tags, $listId, $taskId);
        $this->db->trans_complete();
        return $taskId;
    }

    public function addFully(array $data)
    {
        $title = $data['title'];
        if ($title == '') {
            return false;
        }

        $listId = $data['list'];

        $prio = (int)$data['prio'];
        if ($prio < -1) $prio = -1;
        elseif($prio > 2) $prio = 2;

        $tags = $data['tags'];
        $note = str_replace("\r\n", "\n", trim($data['note']));
        $duedate = parse_duedate(trim($data['duedate']));
        if ($this->config->item('autotag')) $tags .= ','.$data['tags'];

        $CI = &get_instance();
        $CI->load->helper('database');
        $data['uuid'] = generateUUID();
        $data['list_id'] = $listId;
        $data['title'] = $title;
        $data['note'] = $note;
        $data['ow'] = $this->getMaximumOW($listId, 0);
        $data['prio'] = $prio;
        $data['duedate'] = $duedate;
        $data['d_created'] = $data['d_edited'] = time();
        $this->db->trans_start();
        $taskId = $this->insert($data);
        $this->dealsWithTags($tags, $listId, $taskId);
        $this->db->trans_complete();
        return $taskId;
    }

    public function edit(array $data)
    {
        $taskId = (int)$data['id'];
        $title = trim($data['title']);
        $note = str_replace("\r\n", "\n", trim($data['note']));
        $prio = (int)$data['prio'];
        if ($prio < -1) $prio = -1;
        elseif ($prio > 2) $prio = 2;
        $duedate = parse_duedate(trim($data['duedate']));
        if (empty ($title)) {
            return false;
        }

        $list = $this->find(array($this->primaryKey => $taskId), 'list_id');
        $tags = trim($data['tags']);
        $this->db->trans_start();
	    $this->db->query("DELETE FROM {$this->db->dbprefix('tag2task')} WHERE `task_id`='{$taskId}'");
        $this->dealsWithTags($tags, $list['list_id'], $taskId, array(
            'title' => $title, 'note' => $note, 'prio' => $prio, 'duedate' => $duedate, 'd_edited' => time()
        ));
        $this->db->trans_complete();
        return $taskId;
    }

    private function dealsWithTags($tags, $listId, $taskId, array $dataToBeUpdated = array())
    {
        if ($tags == '') { return; }
        $aTags = $this->prepareTags($tags);
        if (empty($aTags)) { return; }
        $this->addTaskTags($taskId, $aTags['ids'], $listId);
        $this->update(array_merge($dataToBeUpdated, array(
            'tags' => implode(',',$aTags['tags']),
            'tags_ids' => implode(',',$aTags['ids']))
        ), $taskId);
    }

    protected function getMaximumOW($listId, $compl)
    {
        $ow = $this->find(array('list_id' => $listId, 'compl' => $compl), 'MAX(`ow`) AS ow');
        return $ow['ow'] + 1;
    }

    protected function parseSmartSyntax($title)
    {
        if (!preg_match("|^(/([+-]{0,1}\d+)?/)?(.*?)(\s+/([^/]*)/$)?$|", $title, $m)) {
            return false;
        }

        $a = array();
        $a['prio'] = isset($m[2]) ? (int)$m[2] : 0;
        $a['title'] = isset($m[3]) ? trim($m[3]) : '';
        $a['tags'] = isset($m[5]) ? trim($m[5]) : '';

        if ($a['prio'] < -1) $a['prio'] = -1;
        elseif ($a['prio'] > 2) $a['prio'] = 2;

        return $a;
    }

    private function prepareTags($tagsStr)
    {
        $tags = explode(',', $tagsStr);
        if(!$tags) return array();

        $aTags = array('tags'=>array(), 'ids'=>array());
        $CI = &get_instance();
        $CI->load->model('tag');
        foreach($tags AS $tag) {
            $tag = str_replace(array('"',"'",'<','>','&','/','\\','^'),'',trim($tag));
            if ($tag == '') continue;

            $aTag = $CI->tag->getOrCreateTag($tag);
            if($aTag && !in_array($aTag['id'], $aTags['ids'])) {
                $aTags['tags'][] = $aTag['name'];
                $aTags['ids'][] = $aTag['id'];
            }
        }

        return $aTags;
    }

    protected function addTaskTags($taskId, $tagIds, $listId)
    {
        if (empty($tagIds)) return;
        foreach($tagIds as $tagId) {
            $this->db->query("INSERT INTO {$this->db->dbprefix('tag2task')} (`task_id`, `tag_id`, `list_id`)
                                VALUES (?, ?, ?)", array($taskId, $tagId, $listId));
        }
    }
}