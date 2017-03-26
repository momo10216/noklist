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
	protected $items;
	protected $pageHeading = 'COM_NOKLIST_PAGE_TITLE_DEFAULT';
	protected $paramsMenuEntry;
	protected $user;
	protected $colHeader = array();
	protected $file = '';

	function display($tpl = null) {
		// Init variables
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->document = JFactory::getDocument();
		$this->form = $this->get('Form');
		$menu = $app->getMenu();
		if (is_object($menu)) {
			$currentMenu = $menu->getActive();
			if (is_object($currentMenu)) {
				$this->paramsMenuEntry = $currentMenu->params;
				$this->colHeader = explode(';',$this->paramsMenuEntry->get('columns'));
				$this->file = $this->paramsMenuEntry->get('file');
			}
		}
		// Init document
		JFactory::getDocument()->setMetaData('robots', 'noindex, nofollow');
		parent::display($tpl);
	}

	function getData() {
		$data = array();
		if (!empty($this->file)) {
			if (is_readable($this->file)) {
				$content = file_get_contents($this->file);
				JLoader::register('CvsHelper', __DIR__.'/../../helpers/cvs.php', true);
				$data  = CvsHelper::loadCVS($content, 'UTF-8', "\t");
			} else {
				showError(JText::_('COM_NOKLIST_FILE_NOT_READABLE'));
			}
		}
		return $data;
	}

	function getLink($task, $id='') {
		$uri = new JURI(JURI::Root().'/index.php');
		$uri->setVar('layout','default');
		$uri->setVar('view','list');
		$uri->setVar('option','com_noklist');
		if (!empty($id)) {
			$uri->setVar('id',$id);
		}
		return $uri->toString();
	}
}
?>