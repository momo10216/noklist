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

$task = JRequest::getVar('task');
switch ($task) {
	case 'new':
	case 'edit':
		echo $this->loadTemplate('edit');
		break;
	case 'delete':
		break;
	case 'list':
	default:
		echo $this->loadTemplate('list');
		break;
}
?>