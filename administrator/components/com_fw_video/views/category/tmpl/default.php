<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
$db = JFactory::getDBO();
$app = JFactory::getApplication();
$filter_order 		= $app->getUserStateFromRequest('com_fw_video.category.filter_order','filter_order','name','cmd');
$filter_order_Dir 	= $app->getUserStateFromRequest('com_fw_video.category.filter_order_Dir','filter_order_Dir','ASC','cmd');
?>
<form action="<?php echo JRoute::_('index.php?option=com_fw_video'); ?>" method="post" id="adminForm" name="adminForm">
	<table class="adminlist">
		
		<thead>
            <tr>
                <td colspan="5">
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
                	<?php echo JHtml::_("grid.sort", "Name", "name", $filter_order_Dir, $filter_order); ?>
                </th>
				<th>
					<?php echo JHtml::_("grid.sort", "Ordering", "ordering", $filter_order_Dir, $filter_order); ?>
					<?php echo JHtml::_("grid.order",  $this->rows, "filesave.png", "category.saveorder"); ?>
				</th>
				<th width="50">
					<?php echo JHtml::_("grid.sort", "Published", "published", $filter_order_Dir, $filter_order); ?>
				</th>
            </tr>
		</thead>
		<tfoot>
            <tr>
                <td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
		</tfoot>
		<tbody>
<?php 
	$i = 0;
	if(count($this->rows)>0){
	foreach($this->rows as $n => $row){
		$published 	= JHTML::_("grid.published", $row, $i );
		$orderkey	= array_search($row->id, $this->ordering[$row->parent_id]);
?>
            <tr class="row<?php echo $n % 2; ?>">
				<td>
                    <a href="index.php?option=com_fw_video&task=categoryedit.edit&id=<?php echo $row->id; ?>"><?php echo $row->id; ?></a>
                </td>
                <td>
                    <?php echo JHtml::_("grid.id", $n, $row->id); ?>
                </td>
				<td>
					<?php echo $row->name; ?>
				</td>
				<td class="order">
					<?php if($filter_order=="ordering"){ ?>
							<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$row->parent_id][$orderkey - 1]), "category.saveorderup", "JLIB_HTML_MOVE_UP", "ordering"); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$row->parent_id][$orderkey + 1]), "category.saveorderdown", "JLIB_HTML_MOVE_DOWN", "ordering"); ?></span>
					<?php } ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text-area-order" />
				</td>
				<td>
					<?php echo JHtml::_("jgrid.published", $row->published, $i, "category."); ?>
				</td>
            </tr>
<?php 
		$i++;
	}
	}
?>
		</tbody>
		
	</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="category" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>