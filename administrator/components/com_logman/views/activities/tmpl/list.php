<?
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<? if(count($activities)) : ?>
	<?= @helper('behavior.bootstrap') ?>
	<div id="activities" class="com_logman">
		<table class="table table-striped simple">
			<tfoot>
				<tr>
					<td colspan="2" class="browse-view-all"><a href="index.php?option=com_logman&view=activities" class="btn"><?=@text('VIEW_ALL')?></a></td>
				</tr>
			</tfoot>
			<tbody>
			<? foreach ($activities as $activity) : ?>
				<tr>
					<td align="left" class="activities-when">
				        <?= @helper('date.humanize', array('date' => $activity->created_on, 'format' => '%l:%M%p'))?>
					</td>

					<td class="activities-message">
						<?= @helper('activity.message', array('row' => $activity))?>
					</td>
				</tr>
	        <? endforeach; ?>
			</tbody>
		</table>
	</div>
<? endif ?>