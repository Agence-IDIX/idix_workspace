<?php

namespace Drupal\idix_workspace\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManager;

/**
 * Provides a 'KPI' block.
 *
 * @Block(
 *  id = "kpi",
 *  admin_label = @Translation("KPI"),
 * )
 */
class KPI extends BlockBase implements ContainerFactoryPluginInterface {

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

  private $channel_grouping = [];
  private $device_types= [];
  private $page_views_per_session;
  private $avg_session_duration;
  private $newsletter_subsccribers;

  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        BlockManager $plugin_manager_block
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pluginManagerBlock = $plugin_manager_block;

    $this->getKpiDataSource();
    $this->getKpiStats();
    $this->getnewsletterSubscribersNb();
    $this->getKpiDeviceType();
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

    $build['nb_newsletters_subscribers'] = [
      '#markup' => '<div><strong>'. $this->newsletter_subsccribers .'</strong> abonnées à la matinale</div>',
    ];

    $build['pageviewsPerSession'] = [
      '#markup' => '<div><strong>' . $this->page_views_per_session .'</strong> Nombre de pages / visite</div>'
    ];

    $build['avgSessionDuration'] = [
      '#markup' => '<div><strong>' . $this->avg_session_duration .' min</strong> Durée moyenne de session</div>'
    ];

    //channel grouping
    $markup = '<div>';
    foreach ($this->channel_grouping as $source => $session) {
      $markup .= '<p>' . $source . ' : '. $session . '</p>';
    }

    $markup .= '</div>';

    $build['audience_source'] = [
      '#markup' => $markup,
    ];

    //device types
    $markup = '<div>';
    foreach ($this->device_types as $devices => $data) {
      $markup .= '<p>' . $devices . ' : (Taux de rebond) '. $data['bounceRate'] . '% (sessions) '. $data['sessions'] .'</p>';
    }

    $markup .= '</div>';

    $build['device_types'] = [
      '#markup' => $markup,
    ];



    return $build;
  }

  private function getnewsletterSubscribersNb() {
    $this->newsletter_subsccribers = 41908;
  }

  private function getKpiStats () {

    $params =  [
      'metrics' => ['ga:pageviewsPerSession' , 'ga:avgSessionDuration'],
      'end_date' => time(),
      'start_date' => strtotime('-1 week'),
    ];

    $feed = google_analytics_reports_api_report_data($params);

    $this->page_views_per_session = round($feed->results->rows[0]['pageviewsPerSession'],2);
    $this->avg_session_duration = round($feed->results->rows[0]['avgSessionDuration'],2);

  }

  private function getKpiDataSource () {

    $params =  [
      'metrics' => ['ga:sessions'],
      'dimensions' => ['ga:channelGrouping'],
      'sort_metric' => ['ga:channelGrouping'],
      'end_date' => time(),
      'start_date' => strtotime('-1 week'),
    ];

    $feed = google_analytics_reports_api_report_data($params);


    foreach ($feed->results->rows as $item) {
      $this->channel_grouping[$item['channelGrouping']] = $item['sessions'];
    }
  }

  private function getKpiDeviceType () {

    $params =  [
      'metrics' => ['ga:sessions' , 'ga:bounceRate'],
      'dimensions' => ['ga:deviceCategory'],
      'sort_metric' => ['ga:deviceCategory'],
      'end_date' => time(),
      'start_date' => strtotime('-1 week'),
    ];

    $feed = google_analytics_reports_api_report_data($params);


    foreach ($feed->results->rows as $item) {
      $this->device_types[$item['deviceCategory']] = [];
      $this->device_types[$item['deviceCategory']]['bounceRate']  = round($item['bounceRate'] , 2);
      $this->device_types[$item['deviceCategory']]['sessions']  = $item['sessions'];
    }
  }

}
