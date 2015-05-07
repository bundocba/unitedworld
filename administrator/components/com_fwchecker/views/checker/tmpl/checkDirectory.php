<?php

class checkDirectory{
	protected $directories = null;
	function getDirectory() {
		if (is_null($this->directories))
		{
			$this->directories = array();

			$registry = JFactory::getConfig();
			jimport('joomla.filesystem.folder');
			$cparams = JComponentHelper::getParams('com_media');

			checkDirectory::addDirectory('administrator/components', JPATH_ADMINISTRATOR.'/components');
			checkDirectory::addDirectory('administrator/language', JPATH_ADMINISTRATOR.'/language');

			// List all admin languages
			$admin_langs = JFolder::folders(JPATH_ADMINISTRATOR.'/language');
			foreach($admin_langs as $alang) {
				checkDirectory::addDirectory('administrator/language/' . $alang, JPATH_ADMINISTRATOR.'/language/'.$alang);
			}

			// List all manifests folders
			$manifests = JFolder::folders(JPATH_ADMINISTRATOR.'/manifests');
			foreach($manifests as $_manifest) {
				checkDirectory::addDirectory('administrator/manifests/' . $_manifest, JPATH_ADMINISTRATOR.'/manifests/'.$_manifest);
			}

			checkDirectory::addDirectory('administrator/modules', JPATH_ADMINISTRATOR.'/modules');
			checkDirectory::addDirectory('administrator/templates', JPATH_THEMES);

			checkDirectory::addDirectory('components', JPATH_SITE.'/components');

			checkDirectory::addDirectory($cparams->get('image_path'), JPATH_SITE.'/'.$cparams->get('image_path'));

			$image_folders = JFolder::folders(JPATH_SITE.'/'.$cparams->get('image_path'));
			// List all images folders
			foreach ($image_folders as $folder) {
				checkDirectory::addDirectory('images/' . $folder, JPATH_SITE.'/'.$cparams->get('image_path').'/'.$folder);
			}

			checkDirectory::addDirectory('language', JPATH_SITE.'/language');
			// List all site languages
			$site_langs = JFolder::folders(JPATH_SITE . '/language');
			foreach ($site_langs as $slang) {
				checkDirectory::addDirectory('language/' . $slang, JPATH_SITE.'/language/'.$slang);
			}

			checkDirectory::addDirectory('libraries', JPATH_LIBRARIES);

			checkDirectory::addDirectory('media', JPATH_SITE.'/media');
			checkDirectory::addDirectory('modules', JPATH_SITE.'/modules');
			checkDirectory::addDirectory('plugins', JPATH_PLUGINS);

			$plugin_groups = JFolder::folders(JPATH_PLUGINS);
			foreach ($plugin_groups as $folder) {
				checkDirectory::addDirectory('plugins/' . $folder, JPATH_PLUGINS.'/'.$folder);
			}

			checkDirectory::addDirectory('templates', JPATH_SITE.'/templates');
			checkDirectory::addDirectory('configuration.php', JPATH_CONFIGURATION.'/configuration.php');
			checkDirectory::addDirectory('cache', JPATH_SITE.'/cache', 'COM_ADMIN_CACHE_DIRECTORY');
			checkDirectory::addDirectory('administrator/cache', JPATH_CACHE, 'COM_ADMIN_CACHE_DIRECTORY');

			checkDirectory::addDirectory($registry->get('log_path', JPATH_ROOT . '/log'), $registry->get('log_path', JPATH_ROOT.'/log'), 'COM_ADMIN_LOG_DIRECTORY');
			checkDirectory::addDirectory($registry->get('tmp_path', JPATH_ROOT . '/tmp'), $registry->get('tmp_path', JPATH_ROOT.'/tmp'), 'COM_ADMIN_TEMP_DIRECTORY');
		}
		return $this->directories;
	}
	function addDirectory($name, $path, $message = '') {
	    $this->directories[$name] = array('writable' => is_writable($path), 'message' => $message);
	}
}
$_directory=checkDirectory::getDirectory();

foreach ($_directory as $key => $value) {
	$check=false;
	if($value['writable']!=1){
		$check=true;
	}

	if($check==true){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Directory/File
				</td>
				<td>
					<?php echo $key; ?>
				</td>
				<td>
					Unwritable - <a href="index.php?option=com_admin&view=sysinfo" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
	}else{
		if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Directory/File
				</td>
				<td>
					<?php echo $key; ?>
				</td>
				<td>
					Writable
				</td>
            </tr>
<?php
		}
	}
}
