<?php // No direct access to this file
defined('_JEXEC') or die;
 
jimport('joomla.form.formrule');
 
class JFormRuleEmail extends JFormRule
{
        protected $regex = '^[\w.-]+(\+[\w.-]+)*@\w+[\w.-]*?\.\w{2,4}$';
}