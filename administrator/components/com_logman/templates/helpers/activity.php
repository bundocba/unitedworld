<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanTemplateHelperActivity extends KTemplateHelperDefault implements KServiceInstantiatable
{
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    public function message($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'row'         => '',
			'formatted'   => true,
			'escape_html' => false,
		    'absolute_links' => false
		));

		$row   = $config->row;
		$route = $this->_getRoute($row, $config);
		if (version_compare(JVERSION, '1.6', '<')) {
			$user = $this->_createRoute('index.php?option=com_users&view=user&task=edit&cid[]='.$row->created_by, $config);
		} else {
			$user = $this->_createRoute('index.php?option=com_users&task=user.edit&id='.$row->created_by, $config);
		}

		if($row->action == 'login' || $row->action == 'logout')
		{
		    $message   = '<a href="'.$route.'">'.$row->title.'</a>';
		    $message  .= ' <span class="action">'.$row->status.'</span>';
		}
		else
		{
			$message   = '<a href="'.$user.'">'.$row->created_by_name.'</a>';
			$message  .= ' <span class="action">'.$row->status.'</span>';

			if ($row->status != 'deleted')
			{
				if ($route) {
					$message .= ' <a href="'.$route.'">'.$row->title.'</a>';
				} else {
					$message .= ' '.$row->title;
				}
			}
			else $message .= ' <span class="ellipsis" class="deleted">'.$row->title.'</span>';

			$message .= ' <span class="ellipsis" class="package">'.$row->name.'</span>';
		}

		if ($config->escape_html) {
			$message = htmlspecialchars($message);
		}

		if (!$config->formatted) {
			$message = strip_tags($message);
		}

		return $message;
	}

	protected function _getRoute($row, $config)
	{
		$is15 = version_compare(JVERSION, '1.6', '<');
		switch ($row->name)
		{
			case 'user':
				if ($is15) {
					$route = 'index.php?option=com_users&view=user&task=edit&cid[]='.$row->row;
				} else {
					$route = 'index.php?option=com_users&view=user&task=user.edit&id='.$row->row;
				}
			break;

			case 'document':
				$route = 'index.php?option=com_docman&task=edit&section=documents&cid='.$row->row;
			break;

			default:
				if ($is15) {
					$route = 'index.php?option=com_content&sectionid=-1&task=edit&cid[]='.$row->row;
				} else {
					$route = 'index.php?option=com_'.$row->package.'&task='.$row->name.'.edit&id='.$row->row;
				}
			break;
		}

		return $this->_createRoute($route, $config);
	}

	protected function _createRoute($uri, $config)
	{
	    $uri = JRoute::_($uri);
	    
	    if ($config->absolute_links) {
	        $uri = JURI::getInstance()->toString(array('scheme', 'host', 'port')).$uri;   
	    }
	    
	    return $uri;
	}
}