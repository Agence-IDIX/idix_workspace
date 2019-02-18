<?php
/**
 * Created by PhpStorm.
 * User: sylvaint
 * Date: 05/11/2018
 * Time: 17:09
 */

namespace Drupal\idix_workspace\Controller;


class SystemController extends \Drupal\system\Controller\SystemController {
  public function themesPage() {
    $build = parent::themesPage();

    $workspace_theme_options = [];

    $themes = $this->themeHandler->rebuildThemeData();

    foreach ($themes as &$theme) {
      $workspace_theme_options[$theme->getName()] = $theme->info['name'];
    }
    $build[] = $this->formBuilder->getForm('Drupal\idix_workspace\Form\ThemeWorkspaceForm', $workspace_theme_options);

    return $build;
  }
}