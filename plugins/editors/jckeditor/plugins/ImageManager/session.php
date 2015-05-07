<?php 
jimport('joomla.plugin.helper'); //keep this in for backward compatiblity
jimport('joomla.filesystem.folder');
jimport('joomla.registry.registry');
jimport('joomla.application.component.helper');
jimport('joomla.log.log');
jimport('joomla.utilities.utility');
require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'session'.DS.'storage.php');

class JCKSession
{
	static function getSessionInstance()
	{
		static $instance;
		
		if($instance)
		{
			return $instance;
		}//end if
	   
	    $session_name = JCKSession::getName();
	    //session_name( $session_name );
    	$instance =  JFactory::getSession(array('name'=>$session_name));
		
		if(method_exists('JSession','start') && class_exists('JEventDispatcher'))
		{
				if(!$instance->isActive())
				{
					$dispatcher = JEventDispatcher::getInstance();
					$instance->initialise(new JInput, $dispatcher);
					$instance->start();
				}	
		}
      	return 	$instance;
	}//end function getSessionInstance
    
    /**
	 * @version 	1.1		Modified by MS to check for the Cookie
	 * @modified	06 Dec 2011
	 */
   static function getName()
    {
     	//Does the cookie have a value? If so get it.
		
        $clientId 	= JRequest::getInt('client',JRequest::getInt('client',0,'cookie')); //fix for PHP 5.3
		$client 	= ($clientId ? 'administrator' : 'site' );
		
		$hash = '';
 
		if(method_exists('JUtility','getHash'))
		    $hash = JUtility::getHash($client);
		else
			 $hash = JApplication::getHash($client);	
		
	  	return $hash;
		
    }//end function getName

}