<?
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= @helper('behavior.mootools') ?>
<?= @helper('behavior.bootstrap') ?>
<script src="media://lib_koowa/js/koowa.js" />

<? if (version_compare(JVERSION, '1.6', '<')): ?>
<style>
.icon-32-purge { background-image: url(media://com_logman/images/icon-32-purge.png); }
</style>
<? endif; ?>

<table id="activities">
	<tr>
		<td class="activities-sidebar">
			<?= @template('sidebar')?>
		</td>
		<td class="activities-body">
			<form action="" method="get" class="-koowa-grid">
			<table class="table">
				<thead>
					<tr>
						<th width="10" align="center">
		                    <?= @helper('grid.checkall') ?>
		                </th>
						<th class="activities-time"><?=@text('Time')?></th>
						<th class="activities-message"><?=@text('Message')?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3">
							<?= @helper('paginator.pagination', array('total' => $total)) ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<? $date = $old_date = '';   ?>
				<? foreach ($activities as $activity) : ?>
			        <? $date = @date(array('date' => $activity->created_on, 'format' => '%d %b %Y'))?>
			        <? if ($date != $old_date): ?>
			        <? $old_date = $date; ?>
			        <tr class="activities-timeago">
			        	<td></td>
						<td colspan="2">
					        <?= $date; ?>
						</td>
					</tr>
			        <? endif; ?>
					<tr>
						<td>
					        <a href="#" style="color:inherit"><?= @helper('grid.checkbox',array('row' => $activity)); ?></a>
						</td>

						<td align="left" class="activities-when">
					        <?= @date(array('date' => $activity->created_on, 'format' => '%H:%M:%S'))?>
						</td>

						<td class="activities-message">
							<?= @helper('activity.message', array('row' => $activity))?>
						</td>
					</tr>
		        <? endforeach; ?>
				</tbody>
			</table>
		</form>
		</td>
	</tr>
</table>