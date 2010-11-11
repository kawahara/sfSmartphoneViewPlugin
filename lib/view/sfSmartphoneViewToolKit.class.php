<?php

/**
 * This file is part of the sfSmartphoneViewPlugin.
 * (c) 2010 Shogo Kawahara <kawahara@bucyou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A view that uses to switch the template of smartphone.
 *
 * @package    sfSmartphoneViewPlugin
 * @subpackage view
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class sfSmartphoneViewToolKit
{
  static protected
    $generalSmartphoneSuffix = 'smartphone',
    $smartphoneSuffixes = array(
      '/iPhone|iPod/' => 'i_phone',
      '/Android/'     => 'android',
    );

  static protected function isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, $suffix)
  {
    $config = $context->getConfiguration();

    $templateFile = $actionName.$viewName.sfInflector::camelize($suffix).$view->getExtension();

    if ($config->getTemplateDir($moduleName, $templateFile))
    {
      return true;
    }

    return false;
  }

  static public function getGeneralSmartphoneSuffix()
  {
    return self::$generalSmartphoneSuffix;
  }

  static public function getSmartphoneSuffixes()
  {
    return self::$smartphoneSuffixes;
  }

  static public function checkUA($context, $pattern)
  {
    $pathArray = $context->getRequest()->getPathInfoArray();

    return preg_match($pattern, $pathArray['HTTP_USER_AGENT']);
  }

  static public function getViewNameSuffixFromUA($view, $context, $moduleName, $actionName, $viewName, $checkFile = true)
  {
    $isSmartphone = false;

    foreach (self::$smartphoneSuffixes as $key => $name)
    {
      if (self::checkUA($context, $key))
      {
        $isSmartphone = true;
        if (!$checkFile
          || self::isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, $name)
        )
        {
          return $name;
        }

        break;
      }
    }

    if ($isSmartphone && (
        !$checkFile
        || self::isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, self::$generalSmartphoneSuffix)
    ))
    {
      return self::$generalSmartphoneSuffix;
    }

    return '';
  }

  static public function getViewNameFromUA($view, $context, $moduleName, $actionName, $viewName)
  {
    return $viewName.sfInflector::camelize(self::getViewNameSuffixFromUA($view, $context, $moduleName, $actionName, $viewName));
  }
}
