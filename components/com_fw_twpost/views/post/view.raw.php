<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
require_once  (JPATH_ROOT.DS."components".DS."com_fw_twpost".DS."helpers".DS."post_tweet.php");
class Fw_twpostViewPost extends JView{
	public function display($tpl = null){
		//Get Param
		$db				 = JFactory::getDbo();
		$app			 = JFactory::getApplication();
		$component		 = JRequest::getVar('option','com_fw_twpost');
		$result 		 = JComponentHelper::getComponent($option= $component);
		$cids 			 = JRequest::getVar('id','');
		$consumer_key 	 = $result->params->get('consumer_key');
		$consumer_secret = $result->params->get('consumer_secret');
		$access_token 	 = $result->params->get('access_token');
		$access_secret 	 = $result->params->get('access_token_secret');
		$cids 			 = explode (',',$cids);
		$type			 = '';
		if(isset($_GET['type'])){
			$type 			 = 	$_GET['type'];
		}
		//End Get Param
		//Query
		
		if(count($cids)>0)
		{
			$msg = array();
			foreach($cids as $key =>$cid)
			{	
				if($type=='k2')
				{
					$query = "SELECT title,introtext FROM #__k2_items WHERE id= ".$cid;	
				}else{					
					$query = "SELECT title,introtext FROM #__content WHERE id= ".$cid;	
				}
				$db->setQuery($query);
				$item=$db->loadAssoc();
				//End Query
				$title = $item['title'];
				$introtext = substr(strip_tags($item['introtext']),0,55);
				if(strlen($title)<=80){
					$content = substr($title,0,80).' - '.$introtext.'...';
				}
				//Post
				$post_tweet  =new post_tweet();			
				$output = $post_tweet->post($content,$consumer_key,$consumer_secret,$access_token,$access_secret);
				if($output == 1){
					$msg[$cid]= '1';
				}else{
					$msg[$cid]= '0';
				}
			}
		}
		//Insert Log
		foreach($msg AS $key =>$val){
			if($val=='1'){
				if($type == 'k2')
				{
					$query = "INSERT INTO #__tw_k2articles_history_post  values('',".$key.",NOW())";
				}else{
					$query = "INSERT INTO #__tw_articles_history_post  values('',".$key.",NOW())";
				}
				$db->setQuery($query);
				$db->query();
			}
		}
		//End Insert Log
		//Redirect status message
		$id 	= '';
		$status = '';
		foreach($msg AS $ids=>$status)
		{	
			$id 	.= ','.$ids.':'.$status;
		}
		$id = ltrim($id,',');
		$link 			   =  JURI::root().'administrator/index.php?option=com_fw_twpost&view=articles&id='.$id;
		if($type=='k2')
		{
			$link 		   =  JURI::root().'administrator/index.php?option=com_fw_twpost&view=k2articles&id='.$id;
		}
		$msg 			   =  'Success';
		$app->redirect($link, $msg, $msgType='message');
	}
}
?>
