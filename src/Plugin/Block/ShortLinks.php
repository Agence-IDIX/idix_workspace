<?php

namespace Drupal\idix_workspace\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManager;

/**
 * Provides a 'ShortLinks' block.
 *
 * @Block(
 *  id = "shortlinks",
 *  admin_label = @Translation("Accès rapide"),
 * )
 */
class ShortLinks extends BlockBase implements ContainerFactoryPluginInterface {

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
    $uid = \Drupal::currentUser()->id();

    $build = [];

    $build['shortlinks'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#wrapper_attributes' => [
        'class' => [
          'wrapper',
        ],
      ],
      '#items' => []
    ];

    //animate home page
    $url = Url::fromUri('internal:/node/5/edit');
    $link = Link::fromTextAndUrl('Animer la home' , $url)->toRenderable();
    $l = [
      '#markup' => render($link),
    ];
    $build['shortlinks']['#items'][] = $l;

    //my draft
    $url = Url::fromUri('internal:/admin/liste-des-articles/brouillon/'.$uid);
    $link = Link::fromTextAndUrl('Mes brouillons' , $url)->toRenderable();
    $l = [
      '#markup' => render($link),
    ];

    $build['shortlinks']['#items'][] = $l;


    //add a poll
    $url = Url::fromRoute('poll.poll_add');
    $link = Link::fromTextAndUrl('Créer un sondage' , $url)->toRenderable();
    $l = [
      '#markup' => render($link),
    ];

    $build['shortlinks']['#items'][] = $l;

    //add a newsletter
    $url = Url::fromRoute('node.add' , ['node_type' => 'newsletter']);
    $link = Link::fromTextAndUrl('Créer une newsletter' , $url)->toRenderable();
    $l = [
      '#markup' => render($link),
    ];

    $build['shortlinks']['#items'][] = $l;


    return $build;
  }

}
