<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fw_videoViewvideo extends JView{
	function display($tpl = null){
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->rows = $items;
		$this->pagination = $pagination;
		
		$this->input_search = $this->getInputSearch();
		$this->list_published = $this->getListPublished();
		$this->list_cate = $this->getListCate();
		
		

		$this->addToolBar();
		parent::display($tpl);
	}	
	protected function addToolBar(){
		fw_videoHelper::addSubmenu('video');
		JToolBarHelper::title('Video Management', 'fw_video');
		
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-fw_video {background-image: url(../administrator/components/com_fw_video/images/icon_item.png);}');
		$document->setTitle('Video Management');
		
		$canDo = fw_videoHelper::getActions();
		if ($canDo->get('core.create')){
			JToolBarHelper::addNewX('videoedit.add');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editListX('videoedit.edit');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteListX('', 'video.delete');
		}
		JToolBarHelper::publishList('video.publish');
		JToolBarHelper::unpublishList('video.unpublish');
		JToolBarHelper::preferences('com_fw_video');
	}
	function getInputSearch(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$input='<input type="text" name="search" id="search" value="'.$app->getUserStateFromRequest('com_fw_video.video.search','search','','cmd').'" size="20">';
		$input_search='
			<div style="float:left; padding-right:10px; padding-top:15px;">
				Search '.$input.' <input type="submit" value="Search" />
			</div>
		';
		return $input_search;
	}
	function getListPublished(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$options = array();
		$options[] = JHTML::_('select.option', '', 'Choose Published');
		$options[] = JHTML::_('select.option', '1', 'Published');
		$options[] = JHTML::_('select.option', '0', 'UnPublished');
		$list=JHTML::_('select.genericlist', $options, 'published', 'class="inputbox" onchange="$(\'adminForm\').submit();"', 'value', 'text', $app->getUserStateFromRequest('com_fw_video.video.published','published','','cmd'));
		$list_published = '
			<div style="float:left; padding-right:10px; padding-top:15px;">
				Published '.$list.'
			</div>
		';
		return $list_published;
	}
	function getListCate(){
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$query="select * from #__fw_category order by ordering ASC ";
		$db->setQuery($query);
		$rows=$db->loadObjectList();

		$children = array();
		if(count($rows)>0){
			foreach ($rows as $v ){
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
			$rows = fw_videoHelper::treeRecurse(intval($rows[0]->parent_id), '', array(), $children, 9999, 0, 0);
		}
		
		$options = array();
		$options[] = JHtml::_('select.option', '', 'Select category');
		if($rows){
			foreach($rows as $row){
				$options[] = JHtml::_('select.option', $row->id, $row->name);
			}
		}
	
		$list=JHTML::_('select.genericlist', $options, 'cateid', 'class="inputbox" onchange="$(\'adminForm\').submit();"', 'value', 'text', $app->getUserStateFromRequest('com_fw_video.video.cateid','cateid','','cmd'));
		$list_cate = '
			<div style="float:left; padding-right:10px; padding-top:15px;">
				Category '.$list.'
			</div>
		';
		
		return $list_cate;
	}
	
	
	
}