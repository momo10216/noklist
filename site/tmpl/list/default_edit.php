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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$EOL = "\n";
$id = Factory::getApplication()->getInput()->get('id');
$record = array();
if ($id != '') {
    $rows = $this->getModel()->getItems();
	if (isset($rows[$id])) {
	    $record = $rows[$id];
	    $title = Text::_('COM_NOKLIST_PAGE_EDIT_TITLE');
	} else {
    	$title = Text::_('COM_NOKLIST_PAGE_ADD_TITLE');
	}
} else {
	$title = Text::_('COM_NOKLIST_PAGE_ADD_TITLE');
}
?>
<h1><?php echo $title; ?></h1>
<form action="<?php echo $this->getLink('save',$id); ?>" method="post" name="adminForm" id="adminForm">

<?php
foreach ($this->getModel()->colHeaders as $key => $col) {
	$value = '';
	$multiple = false;
	if (isset($record[$key])) { $value = $record[$key]; }
	echo '	<div class="control-label"><label for="jform_'.$key.'" title="" data-original-title="'.$col.'">'.$col.'</label></div>'.$EOL;
	echo '	<div class="controls">';
	switch (strtolower($this->getModel()->colTypes[$col])) {
		case 'multiselect':
			$multiple = true;
		case 'select':
			$values = explode(',',$value);
			echo '<select name="col_'.$key;
			if ($multiple) { echo '[]'; }
			echo '"';
			if ($multiple) { echo ' multiple'; }
			echo '>';
			foreach ($this->getModel()->colParams[$col] as $param) {
				echo '<option';
				if (array_search($param,$values) !== false)  { echo ' selected'; }
				echo '>'.$param.'</option>';
			}
			echo '</select>';
			break;
		case 'boolean':
			JFormHelper::loadFieldClass('radio');
			$field = new JFormFieldRadio();
			$xml = '<field name="col_'.$key.'" type="radio" size="1" default="1" class="btn-group btn-group-yesno">';
			$xml .= '<option value="1">JYES</option>';
			$xml .= '<option value="0">JNO</option>';
			$xml .= '</field>';
			$field->setup(new SimpleXMLElement($xml), 1);
			$field->setValue($value);
			echo $field->renderField(array('hiddenLabel'=>true));
			break;
		case 'range':
			echo '<input id="jform_'.$key.'" type="range" name="col_'.$key.'" value="'.$value.'"';
			if (isset($this->getModel()->colParams[$col])) {
				if(isset($this->getModel()->colParams[$col][0])) { echo ' min="'.$this->getModel()->colParams[$col][0].'"'; }
				if(isset($this->getModel()->colParams[$col][1])) { echo ' max="'.$this->getModel()->colParams[$col][1].'"'; }
				if(isset($this->getModel()->colParams[$col][2])) { echo ' step="'.$this->getModel()->colParams[$col][2].'"'; }
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
		case 'textarea':
			echo '<textarea id="jform_'.$key.'" name="col_'.$key.'"';
			if (isset($this->getModel()->colParams[$col])) {
				if(isset($this->getModel()->colParams[$col][0])) { echo ' rows="'.$this->getModel()->colParams[$col][0].'"'; }
				if(isset($this->getModel()->colParams[$col][1])) { echo ' cols="'.$this->getModel()->colParams[$col][1].'"'; }
			}
			echo '>'.$value.'</textarea>';
			break;
		case 'htmlarea':
			$editor = JFactory::getEditor();
			$width = '100%';
			$height = '300';
			$cols = '60';
			$rows = '20';
			if (isset($this->getModel()->colParams[$col])) {
				if(isset($this->getModel()->colParams[$col][0])) { $height = $this->getModel()->colParams[$col][0]; }
				if(isset($this->getModel()->colParams[$col][1])) { $width = $this->getModel()->colParams[$col][1]; }
				if(isset($this->getModel()->colParams[$col][2])) { $cols = $this->getModel()->colParams[$col][2]; }
				if(isset($this->getModel()->colParams[$col][3])) { $rows = $this->getModel()->colParams[$col][3]; }
			}
			echo $editor->display('col_'.$key, $value, $width, $height, $cols, $rows);
			break;
		case 'createby':
		case 'updateby':
			echo '<span id="jform_'.$key.'">'.$value.'</span>';
			break;
		case 'createdate':
		case 'updatedate':
			$date = new JDate($value);
			$format = Text::_('DATE_FORMAT_LC4');
			echo '<span id="jform_'.$key.'">'.$date->format($format).'</span>';
			break;
		case 'number':
			echo '<input id="jform_'.$key.'" type="number" name="col_'.$key.'" value="'.$value.'"/>';
			break;
		case 'email':
		case 'url':
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
			<?php echo Text::_('JSAVE') ?>
		</button>
		<button onClick="location.href='<?php echo $this->getLink('list'); ?>';">
			<?php echo Text::_('JCANCEL') ?>
		</button>
	</p>
</form>
