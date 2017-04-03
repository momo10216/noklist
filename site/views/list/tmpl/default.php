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

function getRecord($colHeader, $colTypes) {
	$record = array();
	foreach ($colHeader as $key => $col) {
		if (strtolower($colTypes[$col]) == 'htmlarea') {
			$value = JRequest::getVar('col_'.$key, '', 'post', 'string', JREQUEST_ALLOWRAW);
		} else {
			$value = JRequest::getVar('col_'.$key);
		}
		if (is_array($value)) {
			$record[$key] = implode(',',$value);
		} else {
			$record[$key] = $value;
		}
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
			$this->saveData(JFactory::getURI()->getVar('id'),getRecord($this->colHeaders, $this->colTypes));
		}
		echo $this->loadTemplate('detail');
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
	case 'detail':
		echo $this->loadTemplate('detail');
		break;
	case 'list':
	default:
		echo $this->loadTemplate('list');
		break;
}
?>