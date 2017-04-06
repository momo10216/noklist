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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
class NoKListViewList extends JViewLegacy {
	private $_delimiter = "\t";
	protected $items;
	protected $pageHeading = 'COM_NOKLIST_PAGE_TITLE_DEFAULT';
	protected $paramsMenuEntry;
	protected $user;
	protected $colPos = array();
	protected $colHeaders = array();
	protected $colTypes = array();
	protected $colParams = array();
	protected $file = '';
	protected $menuItemId = '';

	function display($tpl = null) {
		// Init variables
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->document = JFactory::getDocument();
		$this->form = $this->get('Form');
		// Get related menu entry
		$menuItemId = JRequest::getVar('menuitemid');
		$menu = $app->getMenu();
		if (is_object($menu)) {
			if (!empty($menuItemId)) {
				$currentMenu = $menu->getItem($menuItemId);
			} else {
				$currentMenu = $menu->getActive();
			}
		}
		// Related menu found
		if (is_object($currentMenu)) {
			$this->menuItemId = $currentMenu->id;
			if (is_object($currentMenu)) {
				$this->paramsMenuEntry = $currentMenu->params;
			}
		}
		// Read configs
		if (is_object($this->paramsMenuEntry)) {
			$this->_setColumnVars($this->paramsMenuEntry->get('columns'));
			$this->file = $this->paramsMenuEntry->get('file');
		}
		// Init document
		JFactory::getDocument()->setMetaData('robots', 'noindex, nofollow');
		parent::display($tpl);
	}

	function getData() {
		$data = array();
		if (!empty($this->file)) {
			if (file_exists($this->file)) {
				if (is_readable($this->file)) {
					$content = file_get_contents($this->file);
					JLoader::register('CvsHelper', __DIR__.'/../../helpers/cvs.php', true);
					$data  = CvsHelper::loadCVS($content, 'UTF-8', $this->_delimiter);
				} else {
					$this->_showError(JText::_('COM_NOKLIST_FILE_NOT_READABLE'));
				}
			}
		}
		return $data;
	}

	function getLink($task, $id='') {
		$uri = new JURI(JURI::Root().'/index.php');
		$uri->setVar('layout','default');
		$uri->setVar('view','list');
		$uri->setVar('option','com_noklist');
		$uri->setVar('task',$task);
		if ($this->menuItemId != '') {
			$uri->setVar('menuitemid',$this->menuItemId);
		}
		if ($id != '') {
			$uri->setVar('id',$id);
		}
		return $uri->toString();
	}

	function canChange() {
		if (is_object($this->paramsMenuEntry)) {
			if ($this->paramsMenuEntry->get('allow_change') == '1') {
				if (array_search($this->paramsMenuEntry->get('change_access'), $this->user->getAuthorisedViewLevels()) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	function saveData($id, $record) {
		$rows = $this->getData();
		$add = true;
		if (($id != '') && isset($rows[$id])) { $add = false; }
		// Set system related fields
		foreach($this->colHeaders as $key => $col) {
			if (isset($this->colTypes[$col])) {
				switch (strtolower($this->colTypes[$col])) {
					case 'createby':
						if ($add) { $record[$key] = $this->user->get('name'); }
						break;
					case 'createdate':
						if ($add) { $record[$key] = date('Y-m-d H:i:s'); }
						break;
					case 'updateby':
						$record[$key] = $this->user->get('name');
						break;
					case 'updatedate':
						$record[$key] = date('Y-m-d H:i:s');
						break;
					default:
						break;
				}
			}
		}
		if ($add) {
			array_push($rows,$record);
		} else {
			$rows[$id] = $record;
		}
		$this->_save($rows);
	}

	function deleteData($id) {
		$rows = $this->getData();
		if (isset($rows[$id])) { unset($rows[$id]); }
		$this->_save($rows);
	}

	function exportData($encoding) {
		$rows = $this->getData();
		$rows = $this->_array_insert_before('0', $rows, $this->colHeaders);
		JLoader::register('CvsHelper', __DIR__.'/../../helpers/cvs.php', true);
		CvsHelper::saveCVS($rows,$encoding,'export-'.date('Ymd').'.csv');
	}

	function importData($file, $encoding) {
		$content = '';
		if (isset($file['tmp_name'])) {
			$content = file_get_contents($file['tmp_name']);
			unlink($file['tmp_name']);
		}
		$content = str_replace("\r","\n",$content);
		$content = str_replace("\n\n","\n",$content);
		JLoader::register('CvsHelper', __DIR__.'/../../helpers/cvs.php', true);
		$rows = CvsHelper::loadCVS($content, $encoding);
		foreach($this->colHeaders as $fkey => $col) {
			if (isset($this->colTypes[$col])) {
				switch (strtolower($this->colTypes[$col])) {
					case 'createby':
						foreach($rows as $rkey => $row) {
							$row[$fkey] = $this->user->get('name');
							$rows[$rkey] = $row;
						}
						break;
					case 'createdate':
						foreach($rows as $rkey => $row) {
							$row[$fkey] = date('Y-m-d H:i:s');
							$rows[$rkey] = $row;
						}
						break;
					case 'updateby':
						foreach($rows as $rkey => $row) {
							$row[$fkey] = $this->user->get('name');
							$rows[$rkey] = $row;
						}
						break;
					case 'updatedate':
						foreach($rows as $rkey => $row) {
							$row[$fkey] = date('Y-m-d H:i:s');
							$rows[$rkey] = $row;
						}
						break;
					default:
						break;
				}
			}
		}
		$this->_save($rows);
	}

	function exchangeRecords($key1, $key2) {
		$rows = $this->getData();
		if (isset($rows[$key1]) & isset($rows[$key2])) {
			$record = $rows[$key1];
			$rows[$key1] = $rows[$key2];
			$rows[$key2] = $record;
		}
		$this->_save($rows);
	}

	function getDisplayValue($col, $value) {
		if (isset($this->colTypes[$col])) {
			switch (strtolower($this->colTypes[$col])) {
				case 'date':
					if (!empty($value)) { return JHTML::date($value,JText::_('DATE_FORMAT_LC4')); }
					break;
				case 'createdate':
				case 'updatedate':
					if (!empty($value)) { return JHTML::date($value,JText::_('DATE_FORMAT_LC5')); }
					break;
				case 'textarea':
					if (!empty($value)) { return '<pre>'.$value.'</pre>'; }
					break;
				default:
					break;
			}
		}
		return $value;
	}

	function getIndex($data, $column='', $direction='ASC') {
		$index = array();
		foreach($data as $key => $row) {
			if (empty($column)) {
				$index[$key] = $key;
			} else {
				$index[$row[$this->colPos[$column]]] = $key;
			}
		}
		if ($direction == 'ASC') {
			ksort($index);
		} else {
			krsort($index);
		}
		return $index;
	}

	private function _save($rows) {
		if (!empty($this->file)) {
			if (!file_exists($this->file) || is_writeable($this->file)) {
				JLoader::register('CvsHelper', __DIR__.'/../../helpers/cvs.php', true);
				$content = CvsHelper::array2cvs($rows, $this->_delimiter);
				file_put_contents($this->file, $content);
			} else {
				$this->_showError(JText::_('COM_NOKLIST_FILE_NOT_WRITEABLE'));
				return false;
			}
		}
		return true;
	}

	private function _array_insert_before($beforekey, array &$array, $new_value) {
		if (array_key_exists($beforekey, $array)) {
			$result = array();
			foreach ($array as $key => $value) {
				if ($key == $beforekey) {
					$result[] = $new_value;
				}
				$result[] = $value;
			}
			return $result;
		}
		return false;
	}

	private function _setColumnVars($config) {
		$config = str_replace("\r","\n",$config);
		$config = str_replace("\n\n","\n",$config);
		foreach(explode("\n",$config) as $entry) {
			// Defaulting
			$header = $entry;
			$type = 'text';
			$params = '';
			if (strpos($entry,'=') !== false) {
				list($header,$type) = explode('=',$entry,2);
			}
			if (strpos($type,'(') !== false) {
				list($type,$params) = explode('(',$type,2);
				$params = trim($params,')');
			}
			$this->colHeaders[] = $header;
			$this->colPos[$header] = array_search($header,$this->colHeaders);
			$this->colTypes[$header] = $type;
			if (!empty($params)) {
				$this->colParams[$header] = explode(',',$params);
			} else {
				$this->colParams[$header] = array();
			}
		}
	}

	private function _showError($text) {
		$application = JFactory::getApplication();
		$application->enqueueMessage($text, 'error');
	}
}
?>