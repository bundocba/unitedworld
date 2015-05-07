<?php
/**
 * Plugin Helper File
 *
 * @package         Tabs
 * @version         3.6.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 ** Plugin that places the button
 */
class plgButtonTabsHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function render($name)
	{
		$button = new JObject;

		if (JFactory::getApplication()->isSite() && !$this->params->enable_frontend)
		{
			return $button;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_tabs');

		JHtml::stylesheet('nnframework/style.min.css', false, true);

		$this->params->tag_open = preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_open);
		$this->params->tag_close = preg_replace('#[^a-z0-9-_]#s', '', $this->params->tag_close);
		$this->params->tag_delimiter = ($this->params->tag_delimiter == '=') ? '=' : ' ';

		$text = $this->getText();
		$text = str_replace('\\\\n', '\\n', addslashes($text));
		$text = str_replace('{', '{\'+\'', $text);

		$js = "
			function insertTabs(editor) {
				jInsertEditorText('" . $text . "', editor);
			}
		";
		JFactory::getDocument()->addScriptDeclaration($js);

		$class = 'blank';
		if ($this->params->button_icon)
		{
			$class .= ' button-nonumber button-tabs';
		}

		$text_ini = strtoupper(str_replace(' ', '_', $this->params->button_text));
		$text = JText::_($text_ini);
		if ($text == $text_ini)
		{
			$text = JText::_($this->params->button_text);
		}

		$button->modal = false;
		$button->link = '#';
		$button->onclick = 'insertTabs(\'' . $name . '\');return false;';
		$button->text = trim($text);
		$button->name = $class;

		return $button;
	}

	private function getText()
	{
		switch (true)
		{
			case ($this->params->button_use_custom_code && $this->params->button_custom_code):
				return $this->getCustomText();
			default:
				return $this->getDefaultText();
		}
	}

	private function getDefaultText()
	{
		return
			'{' . $this->params->tag_open . $this->params->tag_delimiter . JText::_('Course Infomation') . ' }\n' .
			'<p>' . JText::_('TAB_TEXT') . '</p><p>&nbsp;</p><p style="text-align: right;"><strong>Unitedworld<br />CPE Registration No: 200912858H<br />Period of Registration: 03 June 2011 to 02 June 2015</strong></p><p>&nbsp;</p>' .
			'<p>{' . $this->params->tag_open . $this->params->tag_delimiter . JText::_('Learning Outcomes') . '}</p>\n' .
			'<p>' . JText::_('TAB_TEXT') . '</p><p>&nbsp;</p><p style="text-align: right;"><strong>Unitedworld<br />CPE Registration No: 200912858H<br />Period of Registration: 03 June 2011 to 02 June 2015</strong></p><p>&nbsp;</p>' .
			'<p>{' . $this->params->tag_open . $this->params->tag_delimiter . JText::_('Module Synopses') . '}</p>\n' .
			'<p>' . JText::_('TAB_TEXT') . '</p><p>&nbsp;</p><p style="text-align: right;"><strong>Unitedworld<br />CPE Registration No: 200912858H<br />Period of Registration: 03 June 2011 to 02 June 2015</strong></p><p>&nbsp;</p>' .
			'<p>{/' . $this->params->tag_close . '}</p>';
	}

	private function getCustomText()
	{
		$text = trim($this->params->button_custom_code);
		$text = str_replace(array("\r", "\n"), array('', '</p>\n<p>'), trim($text)) . '</p>';
		$text = preg_replace('#^(.*?)</p>#', '\1', $text);
		$text = str_replace(array('{tab ', '{/tabs}'), array('{' . $this->params->tag_open . $this->params->tag_delimiter, '{/' . $this->params->tag_close . '}'), trim($text));

		return $text;
	}
}
