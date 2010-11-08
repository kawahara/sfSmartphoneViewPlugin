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
class sfSmartphoneView extends sfPHPView
{
  protected
    $nameSuffix = '',
    $enableNameSuffix = '';

  public function initialize($context, $moduleName, $actionName, $viewName)
  {
    $this->nameSuffix = sfSmartphoneViewToolKit::getViewNameSuffixFromUA($this, $context, $moduleName, $actionName, $viewName, false);
    if ($this->nameSuffix)
    {
      $this->enableNameSuffix = sfSmartphoneViewToolKit::getViewNameSuffixFromUA($this, $context, $moduleName, $actionName, $viewName);
    }
    parent::initialize($context, $moduleName, $actionName, $viewName.sfInflector::camelize($this->enableNameSuffix));
  }
}
