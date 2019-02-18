<?php

namespace Drupal\idix_workspace\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManager;

/**
 * Provides a the last activies block.
 *
 * @Block(
 *  id = "last_activities",
 *  admin_label = @Translation("Dernières activités"),
 * )
 */
class LastActivities extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Block\BlockManager definition.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $pluginManagerBlock;
  /**
   * Constructs a new LastActivities object.
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

    $user = User::load(\Drupal::currentUser()->id());

    $build['last_published_articles'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#wrapper_attributes' => [
        'class' => [
          'wrapper',
        ],
      ],
      '#title' => 'Derniers articles publiés',
      '#items' => $this->getContent('article', 3 , 'published' ,$user->id())
    ];

    $build['last_draft_articles'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#wrapper_attributes' => [
        'class' => [
          'wrapper',
        ],
      ],
      '#title' => 'Derniers brouillions',
      '#attributes' => [
        'class' => [
          'wrapper__links',
        ],
      ],
      '#items' =>$this->getContent('article', 3 , 'draft' ,$user->id())
    ];

    return $build;
  }

  private function getContent($type , $nb , $status, $uid = NULL) {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query  = \Drupal::entityQuery('node');
    $query->condition('type', $type);

    if(!is_null($uid)) {
      $query->condition('uid' , $uid);
    }

    if($status == 'published') {
      $query->condition('status', 1);
    }elseif($status == 'draft') {
      $query->condition('field_etape_contenu' , 'brouillon');
    }

    $query->range(0, $nb);

    $results = $query->execute();
    $nodes = $storage->loadMultiple($results);
    $links = [];

    foreach ($nodes as $node) {
      $url = Url::fromRoute('entity.node.edit_form', ['node' => $node->id()]);
      $link = Link::fromTextAndUrl($node->label() , $url)->toRenderable();
      $l = [
        '#markup' => render($link),
//        'attributes' => [
//          'class' => [
//            'wrapper__links__link',
//          ],
//        ],
      ];

      $links[] = $l;
    }
    return $links;
  }

}
