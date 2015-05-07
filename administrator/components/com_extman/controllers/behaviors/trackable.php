<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerBehaviorTrackable extends KControllerBehaviorAbstract
{
    protected $_token;
    
    public function __construct($config)
    {
        parent::__construct($config);
        
        $this->setToken($config->token);
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'token' => 'c7326a714a1275378a6d4608f547737b'
        ));
        
        parent::_initialize($config);
    }
    
    public function setToken($token)
    {
        $this->_token = $token;
        
        return $this;
    }
    
    public function getToken()
    {
        return $this->_token;
    }
    
	protected function _afterGet(KCommandContext $context)
	{
		$event = $this->getModel()->event;

		if ($event)
		{
			$name 	   = $this->getView()->getName();
			$extension = $name == 'extension' ? $this->getModel()->getItem() : $this->getRequest();

			$context->result = $context->result.$this->track($event, $extension);
		}
	}

	protected function _afterDelete(KCommandContext $context)
	{
		if ($context->status == KHttpResponse::NO_CONTENT)
		{
			$extension = $context->result instanceof KDatabaseRowsetInterface ? $context->result->top() : $context->result;

			$url = KRequest::referrer();

			$query            = $url->getQuery(true);
			$query['event']   = 'uninstall';
			$query['name']    = $extension->name;
			$query['version'] = $extension->version;

			$url->setQuery($query);
			$this->setRedirect($url);
		}
	}

	public function getTrackingInfo($extension)
	{
		$version = new JVersion();

		$server = @php_uname('s').' '.@php_uname('r');

		// php_uname is disabled
		if (empty($server)) {
			$server = 'Unknown';
		}

		$info = array(
			'Product' 			=> $extension->name,
			'Version' 			=> $extension->version,
			'Joomla' 			=> $this->_extractVersionInfo($version->getShortVersion()),
			'Koowa'	 			=> class_exists('Koowa') && method_exists('Koowa', 'getInstance') ? Koowa::getInstance()->getVersion() : 0,
			'PHP' 				=> $this->_extractVersionInfo(phpversion()),
			'Database' 			=> $this->_extractVersionInfo(JFactory::getDBO()->getVersion()),
			'Web Server' 		=> @$_SERVER['SERVER_SOFTWARE'],
			'Web Server OS' 	=> $server,
			'Joomla Language' 	=> JFactory::getLanguage()->getName()
		);

		return $info;
	}

	public function track($event, $extension)
	{
		$info = json_encode($this->getTrackingInfo($extension));
		$return = "<script type=\"text/javascript\"> var mp_protocol = (('https:' == document.location.protocol) ? 'https://' : 'http://'); document.write(unescape('%3Cscript src=\"' + mp_protocol + 'api.mixpanel.com/site_media/js/api/mixpanel.js\" type=\"text/javascript\"%3E%3C/script%3E')); </script> <script type='text/javascript'> try {  var mpmetrics = new MixpanelLib('mixpanel_token'); } catch(err) { null_fn = function () {}; var mpmetrics = {  track: null_fn,  track_funnel: null_fn,  register: null_fn,  register_once: null_fn, register_funnel: null_fn }; } </script>"
				. "<script type=\"text/javascript\">mpmetrics.track('".$event."', ".$info.")</script>";
		
		// Can't use sprintf as the percent encoding confuses it
		$return = str_replace('mixpanel_token', $this->_token, $return);

		return $return;
	}

	protected function _extractVersionInfo($version)
	{
		return substr($version, 0, strpos($version, '.', strpos($version, '.')+1));
	}
}