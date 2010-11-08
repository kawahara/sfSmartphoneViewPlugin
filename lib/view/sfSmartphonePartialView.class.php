<?php

/**
 * This file is part of the sfSmartphoneViewPlugin.
 * (c) 2010 Shogo Kawahara <kawahara@bucyou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSmartphonePartialView
 *
 * @package    sfSmartphoneViewPlugin
 * @subpackage view
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class sfSmartphonePartialView extends sfPartialView
{
  public function initialize($context, $moduleName, $actionName, $viewName)
  {
    parent::initialize($context, $moduleName, $actionName, sfSmartphoneViewToolKit::getViewNameFromUA(
      $this, $context, $moduleName, $actionName, $viewName
    ));
  }

  public function configure()
  {
    parent::configure();
    $this->setTemplate($this->actionName.$this->viewName.$this->getExtension());
  }
}
