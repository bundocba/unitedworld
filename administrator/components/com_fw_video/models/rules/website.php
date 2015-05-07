<?php // No direct access to this file
defined('_JEXEC') or die;
 
jimport('joomla.form.formrule');
 
class JFormRuleWebsite extends JFormRule
{
    protected $regex = '^[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&?\/.=]+$';
}