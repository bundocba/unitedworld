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

  * PHPIDS specific utility class to convert charsets manually
  *
  * Note that if you make use of IDS_Converter::runAll(), existing class
  * methods will be executed in the same order as they are implemented in the
  * class tree!
  *
  * @category  Security
  * @package   PHPIDS
  * @author    Christian Matthies <ch0012@gmail.com>
  * @author    Mario Heiderich <mario.heiderich@gmail.com>
  * @author    Lars Strojny <lars@strojny.net>
  * @copyright 2007-2009 The PHPIDS Group
  * @license   http://www.gnu.org/licenses/lgpl.html LGPL
  * @version   Release: $Id:Converter.php 517 2007-09-15 15:04:13Z mario $
  * @link      http://php-ids.org/
*/
if (!defined('_JEXEC') && !defined('OSEDS'))
{
	die("Direct Access Not Allowed");
}
class IDS_Filter
{

    /**
     * Filter rule
     *
     * @var    string
     */
    public $rule;

    /**
     * List of tags of the filter
     *
     * @var    array
     */
    protected $tags = array();

    /**
     * Filter impact level
     *
     * @var    integer
     */
    public $impact = 0;

    /**
     * Filter description
     *
     * @var    string
     */
    public $description = null;

    /**
     * Constructor
     *
     * @param mixed   $rule        filter rule
     * @param string  $description filter description
     * @param array   $tags        list of tags
     * @param integer $impact      filter impact level
     *
     * @return void
     */
    public function __construct($id, $rule, $description, array $tags, $impact)
    {
    	$this->id          = $id;
        $this->rule        = $rule;
        $this->tags        = $tags;
        $this->impact      = $impact;
        $this->description = $description;
    }

    /**
     * Matches a string against current filter
     *
     * Matches given string against the filter rule the specific object of this
     * class represents
     *
     * @param string $string the string to match
     *
     * @throws InvalidArgumentException if argument is no string
     * @return boolean
     */
    public function match($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException('
                Invalid argument. Expected a string, received ' . gettype($string)
            );
        }
        return (bool) preg_match('/'.$this->getRule().'/ms', strtolower($string));
    }

    public function replace($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException('
                Invalid argument. Expected a string, received ' . gettype($string)
            );
        }
        return (bool) preg_replace('/'.$this->getRule().'/ms', strtolower($string));
    }
    /**
     * Returns filter description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return list of affected tags
     *
     * Each filter rule is concerned with a certain kind of attack vectors.
     * This method returns those affected kinds.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Returns filter rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Get filter impact level
     *
     * @return integer
     */
    public function getImpact()
    {
        return $this->impact;
    }

    /**
     * Get filter ID
     *
     * @return integer
     */
    public function getId()
    {
    	return $this->id;
    }
}

/**
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 expandtab
 */
