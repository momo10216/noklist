<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	NoK-List
* @copyright	Copyright (c) 2017 Norbert Kümin. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/
defined('_JEXEC') or die; // no direct access

function getRecord() {
	$record = array();
	foreach ($this->colHeader as $key => $col) {
		$value = JRequest::getVar('col_'.$key);
		$record[$key] = $value;
	}
	return $record;
}

$task = JRequest::getVar('task');
switch ($task) {
	case 'new':
	case 'edit':
		$uri = JFactory::getURI();
		$id = $uri->getVar('id');
		$record = getRecord();
		//$this->saveData($id,$record)
		echo $this->loadTemplate('edit');
		break;
	case 'save':
		echo $this->loadTemplate('list');
		break;
	case 'delete':
		echo $this->loadTemplate('list');
		break;
	case 'list':
	default:
		echo $this->loadTemplate('list');
		break;
}


?>