<?php

namespace Drupal\idix_workspace\Access;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;

class ViewAllUnpublishedAccessCheck implements AccessInterface {

  public function access(AccountInterface $account){
    return ($account->hasPermission('view all unpublished content') ? AccessResult::allowed() : AccessResult::neutral());
  }

}

