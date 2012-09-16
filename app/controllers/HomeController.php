<?php

/**
 * Home Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class HomeController extends BaseController
{
    public function index()
    {
        $this->load->view('home/index', $this->data);
    }

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
        if($listId == -1) {
            $userLists = $this->tinylist->getUserListsSimple();
            $sqlWhere .= " AND {$dbPrefix}todolist.list_id IN (". implode(array_keys($userLists), ','). ") ";
        }
        else $sqlWhere .= " AND {$dbPrefix}todolist.list_id=". $listId;
        if ($getData['compl'] == 0) $sqlWhere .= ' AND compl=0';

        $tag = empty($getData['t']) ? '' : $getData['t'];
        if($tag != '')
        {
            $at = explode(',', $tag);
            $tagIds = array();
            $tagExIds = array();
            foreach($at as $atv) {
                $atv = trim($atv);
                if($atv == '' || $atv == '^') continue;
                if(substr($atv,0,1) == '^') {
                    $tagExIds[] = $this->tag->getTagId(substr($atv,1));
                } else {
                    $tagIds[] = $this->tag->getTagId($atv);
                }
            }

            if(sizeof($tagIds) > 1) {
                $inner .= "INNER JOIN (SELECT task_id, COUNT(tag_id) AS c FROM {$dbPrefix}tag2task WHERE list_id=$listId AND tag_id IN (".
                            implode(',',$tagIds). ") GROUP BY task_id) AS t2t ON id=t2t.task_id";
                $sqlWhere = " AND c=". sizeof($tagIds); //overwrite sqlWhere!
            }
            elseif($tagIds) {
                $inner .= "INNER JOIN {$dbPrefix}tag2task ON id=task_id";
                $sqlWhere .= " AND tag_id = ". $tagIds[0];
            }

            if($tagExIds) {
                $sqlWhere .= " AND id NOT IN (SELECT DISTINCT task_id FROM {$dbPrefix}tag2task WHERE list_id=$listId AND tag_id IN (".
                            implode(',',$tagExIds). "))"; //DISTINCT ?
            }
        }

        $this->load->helper('database_helper');
        $s = $this->uri->segment('s');
        if($s != '') $sqlWhere .= " AND (title LIKE ". quoteForLike("%%%s%%",$s). " OR note LIKE ". quoteForLike("%%%s%%",$s). ")";
        $sort = (int)$this->uri->segment('sort');
        $sqlSort = "ORDER BY compl ASC, ";
        if($sort == 1) $sqlSort .= "prio DESC, ddn ASC, duedate ASC, ow ASC";		// byPrio
        elseif($sort == 101) $sqlSort .= "prio ASC, ddn DESC, duedate DESC, ow DESC";	// byPrio (reverse)
        elseif($sort == 2) $sqlSort .= "ddn ASC, duedate ASC, prio DESC, ow ASC";	// byDueDate
        elseif($sort == 102) $sqlSort .= "ddn DESC, duedate DESC, prio ASC, ow DESC";// byDueDate (reverse)
        elseif($sort == 3) $sqlSort .= "d_created ASC, prio DESC, ow ASC";			// byDateCreated
        elseif($sort == 103) $sqlSort .= "d_created DESC, prio ASC, ow DESC";		// byDateCreated (reverse)
        elseif($sort == 4) $sqlSort .= "d_edited ASC, prio DESC, ow ASC";			// byDateModified
        elseif($sort == 104) $sqlSort .= "d_edited DESC, prio ASC, ow DESC";		// byDateModified (reverse)
        else $sqlSort .= "ow ASC";

        $t = array();
        $t['total'] = 0;
        $t['list'] = array();
        $q = $this->todolist->executeQuery("SELECT *, duedate IS NULL AS ddn FROM {$dbPrefix}todolist $inner WHERE 1=1 $sqlWhere $sqlSort");

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

    public function getJSData()
    {
        header('Content-type: text/javascript; charset=utf-8');
        echo "mytinytodo.lang.init(". $this->lang->makeJS() .");";
    }
}