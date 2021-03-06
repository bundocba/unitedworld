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
if (!defined('_JEXEC') && !defined('OSEDS'))
{
	die("Direct Access Not Allowed");
}
function getSignatures()
{
	$signatures[]='chr(';
	$signatures[]='chr=';
	$signatures[]='chr%20';
	$signatures[]='%20chr';
	$signatures[]='wget%20';
	$signatures[]='%20wget';
	$signatures[]='wget(';
	$signatures[]='cmd=';
	$signatures[]='%20cmd';
	$signatures[]='cmd%20';
	$signatures[]='rush=';
	$signatures[]='%20rush';
	$signatures[]='rush%20';
	$signatures[]='union%20';
	$signatures[]='UNION%20';
	$signatures[]='%20union';
	$signatures[]='%20UNION';
	$signatures[]='union(';
	$signatures[]='union=';
	$signatures[]='union+';
	$signatures[]='UNION+';
	$signatures[]='echr(';
	$signatures[]='%20echr';
	$signatures[]='echr%20';
	$signatures[]='echr=';
	$signatures[]='system(';
	$signatures[]='system%20';
	$signatures[]='cp%20';
	$signatures[]='%20cp';
	$signatures[]='cp(';
	$signatures[]='mdir%20';
	$signatures[]='%20mdir';
	$signatures[]='mdir(';
	$signatures[]='mcd%20';
	$signatures[]='mrd%20';
	$signatures[]='rm%20';
	$signatures[]='%20mcd';
	$signatures[]='%20mrd';
	$signatures[]='%20rm';
	$signatures[]='mcd(';
	$signatures[]='mrd(';
	$signatures[]='rm(';
	$signatures[]='mcd=';
	$signatures[]='mrd=';
	$signatures[]='mv%20';
	$signatures[]='rmdir%20';
	$signatures[]='mv(';
	$signatures[]='rmdir(';
	$signatures[]='chmod(';
	$signatures[]='chmod%20';
	$signatures[]='%20chmod';
	$signatures[]='chmod(';
	$signatures[]='chmod=';
	$signatures[]='chown%20';
	$signatures[]='chgrp%20';
	$signatures[]='chown(';
	$signatures[]='chgrp(';
	$signatures[]='locate%20';
	$signatures[]='grep%20';
	$signatures[]='locate(';
	$signatures[]='grep(';
	$signatures[]='diff%20';
	$signatures[]='kill%20';
	$signatures[]='kill(';
	$signatures[]='killall';
	$signatures[]='passwd%20';
	$signatures[]='%20passwd';
	$signatures[]='passwd(';
	$signatures[]='telnet%20';
	$signatures[]='vi(';
	$signatures[]='vi%20';
	$signatures[]='insert%20into';
	$signatures[]='select%20';
	$signatures[]='select+';
	$signatures[]='SELECT+';
	$signatures[]='nigga(';
	$signatures[]='%20nigga';
	$signatures[]='nigga%20';
	$signatures[]='fopen';
	$signatures[]='fwrite';
	$signatures[]='%20like';
	$signatures[]='like%20';
	$signatures[]='$_REQUEST';
	$signatures[]='$_GET';
	$signatures[]='$REQUEST';
	$signatures[]='$GET';
	$signatures[]='.system';
	$signatures[]='HTTP_PHP';
	$signatures[]='&amp;aim';
	$signatures[]='%20getenv';
	$signatures[]='getenv%20';
	$signatures[]='new_password';
	$signatures[]='&amp;icq';
	$signatures[]='/etc/password';
	$signatures[]='/etc/shadow';
	$signatures[]='/etc/groups';
	$signatures[]='/etc/gshadow';
	$signatures[]='/etc/motd';
	$signatures[]='HTTP_USER_AGENT';
	$signatures[]='HTTP_HOST';
	$signatures[]='/bin/ps';
	$signatures[]='wget%20';
	$signatures[]='uname\x20-a';
	$signatures[]='uname%20-a';
	$signatures[]='uname%20-a';
	$signatures[]='/usr/bin/id';
	$signatures[]='/bin/echo';
	$signatures[]='/bin/kill';
	$signatures[]='/bin/';
	$signatures[]='/sbin/';
	$signatures[]='/usr/sbin';
	$signatures[]='/chgrp';
	$signatures[]='/chown';
	$signatures[]='/usr/bin';
	$signatures[]='g\+\+';
	$signatures[]='bin/python';
	$signatures[]='bin/tclsh';
	$signatures[]='bin/nasm';
	$signatures[]='perl%20';
	$signatures[]='traceroute%20';
	$signatures[]='ping%20';
	$signatures[]='.pl';
	$signatures[]='/usr/X11R6/bin/xterm';
	$signatures[]='lsof%20';
	$signatures[]='/bin/mail';
	$signatures[]='.conf';
	$signatures[]='motd%20';
	$signatures[]='HTTP/1.';
	$signatures[]='.inc.php';
	$signatures[]='config.php';
	$signatures[]='cgi-';
	$signatures[]='.eml';
	$signatures[]='file\://';
	$signatures[]='file://';
	$signatures[]='window.open';
	$signatures[]='javascript\://';
	$signatures[]='ijavascript://';
	$signatures[]='javascript:document.cookie=';
	$signatures[]='mg%20src';
	$signatures[]='img%20src';
	$signatures[]='ftp.exe';
	$signatures[]='xp_enumdsn';
	$signatures[]='xp_availablemedia';
	$signatures[]='xp_filelist';
	$signatures[]='xp_cmdshell';
	$signatures[]='nc.exe';
	$signatures[]='.htpasswd';
	$signatures[]='servlet';
	$signatures[]='/etc/passwd';
	$signatures[]='wwwacl';
	$signatures[]='~root';
	$signatures[]='~ftp';
	$signatures[]='.history';
	$signatures[]='bash_history';
	$signatures[]='.bash_history';
	$signatures[]='~nobody';
	$signatures[]='server-info';
	$signatures[]='server-status';
	$signatures[]='reboot%20';
	$signatures[]='halt%20';
	$signatures[]='powerdown%20';
	$signatures[]='/home/ftp';
	$signatures[]='/home/www';
	$signatures[]='secure_site,ok';
	$signatures[]='chunked';
	$signatures[]='org.apache';
	$signatures[]='/servlet/con';
	$signatures[]='&lt;script';
	$signatures[]='/perl';
	$signatures[]='mod_gzip_status';
	$signatures[]='db_mysql.inc';
	$signatures[]='.inc';
	$signatures[]='select%20from';
	$signatures[]='select from';
	$signatures[]='drop%20';
	$signatures[]='.system';
	$signatures[]='getenv';
	$signatures[]='phpinfo()';
	$signatures[]='&lt;?php';
	$signatures[]='?&gt;';
	$signatures[]='sql=';
	$signatures[]='%2527';
	$signatures[]='&lt;br';
	$signatures[]='cc:';
	$signatures[]='bcc:';
	$signatures[]='admin%27--';
	$signatures[]='%27%20or%200=0%20--';
	$signatures[]='&quot;%20or%200=0%20--';
	$signatures[]='or%200=0%20--';
	$signatures[]='%27%20or%200=0%20#';
	$signatures[]='&quot;%20or%200=0%20#';
	$signatures[]='or%200=0%20#';
	$signatures[]='%27%20or%20%27x%27=%27x';
	$signatures[]='&quot;%20or%20&quot;x&quot;=&quot;x';
	$signatures[]='%27)%20or%20(%27x%27=%27x';
	$signatures[]='%27%20or%201=1--';
	$signatures[]='&quot;%20or%201=1--';
	$signatures[]='or%201=1--';
	$signatures[]='%27%20or%20a=a--';
	$signatures[]='&quot;%20or%20&quot;a&quot;=&quot;a';
	$signatures[]='%27)%20or%20(%27a%27=%27a';
	$signatures[]='&quot;)%20or%20(&quot;a&quot;=&quot;a';
	$signatures[]='hi&quot;%20or%20&quot;a&quot;=&quot;a';
	$signatures[]='hi&quot;%20or%201=1%20--';
	$signatures[]='hi%27%20or%201=1%20--';
	$signatures[]='hi%27%20or%20%27a%27=%27a';
	$signatures[]='hi%27)%20or%20(%27a%27=%27a';
	$signatures[]='hi&quot;)%20or%20(&quot;a&quot;=&quot;a';
	$signatures[]='baseDir=';
	$signatures[]='c99shell';
	$signatures[]='c99.txt';
	$signatures[]='c99.php';
	$signatures[]='r57shell';
	$signatures[]='r57.txt';
	$signatures[]='r57.php';
	$signatures[]='crystalshell';
	$signatures[]='phpshell';
	$signatures[]='dtool';
	$signatures[]='fetch%20';
	$signatures[]='curl%20';
	$signatures[]='lynx%20';
	$signatures[]='ls%20-';
	$signatures[]='ls%20-al';
	$signatures[]='/var/tmp';
	$signatures[]='cd%20';
	$signatures[]='$_SERVER';
	$signatures[]='$SERVER';
	$signatures[]='$_POST';
	$signatures[]='$POST';
	$signatures[]='rundll32';
	$signatures[]='PHP_SELF';
	$signatures[]='&lt;iframe';
	$signatures[]='<iframe';
	$signatures[]='mosConfig_absolute_path=';
	$signatures[]='mosConfig_live_site=';
	$signatures[]='.txt?';
	$signatures[]='.log?';
	$signatures[]='.logs?';
	$signatures[]='.jpg?';
	$signatures[]='.png?';
	$signatures[]='.gif?';
	$signatures[]='/alb?';
	$signatures[]='/di??';
	$signatures[]='.sys';
	$signatures[]='/alb??';
	$signatures[]='/tt??';
	$signatures[]='.ico??';
	$signatures[]='/pop??';
	$signatures[]='/idscan9??';
	$signatures[]='/idscan3??';
	$signatures[]='/java???';
	$signatures[]='/a4?';
	$signatures[]='??';
	$signatures[]='.pdf??';
	$signatures[]='id?';
	$signatures[]='qte_web_path=';
	$signatures[]='qte_root=';
	$signatures[]='substring(@@version,1,1)';
	$signatures[]='.cn';
	$signatures[]='config[root_dir]=';
	$signatures[]='proc/self/environ';
	$signatures[]='proc/self/';
	$signatures[]='file_upload.php?sbp=';
	$signatures[]='lm_absolute_path=';
	$signatures[]='1+and+1';
	$signatures[]='1%20and%201';
	$signatures[]='cropimagedir=';
	$signatures[]='phpbb_root_path=';
	$signatures[]='absolute_path=';
	$signatures[]='base_dir=';
	$signatures[]='cpage=';
	$signatures[]='select%20password%20from%20#__users%20where%20username=';
	$signatures[]='GET$01$2$3$4$5$';
	$signatures[]='/**/union/**/all/**/select/**/';
	$signatures[]='g_pcltar_lib_dir=';
	$signatures[]=':2082/index.html?';
	$signatures[]=':2666/index.html?';
	$signatures[]='Itemid=http';
	$signatures[]='.doc?';
	$signatures[]='Musikas';
	$signatures[]='idx?';
	$signatures[]='/x?';
	$signatures[]='iespana.es';
	$signatures[]='PDT_InterScan_NT';
	$signatures[]='pisem.su';
	$signatures[]='hostinginfive.com';
	$signatures[]='fortunecity.co.uk';
	$signatures[]='Please_Click_on_my_google_ads';
	$signatures[]='owned-nets.blogspot.com';
	$signatures[]='myfamily.yoll.net';
	$signatures[]='hoffsons.piranho.de';
	$signatures[]='wiiman.t35.com';
	$signatures[]='mybabycaleb.chat.ru';
	$signatures[]='laudanskisucksss.chat.ru';
	$signatures[]='204.2.183.2';
	$signatures[]='hissusoeoekiaskwkdehsrfeyare.mail333.su';
	$signatures[]='mypregnancy.thvhosting.net';
	$signatures[]='pointaction.com';
	$signatures[]='chat.ru';
	$signatures[]='belasarteshotel.com.br';
	$signatures[]='land.ru';
	$signatures[]='dominiotemporario.com';
	$signatures[]='telarius.narod.ru';
	$signatures[]='c99madnet';
	$signatures[]='geogral.com';
	$signatures[]='jlginfo.com';
	$signatures[]='fx29id1';
	$signatures[]='xroot.txt';
	$signatures[]='sly8.com';
	$signatures[]='ver1?';
	$signatures[]='suckmydick';
	$signatures[]='<!%20--#include%20virtual';
	$signatures[]='<!%20--#include%20virtual';
	$signatures[]='<?';
	$signatures[]='<?%20';
	$signatures[]='`';
	$signatures[]='cmd.exe?dir';
	$signatures[]='repair\sam';
	$signatures[]='echo%20';
	$signatures[]='ps%20-aux';
	$signatures[]='gcc%20';
	$signatures[]='Xeyes%20';
	$signatures[]='THCOWNZIIS';
	$signatures[]='mysql_query';
	$signatures[]='X5O!P%@AP[4';
	return $signatures;
}
?>