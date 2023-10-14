<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_noklist
 *
 * @copyright   Copyright (c) 2023 Norbert Kümin. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NKuemin\Component\NoKList\Site\View\List;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;

/**
 * View for the containers list
 */
class HtmlView extends BaseHtmlView {
    /**
     * The model state
     *
     * @var   \Joomla\CMS\Object\CMSObject
     */
    protected $state = null;

    /**
     * An array containing archived articles
     *
     * @var   \stdClass[]
     */
    protected $items = array();

    /**
     * The page parameters
     *
     * @var    \Joomla\Registry\Registry|null
     *
     * @since  4.0.0
     */
    protected $paramsGlobal = null;
    protected $paramsMenu = null;

    /**
     * The app object
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     *
     * @since  4.0.0
     */
    protected $app = null;

    /**
     * The user object
     *
     * @var    \Joomla\CMS\User\User
     *
     * @since  4.0.0
     */
    protected $user = null;

    /**
     * Display the view
     *
     * @param   string  $template  The name of the layout file to parse.
     * @return  void
     */
    public function display($template = null) {
        $this->app = Factory::getApplication();
        $this->user = $this->getCurrentUser();
        $this->state = $this->get('State');
        $this->items = $this->get('Items');

        if ($errors = $this->getModel()->getErrors()) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Get and merge parameters
        $this->paramsGlobal = $this->state->get('params');
        $currentMenu = $this->app->getMenu()->getActive();
		if (is_object($currentMenu)) {
            $this->paramsMenu = $currentMenu->getParams();
		}

        // Call the parent display to display the layout file
        parent::display($template);
    }

	function getLink($task, $id='') {
        $uri = Uri::getInstance();
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
		if (is_object($this->paramsMenu)) {
			if ($this->paramsMenu->get('allow_change') == '1') {
				if (array_search($this->paramsMenu->get('change_access'), $this->user->getAuthorisedViewLevels()) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	function getDisplayValue($col, $value) {
		if (isset($this->colTypes[$col])) {
			if (isset($this->colParams[$col])) {
				$param = $this->colParams[$col];
			} else {
				$param = array();
			}
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
				case 'url':
					$newWin = false;
					if (isset($param[0]) && ($param[0] == '1')) {
						$newWin = true;
					}
					$linkText = $value;
					if (isset($param[1]) && !empty($param[1])) {
						$linkText = $param[1];
					}
					if (!empty($value)) {
						$retval = '<a href="'.$value.'"';
						if ($newWin === true) { $retval .= ' target="_blank"'; }
						$retval .= '>'.$linkText.'</a>';
						return $retval;
					}
					break;
				case 'email':
					if (!empty($value)) { return '<a href="mailto:'.$value.'">'.$value.'</a>'; }
					break;
				case 'boolean':
					if ($value == '0') { return JText::_('JNO'); }
					if ($value == '1') { return JText::_('JYES'); }
					break;
				default:
					break;
			}
		}
		return $value;
	}

	function getSortLink($sortField='', $sortDirection='') {
        $uri = Uri::getInstance();
		$uri->setVar('layout','default');
		$uri->setVar('view','list');
		$uri->setVar('option','com_noklist');
		$uri->setVar('task','list');
		$uri->setVar('sortfield',$sortField);
		$uri->setVar('sortdirection',$sortDirection);
		if ($this->menuItemId != '') {
			$uri->setVar('menuitemid',$this->menuItemId);
		}
		return $uri->toString();
	}
}