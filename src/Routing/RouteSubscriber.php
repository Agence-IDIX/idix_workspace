<?php
namespace Drupal\idix_workspace\Routing;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Created by PhpStorm.
 * User: sylvaint
 * Date: 05/11/2018
 * Time: 16:55
 */

class RouteSubscriber extends RouteSubscriberBase {
  public function alterRoutes(RouteCollection $collection) {
$i = 1;
    if ($route = $collection->get('system.themes_page')) {

      $route->setDefault(
        '_controller',
        'Drupal\idix_workspace\Controller\SystemController::themesPage'
      );
    }

//    if ($route = $collection->get('search.view_node_search')) {
    if ($route = $collection->get('search.view_workspace_content')) {

      $route->setOption(
        '_admin_route',
        'TRUE'
      );
    }
  }


}