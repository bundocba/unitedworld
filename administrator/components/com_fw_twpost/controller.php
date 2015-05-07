<?php
class Fw_twpostController extends JControllerLegacy
{
	protected $default_view = 'articles';
	public function display($cachable = false, $urlparams = false)
	{
		$view		= JRequest::getCmd('view', 'articles');
		$layout 	= JRequest::getCmd('layout', 'articles');
		$id			= JRequest::getInt('id');
		parent::display();
		return $this;
	}
}
