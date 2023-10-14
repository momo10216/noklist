<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_noklist
 *
 * @copyright   Copyright (c) 2022 Norbert Kümin. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NKuemin\Component\NoKList\Site\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\ListModelInterface;
use NKuemin\Component\NoKList\Site\Helper\CsvHelper;
use NKuemin\Component\NoKList\Site\Helper\JsonHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of container records.
 *
 * @since  1.6
 */
class ListModel extends BaseModel implements ListModelInterface {
	private $_delimiter = "\t";
	private $file = '';
    public $colHeaders = array();
    public $colPos = array();
    public $colTypes = array();
	public $colParams = array();

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @see    \Joomla\CMS\MVC\Model\BaseDatabaseModel
     * @since   3.2
     */
    public function __construct($config = array(), MVCFactoryInterface $factory = null) {
        parent::__construct($config, $factory);
        $configs = $this->_getConfig();
		if (is_object($configs)) {
			$this->file = $configs->get('file');
		}
        $this->_setColData();
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getItems() {
		$data = array();
        if (!empty($this->file)) {
            if (file_exists($this->file) && is_readable($this->file)) {
                $content = file_get_contents($this->file);
                $data  = CsvHelper::load($content, 'UTF-8', $this->_delimiter);
            } else {
                throw new GenericDataException(Text::_('COM_NOKLIST_FILE_NOT_READABLE'), 500);
            }
		}
		return $data;
    }

	public function getIndex($data, $column='', $direction='ASC') {
		$index = array();
		foreach($data as $key => $row) {
			if (empty($column)) {
				$index[$key] = $key;
			} else {
				$index[$key] = $row[$this->colPos[$column]];
			}
		}
		if ($direction == 'ASC') {
			asort($index);
		} else {
			arsort($index);
		}
		return $index;
	}

	public function saveData($id, $record) {
		$rows = $this->getItems();
		$user = Factory::getUser();
		$add = true;
		if (($id != '') && isset($rows[$id])) { $add = false; }
		// Set system related fields
		foreach($this->colHeaders as $key => $col) {
			if (isset($this->colTypes[$col])) {
				switch (strtolower($this->colTypes[$col])) {
					case 'createby':
						if ($add) {
						    $record[$key] = $user->get('name');
						} else {
						    $record[$key] = $rows[$id][$key];
						}
						break;
					case 'createdate':
						if ($add) {
						    $record[$key] = date('Y-m-d H:i:s');
						} else {
						    $record[$key] = $rows[$id][$key];
						}
						break;
					case 'updateby':
						$record[$key] = $user->get('name');
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

	public function delete($id) {
		$rows = $this->getItems();
		if (isset($rows[$id])) { unset($rows[$id]); }
		$this->_save($rows);
	}

	public function exportCsvData($encoding) {
		$rows = $this->getItems();
		$rows = $this->_array_insert_before('0', $rows, $this->colHeaders);
		CsvHelper::save($rows,$encoding,'export-'.date('Ymd').'.csv');
	}

	public function exportJsonData() {
		$rows = $this->getItems();
		$cols = $this->colHeaders;
		$jsonData = array();
		foreach($rows as $row) {
			$record = array();
			foreach($cols as $key => $col) {
				if (isset($row[$key])) {
					$record[$col] = $row[$key];
				}
			}
			$jsonData[] = $record;
		}
		JsonHelper::save($jsonData,'utf-8','');
	}

	function importCsvData($file, $encoding) {
		$content = '';
		if (isset($file['tmp_name'])) {
			$content = file_get_contents($file['tmp_name']);
			unlink($file['tmp_name']);
		}
		$user = Factory::getUser();
		$content = str_replace("\r","\n",$content);
		$content = str_replace("\n\n","\n",$content);
		$rows = CsvHelper::load($content, $encoding);
		foreach($this->colHeaders as $fkey => $col) {
			if (isset($this->colTypes[$col])) {
				switch (strtolower($this->colTypes[$col])) {
					case 'createby':
					case 'updateby':
						foreach($rows as $rkey => $row) {
							$row[$fkey] = $user->get('name');
							$rows[$rkey] = $row;
						}
						break;
					case 'createdate':
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

	public function exchangeRecords($key1, $key2) {
		$rows = $this->getItems();
		if (isset($rows[$key1]) & isset($rows[$key2])) {
			$record = $rows[$key1];
			$rows[$key1] = $rows[$key2];
			$rows[$key2] = $record;
    		$this->_save($rows);
		}
	}

    private function _getConfig() {
    		// Read configs
            $app = Factory::getApplication();
            $state = $this->get('State');
            $currentMenu = $app->getMenu()->getActive();
    		if (is_object($currentMenu)) {
                return $currentMenu->getParams();
    		}
    		return null;
    }

	private function _setColData() {
        $configs = $this->_getConfig();
		if (is_object($configs)) {
            $config = $configs->get('columns');
        }
		$config = str_replace("\r","\n",$config);
		$config = str_replace("\n\n","\n",$config);
		$colHeaders = array();
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

	private function _save($rows) {
		if (!empty($this->file)) {
			if (!file_exists($this->file) || is_writeable($this->file)) {
				$content = CsvHelper::array2cvs($rows, $this->_delimiter);
				file_put_contents($this->file, $content);
			} else {
				$this->_showError(Text::_('COM_NOKLIST_FILE_NOT_WRITEABLE'));
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
}