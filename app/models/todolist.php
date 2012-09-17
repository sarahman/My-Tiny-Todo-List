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
        $ow = $this->find(array('list_id' => $listId, 'compl' => $compl), 'MAX(ow) AS ow');
        $ow = $ow['ow'] + 1;
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
}