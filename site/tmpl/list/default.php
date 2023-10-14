<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_noklist
 *
 * @copyright   Copyright (c) 2023 Norbert Kümin. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; // no direct access

use Joomla\CMS\Factory;

function getRecord($colHeader, $colTypes) {
	$record = array();
	foreach ($colHeader as $key => $col) {
    	$value = '';
		switch (strtolower($colTypes[$col])) {
		    case 'email';
		    case 'htmlarea':
		    case 'text':
		    case 'url':
    			$value = Factory::getApplication()->getInput()->get('col_'.$key, $value, 'String');
    		    break;
		    default:
    			$value = Factory::getApplication()->getInput()->get('col_'.$key, $value);
    		    break;
		}
		if (is_array($value)) {
			$record[$key] = implode(',',$value);
		} else {
			$record[$key] = $value;
		}
	}
	return $record;
}

$task = Factory::getApplication()->getInput()->get('task');
switch ($task) {
	case 'new':
	case 'edit':
		echo $this->loadTemplate('edit');
		break;
	case 'save':
		if ($this->canChange()) {
            $id = Factory::getApplication()->getInput()->get('id');
			$this->getModel()->saveData($id,getRecord($this->getModel()->colHeaders, $this->getModel()->colTypes));
		}
		echo $this->loadTemplate('detail');
		break;
	case 'delete':
		if ($this->canChange()) {
            $id = Factory::getApplication()->getInput()->get('id');
			$this->getModel()->delete($id);
		}
		echo $this->loadTemplate('list');
		break;
	case 'csv_export':
		if ($this->paramsMenu->get('allow_csv_export') == '1') {
			$input = JFactory::getApplication()->input;
			$this->getModel()->exportCsvData($input->get('export_encoding'));
		} else {
			echo $this->loadTemplate('list');
		}
		break;
	case 'json_export':
		if ($this->paramsMenu->get('allow_json_export') == '1') {
			$this->getModel()->exportJsonData();
		} else {
			echo $this->loadTemplate('list');
		}
		break;
	case 'import':
		if ($this->paramsMenu->get('allow_import') == '1') {
			$input = JFactory::getApplication()->input;
			$this->getModel()->importCsvData($input->files->get('import_file'), $input->get('import_encoding'));
		}
		echo $this->loadTemplate('list');
		break;
	case 'moveup':
        $key = Factory::getApplication()->getInput()->get('id');
		$this->getModel()->exchangeRecords($key, $key-1);
		echo $this->loadTemplate('list');
		break;
	case 'movedown':
        $key = Factory::getApplication()->getInput()->get('id');
		$this->getModel()->exchangeRecords($key, $key+1);
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
