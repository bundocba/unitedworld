<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
//require_once  (JPATH_ROOT.DS."components".DS."com_fw_fbpost".DS."helpers".DS."facebook".DS."index.php");
class Fw_fbpostViewPost extends JView{
	public function display($tpl = null){
		//Get Param
		$db				 = JFactory::getDbo();
		$app			 = JFactory::getApplication();
		$component		 = JRequest::getVar('option','com_fw_fbpost');
		$result 		 = JComponentHelper::getComponent($option= $component);
		$cids 			 = JRequest::getVar('id','');
		$api_token 		 = trim($result->params->get('user_token'));
		$app_id			 = trim($result->params->get('app_id'));
		$app_secrect 	 = trim($result->params->get('app_secret'));
		$cids 			 = explode (',',$cids);
		$type			 = '';
		if(isset($_GET['type']))
		{
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
				$title	 	= urlencode($item['title']);
				$introtext  = urlencode(strip_tags($item['introtext']));
				$content 	= $title."%0A%0A".$introtext;

				//Post
				$url = JURI::base()."components/com_fw_fbpost/views/post/index.php?app_id=".$app_id."&secret=".$app_secrect."&content=".$content."&access_token=".$api_token;
				//echo $url;exit;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				//echo $output;exit;
				if($output == 1){
					$msg[$cid]= '1';
				}else{
					$msg[$cid]= '0';
				}
			}
		}
		
		foreach($msg AS $key =>$val){
			if($val=='1'){
				if($type == 'k2')
				{
					$query = "INSERT INTO #__k2articles_history_post  values('',".$key.",NOW())";
				}else{
					$query = "INSERT INTO #__articles_history_post  values('',".$key.",NOW())";
				}
				$db->setQuery($query);
				$db->query();
			}
		}
		$id 	= '';
		$status = '';
		foreach($msg AS $ids=>$status)
		{	
			$id 	.= ','.$ids.':'.$status;
		}
		$id = ltrim($id,',');
		$link 			   =  JURI::root().'administrator/index.php?option=com_fw_fbpost&view=articles&id='.$id;
		if($type=='k2')
		{
			$link 		   =  JURI::root().'administrator/index.php?option=com_fw_fbpost&view=k2articles&id='.$id;
		}
		$msg 			   =  'Success';
		$app->redirect($link, $msg, $msgType='message');
	}
}
?>
