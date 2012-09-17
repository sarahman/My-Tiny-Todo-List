<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

$config['site_title']   = 'My Tiny Todolist'; //Site Title Goes Here
$config['site_main']    = ''; //Site Main Url Goes Here

$config['adminEmail']   = ''; //Admin Email Address
$config['infoEmail']    = ''; // Info Email Address
$config['infoName']     = ''; // Info Name

$config['template_url'] = '/assets/themes'; // Info Name
$config['is_rtl'] = false;
$config['autotag'] = 1;
$config['duedateformat'] = 1;
$config['firstdayofweek'] = 1;
$config['session'] = 'files';
$config['clock'] = 24;
$config['dateformat'] = 'j M Y';
$config['dateformat2'] = 'n/j/y';
$config['dateformatshort'] = 'j M';
$config['template'] = 'default';
$config['showdate'] = 0;
$config['needAuth'] = false;

$config['params']['prefix'] = array('default'=>'', 'type'=>'s');
$config['params']['url'] = array('default'=>'', 'type'=>'s');
$config['params']['mtt_url'] = array('default'=>'', 'type'=>'s');
$config['params']['title'] = array('default'=>'', 'type'=>'s');
$config['params']['lang'] = array('default'=>'en', 'type'=>'s');
$config['params']['password'] = array('default'=>'', 'type'=>'s');
$config['params']['smartsyntax'] = array('default'=>1, 'type'=>'i');
$config['params']['timezone'] = array('default'=>'UTC', 'type'=>'s');
$config['params']['autotag'] = array('default'=>1, 'type'=>'i');
$config['params']['duedateformat'] = array('default'=>1, 'type'=>'i');
$config['params']['firstdayofweek'] = array('default'=>1, 'type'=>'i');
$config['params']['session'] = array('default'=>'files', 'type'=>'s', 'options'=>array('files','default'));
$config['params']['clock'] = array('default'=>24, 'type'=>'i', 'options'=>array(12,24));
$config['params']['dateformat'] = array('default'=>'j M Y', 'type'=>'s');
$config['params']['dateformat2'] = array('default'=>'n/j/y', 'type'=>'s');
$config['params']['dateformatshort'] = array('default'=>'j M', 'type'=>'s');
$config['params']['template'] = array('default'=>'default', 'type'=>'s');
$config['params']['showdate'] = array('default'=>0, 'type'=>'i');