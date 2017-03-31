<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	NoK-List
* @copyright	Copyright (c) 2017 Norbert K�min. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/
defined('_JEXEC') or die; // no direct access

$EOL = "\n";
$id = JRequest::getVar('id');
$record = array();
if ($id != '') {
	$rows = $this->getData();
	if (isset($rows[$id])) { $record = $rows[$id]; }
	$title = JText::_('COM_NOKLIST_PAGE_EDIT_TITLE');
} else {
	$title = JText::_('COM_NOKLIST_PAGE_ADD_TITLE');
}
?>
<h1><?php echo $title; ?></h1>
<form action="<?php echo $this->getLink('save',$id); ?>" method="post" name="adminForm" id="adminForm">

<?php
foreach ($this->colHeaders as $key => $col) {
	$value = '';
	if (isset($record[$key])) { $value = $record[$key]; }
	echo '	<div class="control-label"><label for="jform_'.$key.'" title="" data-original-title="'.$col.'">'.$col.'</label></div>'.$EOL;
	echo '	<div class="controls">';
	switch (strtolower($this->colTypes[$col])) {
		case 'select':
			echo '<select name="col_'.$key.'">';
			foreach ($this->colParams[$col] as $param) {
				echo '<option';
				if ($param == $value) { echo ' selected'; }
				echo '>'.$param.'</option>';
			}
			echo '</select>';
			break;
		case 'range':
			echo '<input id="jform_'.$key.'" type="range" name="col_'.$key.'" value="'.$value.'"';
			if (isset($this->colParams[$col])) {
				if(isset($this->colParams[$col][0])) { echo ' min="'.$this->colParams[$col][0].'"'; }
				if(isset($this->colParams[$col][1])) { echo ' max="'.$this->colParams[$col][1].'"'; }
				if(isset($this->colParams[$col][2])) { echo ' step="'.$this->colParams[$col][2].'"'; }
			}
			echo '/>';
			break;
		case 'date':
			$format = JText::_('DATE_FORMAT_LC4');
			foreach(array('Y','m','d') as $char) {
				$format = str_replace($char, '%'.$char, $format);
			}
			echo JHtml::calendar($value, 'col_'.$key, 'jform_'.$key, $format);
			break;
		case 'text':
		default:
			echo '<input id="jform_'.$key.'" type="text" name="col_'.$key.'" value="'.$value.'"/>';
			break;
	}
	echo '</div>'.$EOL;
}
?>
	<p align="center">
		<button type="submit">
			<?php echo JText::_('JSAVE') ?>
		</button>
		<button onClick="location.href='<?php echo $this->getLink('list'); ?>';">
			<?php echo JText::_('JCANCEL') ?>
		</button>
	</p>
</form>