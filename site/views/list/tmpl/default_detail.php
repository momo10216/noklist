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
$EOL = "\n";

// Display header
echo '<table>'.$EOL;

// Display record
$id = JFactory::getURI()->getVar('id');
$rows = $this->getData();
if (isset($rows[$id])) {
	$row = $rows[$id];
	foreach($this->colHeaders as $key => $col) {
		echo '<tr><td>'.$col.'</td><td>'.$this->getDisplayValue($col,$row[$key]).'</td></tr>'.$EOL;
	}
}

// Display footer
echo '</table>'.$EOL;
?>