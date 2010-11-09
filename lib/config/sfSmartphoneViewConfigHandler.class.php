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
 * @subpackage config
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class sfSmartphoneViewConfigHandler extends sfViewConfigHandler
{
  /**
   * Execute this configuration handler.
   *
   * @param  array $configFiles
   * @return string
   * @see    sfViewConfigHandler::execute()
   */
  public function execute($configFiles)
  {
    // parse the yaml
    $this->yamlConfig = self::getConfiguration($configFiles);

    // init our data array
    $data = array();

    $generalSmartphoneSuffix = '_'.sfSmartphoneViewToolKit::getGeneralSmartphoneSuffix();

    $data[] = "\$response = \$this->context->getResponse();\n\n";
    $data[] = "\$viewNameSuffix = \$this->enableNameSuffix ? '_'.\$this->enableNameSuffix : '';\n\n";

    $first = true;
    $data[] = "\$un = false;\n";

    if ($this->getConfigValue('use_smartphone_view', ''))
    {
      $data[] = "\$un = true;\n";
    }
    foreach($this->yamlConfig as $viewName => $values)
    {
      if ('all' ==  $viewName || 0 === strpos($viewName, '_'))
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$templateName.\$this->viewName == '$viewName')\n".
                "{\n";
      if ($this->getConfigValue('use_smartphone_view', $viewName))
      {
        $data[] = "  \$un = true;\n";
      }
      else
      {
        $data[] = "  \$un = false;\n";
      }
      $first = false;
      $data[] = "}\n";
    }

    $data[] = "if(\$un)\n{\n";
    $data[] = " \$viewNameSuffix = \$this->nameSuffix ? '_'.\$this->nameSuffix : '';\n";
    $data[] = "}\n";

    $tmp = $this->yamlConfig;
    unset($this->yamlConfig['all']);


    $data[] = "\$o = true;\n";
    if (isset($this->yamlConfig[$generalSmartphoneSuffix]))
    {
      $data[] = "if (\$viewNameSuffix)\n{\n";
      $data[] = "  \$o = false;\n";
      $data[] = $this->addLayout($generalSmartphoneSuffix);
      $data[] = $this->addComponentSlots($generalSmartphoneSuffix);
      $data[] = $this->addHtmlHead($generalSmartphoneSuffix);
      $data[] = $this->addEscaping($generalSmartphoneSuffix);

      $data[] = $this->addHtmlAsset($generalSmartphoneSuffix);

      $data[] = "}\n";
      unset($this->yamlConfig['_'.$generalSmartphoneSuffix]);
    }

    $first = true;
    foreach ($this->yamlConfig as $viewName => $values)
    {
      if ('_another' ==  $viewName || $generalSmartphoneSuffix == $viewName || 0 !== strpos($viewName, '_'))
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$viewNameSuffix == '$viewName')\n".
                "{\n";

      $data[] = $this->addLayout($viewName);
      $data[] = $this->addComponentSlots($viewName);
      $data[] = $this->addHtmlHead($viewName);
      $data[] = $this->addEscaping($viewName);

      $data[] = $this->addHtmlAsset($viewName);

      $data[] = "}\n";

      $first = false;
    }

    if (isset($this->yamlConfig['_another']))
    {
      $data[] = ($first  ? 'if' : 'elseif')."(\$o)\n{\n";

      $data[] = $this->addLayout('_another');
      $data[] = $this->addComponentSlots('_another');
      $data[] = $this->addHtmlHead('_another');
      $data[] = $this->addEscaping('_another');

      $data[] = $this->addHtmlAsset('_another');

      $data[] = "}\n";
    }

    $this->yamlConfig = $tmp;

    // first pass: iterate through all view names to determine the real view name
    $first = true;
    foreach ($this->yamlConfig as $viewName => $values)
    {
      if ('all' == $viewName || 0 === strpos($viewName, '_'))
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$this->actionName.\$this->viewName == '$viewName')\n".
                "{\n";
      $data[] = $this->addTemplate($viewName);
      $data[] = "}\n";

      $first = false;
    }

    // general view configuration
    $data[] = ($first ? '' : "else\n{")."\n";
    $data[] = $this->addTemplate($viewName);
    $data[] = ($first ? '' : "}")."\n\n";

    // second pass: iterate through all real view names
    $first = true;
    foreach ($this->yamlConfig as $viewName => $values)
    {
      if ('all' == $viewName || 0 === strpos($viewName, '_'))
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$templateName.\$this->viewName == '$viewName')\n".
                "{\n";

      $data[] = $this->addLayout($viewName);
      $data[] = $this->addComponentSlots($viewName);
      $data[] = $this->addHtmlHead($viewName);
      $data[] = $this->addEscaping($viewName);

      $data[] = $this->addHtmlAsset($viewName);

      $data[] = "}\n";

      $first = false;
    }

    // general view configuration
    $data[] = ($first ? '' : "else\n{")."\n";

    $data[] = $this->addLayout();
    $data[] = $this->addComponentSlots();
    $data[] = $this->addHtmlHead();
    $data[] = $this->addEscaping();

    $data[] = $this->addHtmlAsset();
    $data[] = ($first ? '' : "}")."\n";

    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by sfSmartphoneViewConfigHandler\n".
                      "// date: %s\n%s\n",
                      date('Y/m/d H:i:s'), implode('', $data));

    return $retval;
  }
}
