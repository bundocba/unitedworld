<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
$db = JFactory::getDBO();
$app = JFactory::getApplication();
$filter_order 		= $app->getUserStateFromRequest('com_fw_video.video.filter_order','filter_order','name','cmd');
$filter_order_Dir 	= $app->getUserStateFromRequest('com_fw_video.video.filter_order_Dir','filter_order_Dir','ASC','cmd');
?>
<form action="<?php echo JRoute::_('index.php?option=com_fw_video'); ?>" method="post" id="adminForm" name="adminForm">
	<table class="adminlist">
		
		<thead>
            <tr>
                <td colspan="8">
                	<?php echo $this->input_search; ?>
                	<?php echo $this->list_published; ?>
                	<?php echo $this->list_cate; ?>
				</td>
            </tr>
            <tr>
				<th width="5">
					ID
				</th>
                <th width="20">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
                </th>
				<th>
					<?php echo JHtml::_("grid.sort", "Category", "cateid", $filter_order_Dir, $filter_order); ?>
				</th>
				<th>
					<?php echo JHtml::_("grid.sort", "Name", "name", $filter_order_Dir, $filter_order); ?>
				</th>
				<th>
					<?php echo JHtml::_("grid.sort", "Link", "link", $filter_order_Dir, $filter_order); ?>
				</th>
				<th>
					<?php echo JHtml::_("grid.sort", "Intro Image", "intro_image", $filter_order_Dir, $filter_order); ?>
				</th>
				<th>
					<?php echo JHtml::_("grid.sort", "Ordering", "ordering", $filter_order_Dir, $filter_order); ?>
					<?php echo JHtml::_("grid.order",  $this->rows, "filesave.png", "video.saveorder"); ?>
				</th>
				<th width="50">
					<?php echo JHtml::_("grid.sort", "Published", "published", $filter_order_Dir, $filter_order); ?>
				</th>
            </tr>
		</thead>
		<tfoot>
            <tr>
                <td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
		</tfoot>
		<tbody>
<?php 
	$i = 0;
	foreach($this->rows as $n => $row){
		$published 	= JHTML::_("grid.published", $row, $i );
		$query="select name from #__fw_category where `id`='".$row->cateid."' ";
		$db->setQuery($query);
		$catename=$db->loadResult();
?>
            <tr class="row<?php echo $n % 2; ?>">
				<td>
                    <a href="index.php?option=com_fw_video&task=videoedit.edit&id=<?php echo $row->id; ?>"><?php echo $row->id; ?></a>
                </td>
                <td>
                    <?php echo JHtml::_("grid.id", $n, $row->id); ?>
                </td>
                <td>
                    <?php echo $catename; ?>
                </td>
				<td>
					<?php echo $row->name; ?>
				</td>
				<td>
					<?php echo $row->link; ?>
				</td>
				<td>
					<?php echo $row->intro_image; ?>
				</td>
				<td class="order">
					<?php if($filter_order=="ordering"){ ?>
						<?php if ($filter_order_Dir == "asc"){ ?>
							<span><?php echo $this->pagination->orderUpIcon($i, ($row->cateid == @$this->rows[$i-1]->cateid), "video.saveorderup", "Up", "ordering"); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($row->cateid == @$this->rows[$i+1]->cateid), "video.saveorderdown", "Down", "ordering"); ?></span>
						<?php } elseif ($filter_order_Dir == "desc") { ?>
							<span><?php echo $this->pagination->orderUpIcon($i, ($row->cateid == @$this->rows[$i-1]->cateid), "video.saveorderdown", "Up", "ordering"); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($row->cateid == @$this->rows[$i+1]->cateid), "video.saveorderup", "Down", "ordering"); ?></span>
						<?php } ?>
					<?php } ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text-area-order" />
				</td>
				<td>
					<?php echo JHtml::_("jgrid.published", $row->published, $i, "video."); ?>
				</td>
            </tr>
<?php 
		$i++;
	}
?>
		</tbody>
		
	</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="video" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>