<?php
/**
 * Created by PhpStorm.
 * User: sylvaint
 * Date: 05/11/2018
 * Time: 15:09
 */

namespace Drupal\idix_workspace\Override;


use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

class ConfigAdminThemeOverride implements ConfigFactoryOverrideInterface {

  public function loadOverrides($names) {
    $user = \Drupal::currentUser();

    $overrides = array();
    if (in_array('system.theme', $names) && $user->hasPermission('view workspace theme') && $user->id() != 1) {
      $config = \Drupal::configFactory()->getEditable('system.theme');
      $overrides['system.theme'] = ['admin' => $config->get('workspace')];
    }
    return $overrides;
  }

  public function getCacheSuffix() {
    return 'ConfigAdminThemeOverride';
  }

  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }
}