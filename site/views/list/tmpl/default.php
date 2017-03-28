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

function getRecord($colHeader) {
	$record = array();
	foreach ($colHeader as $key => $col) {
		$value = JRequest::getVar('col_'.$key);
		$record[$key] = $value;
	}
	return $record;
}

$task = JRequest::getVar('task');
switch ($task) {
	case 'new':
	case 'edit':
		echo $this->loadTemplate('edit');
		break;
	case 'save':
		if ($this->canChange()) {
			$this->saveData(JFactory::getURI()->getVar('id'),getRecord($this->colHeader));
		}
		echo $this->loadTemplate('list');
		break;
	case 'delete':
		if ($this->canChange()) {
			$this->deleteData(JFactory::getURI()->getVar('id'));
		}
		echo $this->loadTemplate('list');
		break;
	case 'export':
		$this->exportData(JFactory::getURI()->getVar('export_encoding'));
		break;
	case 'moveup':
		$key = JFactory::getURI()->getVar('id');
		$this->exchangeRecords($key, $key-1);
		echo $this->loadTemplate('list');
		break;
	case 'movedown':
		$key = JFactory::getURI()->getVar('id');
		$this->exchangeRecords($key, $key+1);
		echo $this->loadTemplate('list');
		break;
	case 'list':
	default:
		echo $this->loadTemplate('list');
		break;
}
?>