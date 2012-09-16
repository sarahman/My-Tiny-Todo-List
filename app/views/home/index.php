<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->config->item('site_title') ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url($this->config->item('template_url') . '/' . $this->config->item('template')) ?>/style.css?v=1.4.2" media="all"/>
    <?php if ($this->config->item('is_rtl')) : ?>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url($this->config->item('template_url') . '/' . $this->config->item('template')) ?>/style_rtl.css?v=1.4.2" media="all"/>
    <?php endif ?>
    <?php if (isset($_GET['pda'])): ?>
    <meta name="viewport" id="viewport" content="width=device-width"/>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url($this->config->item('template_url') . '/' . $this->config->item('template')) ?>/pda.css?v=1.4.2" media="all"/>
    <?php else: ?>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url($this->config->item('template_url') . '/' . $this->config->item('template')) ?>/print.css?v=1.4.2" media="print"/>
    <?php endif ?>
</head>

<body>

<script type="text/javascript" src="<?php echo site_url('assets/js/jquery-1.4.4.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-1.8.7.custom.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/jquery.autocomplete-1.1.js') ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/mytinytodo.js?v=1.4.2') ?>"></script>
<!--<script type="text/javascript" src="--><?php //echo site_url('assets/js/') ?><!--mytinytodo_lang.php?v=1.4.2"></script>-->
<script type="text/javascript" src="<?php echo site_url('assets/js/mytinytodo_ajax_storage.js?v=1.4.2') ?>"></script>

<script type="text/javascript">
    $().ready(function () {

        <?php if (isset($_GET['pda'])) : ?>

        $('body').width(screen.width);
        $(window).resize(function () {
            $('body').width(screen.width);
        });

        <?php endif ?>

        mytinytodo.mttUrl = "<?php echo site_url() ?>";
        $.get(mytinytodo.mttUrl+'home/getJSData');
        mytinytodo.templateUrl = "<?php echo site_url($this->config->item('template_url') . '/' . $this->config->item('template')) . '/' ?>";
        mytinytodo.db = new mytinytodoStorageAjax(mytinytodo);
        mytinytodo.init({
            needAuth: <?php echo $this->config->item('needAuth') ? "true" : "false" ?>,
            isLogged: <?php echo ($this->config->item('needAuth') && $this->session->userdata('username')) ? "true" : "false" ?>,
            showdate: <?php echo ($this->config->item('showdate') && !isset($_GET['pda'])) ? "true" : "false" ?>,
            singletab: <?php echo (isset($_GET['singletab']) || isset($_GET['pda'])) ? "true" : "false" ?>,
            duedatepickerformat:"<?php echo htmlspecialchars($this->config->item('dateformat2')) ?>",
            firstdayofweek: <?php echo (int)$this->config->item('firstdayofweek') ?>,
            autotag: <?php echo $this->config->item('autotag') ? "true" : "false" ?>
        <?php if (isset($_GET['list'])) echo ",openList: " . (int)$_GET['list'] ?>
        <?php if (isset($_GET['pda'])) echo ", touchDevice: true" ?>
        }).loadLists(1);
    });
</script>

<div id="wrapper">
<div id="container">
<div id="mtt_body">

<h2><?php echo $this->config->item('site_title') ?></h2>

<div id="loading"></div>

<div id="bar">
    <div id="msg"><span class="msg-text"></span>

        <div class="msg-details"></div>
    </div>
    <div class="bar-menu">
 <span class="menu-owner" style="display:none">
   <a href="#settings" id="settings"><?php echo lang('a_settings') ?></a>
 </span>
        <span class="bar-delim" style="display:none"> | </span>
 <span id="bar_auth">
  <span id="bar_public" style="display:none"><?php echo lang('public_tasks') ?> |</span>
  <a href="#login" id="bar_login" class="nodecor"><u><?php echo lang('a_login') ?></u> <span class="arrdown"></span></a>
  <a href="#logout" id="bar_logout"><?php echo lang('a_logout') ?></a>
 </span>
    </div>
</div>

<br clear="all"/>

<div id="page_tasks" style="display:none">

    <div id="lists">
        <ul class="mtt-tabs"></ul>
        <div class="mtt-tabs-add-button" title="<?php echo lang('list_new') ?>"><span></span></div>
        <div id="tabs_buttons">
            <div class="mtt-tabs-select-button mtt-tabs-button" title="<?php echo lang('list_select') ?>"><span></span></div>
        </div>
        <div id="list_all" class="mtt-tab mtt-tabs-alltasks mtt-tabs-hidden"><a
            href="#alltasks"><span><?php echo lang('alltasks') ?></span>

            <div class="list-action"></div>
        </a></div>
    </div>


    <div id="toolbar" class="mtt-htabs">

        <div id="htab_search">
            <table class="mtt-searchbox">
                <tr>
                    <td>
                        <div class="mtt-searchbox-c">
                            <input type="text" name="search" value="" maxlength="250" id="search" autocomplete="off"/>

                            <div class="mtt-searchbox-icon mtt-icon-search"></div>
                            <div id="search_close" class="mtt-searchbox-icon mtt-icon-cancelsearch"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="htab_newtask">
            <table class="mtt-taskbox">
                <tr>
                    <td class="mtt-tb-cell">
                        <div class="mtt-tb-c">
                            <form id="newtask_form" method="post">
                                <label id="task_placeholder" class="placeholding" for="task">
                                    <input type="text" name="task" value="" maxlength="250" id="task"
                                           autocomplete="off"/>
                                    <span><?php echo lang('htab_newtask') ?></span>
                                </label>

                                <div id="newtask_submit" class="mtt-taskbox-icon mtt-icon-submittask"
                                     title="<?php echo lang('btn_add') ?>"></div>
                            </form>
                        </div>
                    </td>
                    <td><a href="#" id="newtask_adv" class="mtt-img-button"
                           title="<?php echo lang('advanced_add') ?>"><span></span></a></td>
                </tr>
            </table>
        </div>

        <div id="searchbar" style="display:none"><?php echo lang('searching') ?> <span id="searchbarkeyword"></span></div>

        <div style="clear:both"></div>

    </div>


    <h3>
        <span id="taskview" class="mtt-menu-button"><span class="btnstr"><?php echo lang('tasks') ?></span> (<span
            id="total">0</span>) <span class="arrdown"></span></span>
        <span class="mtt-notes-showhide"><?php echo lang('notes') ?> <a href="#"
                                                                 id="mtt-notes-show"><?php echo lang('notes_show') ?></a> / <a
            href="#" id="mtt-notes-hide"><?php echo lang('notes_hide') ?></a></span>
        <span id="mtt_filters"></span>
        <span id="tagcloudbtn" class="mtt-menu-button"><?php echo lang('tagcloud') ?> <span class="arrdown2"></span></span>
    </h3>

    <div id="taskcontainer">
        <ol id="tasklist" class="sortable"></ol>
    </div>

</div>
<!-- end of page_tasks -->


<div id="page_taskedit" style="display:none">

    <div><a href="#" class="mtt-back-button"><?php echo lang('go_back') ?></a></div>

    <h3 class="mtt-inadd"><?php echo lang('add_task') ?></h3>

    <h3 class="mtt-inedit"><?php echo lang('edit_task') ?>
        <div id="taskedit-date" class="mtt-inedit">
            (<span class="date-created" title="<?php echo lang('taskdate_created') ?>"><span></span></span><span
            class="date-completed" title="<?php echo lang('taskdate_completed') ?>"> &mdash; <span></span></span>)
        </div>
    </h3>

    <form id="taskedit_form" name="edittask" method="post">
        <input type="hidden" name="isadd" value="0"/>
        <input type="hidden" name="id" value=""/>

        <div class="form-row form-row-short">
            <span class="h"><?php echo lang('priority') ?></span>
            <select name="prio">
                <option value="2">+2</option>
                <option value="1">+1</option>
                <option value="0" selected="selected">&plusmn;0</option>
                <option value="-1">&minus;1</option>
            </select>
        </div>
        <div class="form-row form-row-short">
            <span class="h"><?php echo lang('due') ?> </span>
            <input name="duedate" id="duedate" value="" class="in100" title="Y-M-D, M/D/Y, D.M.Y, M/D, D.M"
                   autocomplete="off"/>
        </div>
        <div class="form-row-short-end"></div>
        <div class="form-row">
            <div class="h"><?php echo lang('task') ?></div>
            <input type="text" name="task" value="" class="in500" maxlength="250"/></div>
        <div class="form-row">
            <div class="h"><?php echo lang('note') ?></div>
            <textarea name="note" class="in500"></textarea></div>
        <div class="form-row">
            <div class="h"><?php echo lang('tags') ?></div>
            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td><input type="text" name="tags" id="edittags" value="" class="in500" maxlength="250"/></td>
                    <td class="alltags-cell">
                        <a href="#" id="alltags_show"><?php echo lang('alltags_show') ?></a>
                        <a href="#" id="alltags_hide" style="display:none"><?php echo lang('alltags_hide') ?></a></td>
                </tr>
            </table>
        </div>
        <div class="form-row" id="alltags" style="display:none;"><?php echo lang('alltags') ?> <span class="tags-list"></span>
        </div>
        <div class="form-row form-bottom-buttons">
            <input type="submit" value="<?php echo lang('save') ?>"/>
            <input type="button" id="mtt_edit_cancel" class="mtt-back-button" value="<?php echo lang('cancel') ?>"/>
        </div>
    </form>

</div>
<!-- end of page_taskedit -->


<div id="authform" style="display:none">
    <form id="login_form">
        <div class="h"><?php echo lang('password') ?></div>
        <div><input type="password" name="password" id="password"/></div>
        <div><input type="submit" value="<?php echo lang('btn_login') ?>"/></div>
    </form>
</div>

<div id="priopopup" style="display:none">
    <span class="prio-neg prio-neg-1">&minus;1</span>
    <span class="prio-zero">&plusmn;0</span>
    <span class="prio-pos prio-pos-1">+1</span>
    <span class="prio-pos prio-pos-2">+2</span>
</div>

<div id="taskviewcontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li id="view_tasks"><?php echo lang('tasks') ?> (<span id="cnt_total">0</span>)</li>
        <li id="view_past"><?php echo lang('f_past') ?> (<span id="cnt_past">0</span>)</li>
        <li id="view_today"><?php echo lang('f_today') ?> (<span id="cnt_today">0</span>)</li>
        <li id="view_soon"><?php echo lang('f_soon') ?> (<span id="cnt_soon">0</span>)</li>
    </ul>
</div>

<div id="tagcloud" style="display:none">
    <a id="tagcloudcancel" class="mtt-img-button"><span></span></a>

    <div id="tagcloudload"></div>
    <div id="tagcloudcontent"></div>
</div>


<div id="listmenucontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li class="mtt-need-list mtt-need-real-list" id="btnRenameList"><?php echo lang('list_rename') ?></li>
        <li class="mtt-need-list mtt-need-real-list" id="btnDeleteList"><?php echo lang('list_delete') ?></li>
        <li class="mtt-need-list mtt-need-real-list" id="btnClearCompleted"><?php echo lang('list_clearcompleted') ?></li>
        <li class="mtt-need-list mtt-need-real-list mtt-menu-indicator" submenu="listexportmenucontainer">
            <div class="submenu-icon"></div><?php echo lang('list_export') ?></li>
        <li class="mtt-menu-delimiter mtt-need-real-list"></li>
        <li class="mtt-need-list mtt-need-real-list" id="btnPublish">
            <div class="menu-icon"></div><?php echo lang('list_publish') ?></li>
        <li class="mtt-need-list mtt-need-real-list" id="btnRssFeed">
            <div class="menu-icon"></div><?php echo lang('list_rssfeed') ?></li>
        <li class="mtt-menu-delimiter mtt-need-real-list"></li>
        <li class="mtt-need-list mtt-need-real-list sort-item" id="sortByHand">
            <div class="menu-icon"></div><?php echo lang('sortByHand') ?> <span class="mtt-sort-direction"></span></li>
        <li class="mtt-need-list sort-item" id="sortByDateCreated">
            <div class="menu-icon"></div><?php echo lang('sortByDateCreated') ?> <span class="mtt-sort-direction"></span></li>
        <li class="mtt-need-list sort-item" id="sortByPrio">
            <div class="menu-icon"></div><?php echo lang('sortByPriority') ?> <span class="mtt-sort-direction"></span></li>
        <li class="mtt-need-list sort-item" id="sortByDueDate">
            <div class="menu-icon"></div><?php echo lang('sortByDueDate') ?> <span class="mtt-sort-direction"></span></li>
        <li class="mtt-need-list sort-item" id="sortByDateModified">
            <div class="menu-icon"></div><?php echo lang('sortByDateModified') ?> <span class="mtt-sort-direction"></span></li>
        <li class="mtt-menu-delimiter"></li>
        <li class="mtt-need-list" id="btnShowCompleted">
            <div class="menu-icon"></div><?php echo lang('list_showcompleted') ?></li>
    </ul>
</div>

<div id="listexportmenucontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li class="mtt-need-list mtt-need-real-list" id="btnExportCSV"><?php echo lang('list_export_csv') ?></li>
        <li class="mtt-need-list mtt-need-real-list" id="btnExportICAL"><?php echo lang('list_export_ical') ?></li>
    </ul>
</div>

<div id="taskcontextcontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li id="cmenu_edit"><b><?php echo lang('action_edit') ?></b></li>
        <li id="cmenu_note"><?php echo lang('action_note') ?></li>
        <li id="cmenu_prio" class="mtt-menu-indicator" submenu="cmenupriocontainer">
            <div class="submenu-icon"></div><?php echo lang('action_priority') ?></li>
        <li id="cmenu_move" class="mtt-menu-indicator" submenu="cmenulistscontainer">
            <div class="submenu-icon"></div><?php echo lang('action_move') ?></li>
        <li id="cmenu_delete"><?php echo lang('action_delete') ?></li>
    </ul>
</div>

<div id="cmenupriocontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li id="cmenu_prio:2">
            <div class="menu-icon"></div>
            +2
        </li>
        <li id="cmenu_prio:1">
            <div class="menu-icon"></div>
            +1
        </li>
        <li id="cmenu_prio:0">
            <div class="menu-icon"></div>&plusmn;0
        </li>
        <li id="cmenu_prio:-1">
            <div class="menu-icon"></div>&minus;1
        </li>
    </ul>
</div>

<div id="cmenulistscontainer" class="mtt-menu-container" style="display:none">
    <ul>
    </ul>
</div>

<div id="slmenucontainer" class="mtt-menu-container" style="display:none">
    <ul>
        <li id="slmenu_list:-1"
            class="list-id--1 mtt-need-list" <?php if (is_readonly()) echo 'style="display:none"' ?>>
            <div class="menu-icon"></div>
            <a href="#alltasks"><?php echo lang('alltasks') ?></a></li>
        <li class="mtt-menu-delimiter slmenu-lists-begin mtt-need-list" <?php if (is_readonly()) echo 'style="display:none"' ?>></li>
    </ul>
</div>

<div id="page_ajax" style="display:none"></div>

</div>
<div id="space"></div>
</div>

<div id="footer">
    <div id="footer_content">Powered by <strong><a href="http://www.mytinytodo.net/">myTinyTodo</a></strong> 1.4.2</div>
</div>

</div>
</body>
</html>
<!-- r387 -->