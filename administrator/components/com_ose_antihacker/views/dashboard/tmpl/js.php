<?php
/**
  * @version     3.0 +
  * @package       Open Source Excellence Security Suite
  * @subpackage    Open Source Excellence CPU
  * @author        Open Source Excellence {@link http://www.opensource-excellence.com}
  * @author        Created on 30-Sep-2010
  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  *
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *  @Copyright Copyright (C) 2008 - 2010- ... Open Source Excellence
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
?>
<script type="text/javascript" >
Ext.ns('oseATH','oseATHDashboard');
 oseATHDashboard.store = new Ext.data.JsonStore({
 		  fields:['name', 'attacks', 'attacks2'],
 	      proxy: new Ext.data.HttpProxy({
	            url: 'index.php?option=com_ose_antihacker',
	            method: 'POST'
	      }),
		  baseParams:{controller: "report", task: "getBlacklistedSummary"},
		  autoLoad:{}
    });


Ext.onReady(function(){
	Ext.chart.Chart.CHART_URL = '<?php echo JURI::root().'components/com_ose_cpu/extjs/resources/charts.swf';?>';
    // extra extra simple
    new Ext.Panel({
        renderTo: 'dashboardchart',
        width:980,
        height:355,
        layout:'fit',
        items: {
            xtype: 'linechart',
            store: oseATHDashboard.store,
            xField: 'name',
            series: [{
				        type: 'line',
				        title: 'Blacklisted IPs',
				        highlight: true,
				        fill:true,
				        yField: 'attacks',
				     },{
				        type:'line',
				        title: 'Monitored IPs',
				        highlight: true,
				        fill:true,
				        yField: 'attacks2',
				     }],
		   tipRenderer : function(chart, record, index, series){
                if(series.yField == 'attacks'){
                    return Ext.util.Format.number(record.data.attacks, '0,0') + ' blacklisted ip on ' + record.data.name;
                }else{
                    return Ext.util.Format.number(record.data.attacks2, '0,0') + ' monitored ip on ' + record.data.name;
                }
            },
        }
      });
})
</script>