<?php

namespace Drupal\idix_workspace\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManager;

/**
 * Provides a 'TopArticles' block.
 *
 * @Block(
 *  id = "top_articles",
 *  admin_label = @Translation("Top Articles"),
 * )
 */
class TopArticles extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Block\BlockManager definition.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $pluginManagerBlock;
  /**
   * Constructs a new TopArticles object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        BlockManager $plugin_manager_block
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pluginManagerBlock = $plugin_manager_block;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    $build['top_articles'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ol',
      '#wrapper_attributes' => [
        'class' => [
          'wrapper',
        ],
      ],
      '#items' => $this->getAnalyticsContent()
    ];

    return $build;
  }

  private function getAnalyticsContent() {

    $params =  [
      'metrics' => ['ga:pageviews'],
      'dimensions' => ['ga:dimension1'],
//      'dimensions' => ['ga:pagePath'],
      'end_date' => time(),
      'start_date' => strtotime('-1 month'),
      'filters' => 'ga:dimension2==article',
      'max_results' => 10,
      'sort_metric' => ['-ga:pageviews']
    ];

    $feed = google_analytics_reports_api_report_data($params);

    $links = [];

    foreach ($feed->results->rows as $i => $item) {
      $node = Node::load($item['dimension1']);

      if($node) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()] , ['absolute' => TRUE]);
        $link = Link::fromTextAndUrl($node->getTitle(), $url)->toRenderable();
        $l = [
          '#markup' => render($link),
        ];

        $links[] = $l;
      }

    }
    return $links;
  }

}
