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
Ext.ns('oseATH','oseATHAddIps');
oseATH.msg = new Ext.App();
function fnValidateIPAddress(ipaddr) {
    ipaddr = ipaddr.replace( /\s/g, "")
    var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
    if (re.test(ipaddr)) {
        //split into units with dots "."
        var parts = ipaddr.split(".");
        //if the first unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[0])) == 0) {
            return false;
        }
        //if the fourth unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[3])) == 0) {
            return false;
        }
        //if any part is greater than 255
        for (var i=0; i<parts.length; i++) {
            if (parseInt(parseFloat(parts[i])) > 255){
                return false;
            }
        }
        return true;
    } else {
        alert ('The IP '+ ipaddr + ' is not a valid IP address');
    	return false;
    }
}

function validateIPs(ip_start, ip_end)
{
		if (ip_start.dom.value!='')
	    {
	    	fnValidateIPAddress(ip_start.dom.value);
	    }
	    if (ip_end.dom.value!='')
	    {
    	    fnValidateIPAddress(ip_end.dom.value);
	    }
}


Ext.onReady(function(){
    iptype = Ext.get('iptype');
    ip_start = Ext.get('ip_start');
    ip_end = Ext.get('ip_end');
    addipbutton = Ext.get('addipbutton');

    title = Ext.get('title');
    // Checking IPs
    validateIPs(ip_start, ip_end);
    if (iptype.dom.value == 'ip')
    {
    	ip_end.dom.disabled=true;
    }
    iptype.on('change', function(){
    	if (iptype.dom.value == 'ip')
	    {
	    	ip_end.dom.disabled = true;
	    	ip_end.dom.value = ip_start.dom.value;
	    }
	    else
	    {
	    	ip_end.dom.disabled=false;
	    }
	    validateIPs(ip_start, ip_end);
    })
    ip_start.on('change', function(){
    	if (iptype.dom.value == 'ip')
	    {
	    	ip_end.dom.value = ip_start.dom.value;
	    }
		validateIPs(ip_start, ip_end);
    })
    ip_end.on('change', function(){
	    validateIPs(ip_start, ip_end);
    })
	addipbutton.on('click', function(){
		Ext.get('loadingindicator').show();
		// Inserting IPs
		Ext.Ajax.request({
					url : 'index.php' ,
					params : {
						option : 'com_ose_antihacker',
						controller:'antihacker',
						task:'addips',
						ip_start: ip_start.dom.value,
						ip_end: ip_end.dom.value,
						iptype: iptype.dom.value,
						status: Ext.get('status').dom.options[Ext.get('status').dom.selectedIndex].value,
						title: title.dom.value,
						insertype: 'man'
					},
					method: 'POST',
					success: function ( result, request ) {
						var msg = Ext.decode(result.responseText);
						if (msg.status=='Done')
						{
					        Ext.get('loadingindicator').hide();
					        Ext.fly('vs-notice').update(msg.result);
					        oseATH.msg.setAlert(msg.status, msg.result);
						}
						else
						{
							Ext.get('loadingindicator').hide();
							Ext.fly('vs-notice').update(msg.result);
							oseATH.msg.setAlert(msg.status, msg.result);
						}
					}
			});

	})
})
</script>