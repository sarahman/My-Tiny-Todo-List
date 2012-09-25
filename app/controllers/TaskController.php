<?php

/**
 * Task Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class TaskController extends BaseController
{
    public function loadTasks()
    {
        $getData = $this->uri->uri_to_assoc();
        $this->load->model('tinylist');
        $this->load->model('tag');
        $this->load->model('todolist');
        $this->checkReadAccess($getData['list'], $this->tinylist);

        $sqlWhere = $inner = '';
        $listId = $getData['list'];
        $dbPrefix = $this->db->dbprefix;
        if ($listId == -1) {
            $userLists = $this->tinylist->getUserListsSimple();
            $sqlWhere .= " AND {$dbPrefix}todolist.list_id IN (". implode(array_keys($userLists), ','). ") ";
        }
        else $sqlWhere .= " AND {$dbPrefix}todolist.list_id={$listId}";
        if ($getData['compl'] == 0) $sqlWhere .= ' AND compl=0';

        $tag = empty($getData['t']) ? '' : $getData['t'];
        if (!empty($tag)) {
            $at = explode(',', $tag);
            $tagIds = array();
            $tagExIds = array();
            foreach ($at as $atv) {
                $atv = trim($atv);
                if ($atv == '' || $atv == '^') continue;
                if (substr($atv,0,1) == '^') {
                    $tagExIds[] = $this->tag->getTagId(substr($atv,1));
                } else {
                    $tagIds[] = $this->tag->getTagId($atv);
                }
            }

            if (sizeof($tagIds) > 1) {
                $inner .= "INNER JOIN (SELECT task_id, COUNT(tag_id) AS c FROM {$dbPrefix}tag2task WHERE list_id={$listId} AND tag_id IN (".
                            implode(',',$tagIds). ") GROUP BY task_id) AS t2t ON id=t2t.task_id";
                $sqlWhere = " AND c=". sizeof($tagIds); //overwrite sqlWhere!
            }
            elseif (!empty($tagIds)) {
                $inner .= "INNER JOIN {$dbPrefix}tag2task ON id=task_id";
                $sqlWhere .= " AND tag_id = ". $tagIds[0];
            }

            if (!empty($tagExIds)) {
                $sqlWhere .= " AND id NOT IN (SELECT DISTINCT task_id FROM {$dbPrefix}tag2task WHERE list_id={$listId} AND tag_id IN (".
                            implode(',',$tagExIds). "))"; //DISTINCT ?
            }
        }

        $this->load->helper('database');
        $s = $this->uri->segment('s');
        if ($s != '') $sqlWhere .= " AND (title LIKE ". quoteForLike("%%%s%%",$s). " OR note LIKE ". quoteForLike("%%%s%%",$s). ")";
        $sort = (int)$this->uri->segment('sort');
        $sqlSort = "ORDER BY compl ASC, ";
        switch ($sort) {
            case 1:		// byPrio
                $sqlSort .= "prio DESC, ddn ASC, duedate ASC, ow ASC"; break;

            case 2:		// byDueDate
                $sqlSort .= "ddn ASC, duedate ASC, prio DESC, ow ASC"; break;

            case 3:		// byDateCreated
                $sqlSort .= "d_created ASC, prio DESC, ow ASC"; break;

            case 4:		// byDateModified
                $sqlSort .= "d_edited ASC, prio DESC, ow ASC"; break;

            case 101:	// byPrio (reverse)
                $sqlSort .= "prio ASC, ddn DESC, duedate DESC, ow DESC"; break;

            case 102:	// byDueDate (reverse)
                $sqlSort .= "ddn DESC, duedate DESC, prio ASC, ow DESC"; break;

            case 103:	// byDateCreated (reverse)
                $sqlSort .= "d_created DESC, prio ASC, ow DESC"; break;

            case 104:	// byDateModified (reverse)
                $sqlSort .= "d_edited DESC, prio ASC, ow DESC"; break;

            default:
                $sqlSort .= "ow ASC"; break;
        }

        $q = $this->todolist->executeQuery(
            "SELECT *, `duedate` IS NULL AS ddn FROM {$dbPrefix}todolist {$inner} WHERE 1=1 {$sqlWhere} {$sqlSort}");

        $t = array('total' => 0, 'list' => array());
        foreach ($q AS $r) {
            $t['total']++;
            $t['list'][] = $this->todolist->prepareTaskRow($r);
        }
        if ($this->uri->segment('setCompl') && $this->haveWriteAccess($listId, $this->tinylist)) {
            $bitwise = ($this->uri->segment('compl') == 0) ? 'taskview & ~1' : 'taskview | 1';
            $this->tinylist->update(array('taskview' => $bitwise), $listId);
        }
        $this->jsonExit($t);
    }

    public function add()
    {
        $this->dealsWithAdding();
    }

    public function addFully()
    {
        $this->dealsWithAdding(true);
    }

    private function dealsWithAdding($fully = false)
    {
        $this->load->model('tinylist');
        $this->load->model('todolist');
        $listId = $this->input->post('list');
        $this->checkWriteAccess($listId, $this->tinylist);
        $taskId = empty($fully) ? $this->todolist->add($_POST) : $this->todolist->addFully($_POST);
        if (empty($taskId)) {
            $this->jsonExit(array('total' => 0));
        }
        $result = $this->todolist->get(array('id' => $taskId));
        $this->jsonExit($result);
    }

    public function edit()
    {
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $taskId = $this->todolist->edit($_POST);
        if (empty($taskId)) {
            $this->jsonExit(array('total' => 0));
        }
        $result = $this->todolist->get(array('id' => $taskId));
        $this->jsonExit($result);
    }

    public function editNote()
    {
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $taskId = $this->todolist->updateNote($_POST);
        $t = array();
        $t['total'] = 1;
        $t['list'][] = array(
            'id' => $taskId,
            'note' => nl2br(escapeTags($this->input->post('note'))),
            'noteText'=>(string)$this->input->post('note'));

        $this->jsonExit($t);
    }

    public function setPriority()
    {
        $data = $this->uri->uri_to_assoc();
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $result = $this->todolist->updatePriority($data);
        $t = array();
        $t['total'] = 1;
        $t['list'][] = array(
            'id' => $result['taskId'],
            'prio' => $result['prio']);

        $this->jsonExit($t);
    }

    public function delete()
    {
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $deleted = $this->todolist->delete($_POST);

        $t = array();
        $t['total'] = $deleted;
        $t['list'][] = array('id' => $this->input->post('id'));
        $this->jsonExit($t);
    }

    public function complete()
    {
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $taskId = $this->todolist->completeTask($_POST);
        $result = $this->todolist->get(array('id' =>$taskId));
        $this->jsonExit($result);
    }

    public function clearCompleted()
    {
        $this->checkWriteAccess();
        $this->load->model('todolist');
        $this->load->model('tinylist');
        $this->todolist->deleteCompletedTask($_POST);
        $result = $this->tinylist->get();
        $this->jsonExit($result);
    }

    public function suggestTags()
    {
        $this->load->model('tinylist');
        $this->load->model('tag');
        $this->checkReadAccess($this->input->get('list'), $this->tinylist);
        $suggestion = $this->tag->getTagSuggestion($_GET);
        $this->load->helper('array');
        echo htmlarray($suggestion);
        exit;
    }
}