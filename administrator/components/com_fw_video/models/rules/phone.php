<?php // No direct access to this file
defined('_JEXEC') or die;
 
jimport('joomla.form.formrule');
 
class JFormRulePhone extends JFormRule
{
        protected $regex = '(\(?(\d|(\d[- ]\d))\)?[-. ]?)?(\d\.?\d\.?\d)';
}