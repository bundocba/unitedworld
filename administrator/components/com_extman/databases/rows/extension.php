<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanDatabaseRowExtension extends KDatabaseRowTable implements KServiceInstantiatable
{
   	public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        $identifier = clone $config->service_identifier;
        if ($config->data && $config->data->identifier) {
        	$id = new KServiceIdentifier($config->data->identifier);
        	if ($id->type === 'com') {
        		$identifier->package = $id->package;
        	}
        }

        $identifier = $container->getIdentifier($identifier);

        if($identifier->classname != 'KDatabaseRowDefault' && class_exists($identifier->classname)) {
            $classname = $identifier->classname;
        } else {
            $classname = 'ComExtmanDatabaseRowExtension';
        }

        $instance = new $classname($config);
        return $instance;
    }

	public function setData($data, $modified = true)
	{
		$result = parent::setData($data, $modified);

		if (isset($data['package']) || isset($data['manifest'])) {
			$this->setup();
		}

		return $result;
	}

	public function __set($column, $value)
	{
		parent::__set($column, $value);

		if (in_array($column, array('package', 'manifest'))) {
			$this->setup();
		}
	}

	public function __get($column)
	{
		if ($column == 'dependents') {
			return $this->getService('com://admin/extman.model.dependencies')->extman_extension_id($this->id)->getList();
		}

		if ($column == 'children') {
			return $this->getService('com://admin/extman.model.extensions')->parent_id($this->id)->getList();
		}

		return parent::__get($column);
	}

	public function setup()
	{
		if (!$this->installer) {
			$this->installer = new ComExtmanInstaller();
		}

		if ($this->package)
		{
			$this->installer->setPath('source', $this->package);
			$this->installer->getManifest();
			$this->manifest_path = $this->installer->getPath('manifest');

			if (!$this->manifest_path || !file_exists($this->manifest_path)) {
				$this->setStatusMessage('Manifest not found');
				return false;
			}
			$this->manifest = simplexml_load_file($this->manifest_path);
		}

		if ($this->manifest && !is_string($this->manifest))
		{
			/* can't use $this->identifier here because __set does string comparison
			 * and doesn't convert the object to string
			 */
			try {
				$this->_data['identifier'] = new KServiceIdentifier((string)$this->manifest->identifier);
			} catch (KServiceIdentifierException $e) {
				$this->setStatusMessage('Invalid identifier in the manifest');
				return false;
			}

			$existing = $this->getTable()->select(array('identifier' => (string)$this->identifier), KDatabase::FETCH_FIELD);

			$this->_new    = !$existing;
			$this->id      = $existing;
			$this->type    = $this->detectType();
			$this->name    = (string) $this->manifest->name;
			$this->version = (string) $this->manifest->version;
		}
	}

	public function detectType()
	{
		static $type_map = array(
			'com' => 'component',
			'mod' => 'module',
			'plg' => 'plugin'
		);

		$type = isset($type_map[(string)$this->identifier->type]) ? $type_map[(string)$this->identifier->type] : 'component';

		return $type;
	}

	public function save()
	{
		$result = true;

		if ($this->package)
		{
			$result = $this->installer->install($this->package);

			if ($result)
			{
				if (is_string($this->identifier)) {
					$this->_data['identifier'] = new KServiceIdentifier($this->identifier);
				}

				$this->joomla_extension_id = $this->getExtensionId(array(
					'type' => $this->type,
					'element' => $this->type == 'plugin' ? $this->identifier->name : $this->identifier->package,
					'folder' => $this->type == 'plugin' ? $this->identifier->package : '',
					'client_id' => $this->identifier->application === 'admin' || $this->type == 'component' ? 1 : 0
				));
			}
			else
			{
				$this->setStatusMessage($this->installer->getError());
				$this->setStatus(KDatabase::STATUS_FAILED);
			}
		}

		if ($result && array_intersect($this->getModified(), array_keys($this->getTable()->getColumns())))
		{
			if ($this->joomla_extension_id)
			{
				$this->setCoreExtension(true);
				if ($this->type === 'plugin')
				{
					if (version_compare(JVERSION, '1.6.0', 'ge')) {
						$query = 'UPDATE #__extensions SET enabled = 1 WHERE extension_id = '.(int)$this->joomla_extension_id;
					} else {
						$query = 'UPDATE #__plugins SET published = 1 WHERE id = '.(int)$this->joomla_extension_id;
					}

					$db = JFactory::getDBO();
					$db->setQuery($query);
					$db->query();
				}
			}
			parent::save();
		}

		if ($result && $this->parent_id)
		{
			$data = array(
				'extman_extension_id' => $this->parent_id,
				'dependent_id' => $this->id
			);

			$row = $this->getService('com://admin/extman.model.dependencies')->set($data)->getItem();
			if ($row->isNew()) {
				$row->setData($data)->save();
			}
		}

		if ($result && $this->manifest)
		{
			if (is_string($this->manifest)) {
				$this->manifest = simplexml_load_string($this->manifest);
			}

			if ($this->manifest->dependencies)
			{
				foreach ($this->manifest->dependencies->dependency as $dependency)
				{
					$row = $this->getService('com://admin/extman.database.row.extension');
					$row->setData(array(
						'package' => $this->source.'/'.(string)$dependency,
						'parent_id' => $this->id
					));
					$row->save();
				}
			}

		}

		return $result;
	}

	public function delete()
	{
		$result = true;

		if ($this->joomla_extension_id)
		{
			$this->setCoreExtension(false);
			if (is_string($this->identifier)) {
				$this->_data['identifier'] = new KServiceIdentifier($this->identifier);
			}
			$client_id = $this->identifier->application === 'admin' || $this->type == 'component' ? 1 : 0;
			$result = $this->installer->uninstall($this->type, $this->joomla_extension_id, $client_id);
		}

		if ($result) {
			$result = parent::delete();
		}

		if ($result)
		{
			// Uninstall dependencies (if they are dependent to this extension only)
			foreach ($this->dependents as $dependency)
			{
				$count = count($this->getService('com://admin/extman.model.dependencies')->dependent_id($dependency->dependent_id)->getList());

				if ($count === 1) {
					$extension = $this->getService('com://admin/extman.model.extensions')->id($dependency->dependent_id)->getItem();
					$extension->delete();
				}

				$dependency->delete();
			}
		}

		return $result;
	}

	public function getExtensionId($extension)
	{
		return ComExtmanInstaller::getExtensionId($extension);
	}

	public function setCoreExtension($value = true)
	{
		$value = (int) $value;
		$db    = JFactory::getDBO();

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query = "UPDATE #__extensions SET protected = {$value}"
				. " WHERE extension_id = ".(int) $this->joomla_extension_id
				. " LIMIT 1";
		}
		else
		{
			$query = "UPDATE #__{$this->type}s SET iscore = {$value}"
				. " WHERE id = ".(int) $this->joomla_extension_id
				. " LIMIT 1";
		}
		$db->setQuery($query);

		return $db->query();
	}
}
