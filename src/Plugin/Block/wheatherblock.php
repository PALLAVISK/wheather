<?php
namespace Drupal\wheather\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Component\Serialization\Json;
use Drupal\wheather\WheatherService;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
* Provides a weather_custom_block with a simple text.
*
* @Block(
*   id = "weather_custom_block",
*   admin_label = @Translation("wheather block"),
* )
*/

//implements ContainerFactoryPluginInterface
class WheatherBlock extends BlockBase implements ContainerFactoryPluginInterface{

  protected $wheather_service;

   /**
   * {@inheritdoc}
   */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, WheatherService $wheather_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->wheather_servcie = $wheather_service;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
      return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('wheather.client')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['city'] = array(
      '#type' => 'textfield',
      '#title' => t('city name:'),
          );
    $form['description'] = array(
      '#type' => 'textarea',
      '#title' => t('city desc:'),
         );
    $form['image'] = array(
      '#type' => 'managed_file',
      '#upload_location' => 'public://upload/pal',
      '#name' => 'city_pic',
      '#title' => t('city img:'),
      '#upload_validators' => [
        'file_validate_extensions' => ['jpeg' , 'jpg' , 'gif' , 'png']
      ],
      '#default_value' => isset($this->configuration['image']) ? $this->configuration['image'] : '',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('city', $form_state->getValue('city'));
    $this->setConfigurationValue('description', $form_state->getValue('description'));
    $this->setConfigurationValue('image', $form_state->getValue('image'));

    $image =$form_state->getValue('image');
    $file = \Drupal\file\Entity\File::load($image[0]);
    $file->setPermanent();
    $file->save();

    $this->setConfigurationValue('image', $form_state->getValue('image'));
  }
  
  /**
   * {@inheritdoc}
   */
  public function build() {
   

    $config = $this->getConfiguration();
    $confi=\Drupal::config('wheather.settings');
    // kint($config);
    // $confi=$this->wheather_service->getConfiguration('wheather.settings');
    // kint($confi);
    $appid=$confi->get('app');
    $appid=$confi->get('simple.id');
    $serv = \Drupal::service('wheather.client');
    $serviCall=$serv->test($city);
    // $serviCall=$this->wheather_service->getData($city);

    $jsonObj=Json::decode($serviCall);
    $city_name = isset($config['city']) ? $config['city'] : '';
    $description = isset($config['description']) ? $config['description'] : '';
    $city_img = isset($config['image']) ? $config['image'] : '';
    $image = \Drupal\file\Entity\File::load($city_img[0]);
    $image = $image->uri->value;

    
    return array(
      '#theme' => 'WheatherBlock',
      '#type' => 'markup',
      '#titles' => $city_name,
      '#temp_minimum' => $jsonObj['main']['temp_min'] ,
      '#temp_maximum' => $jsonObj['main']['temp_max'] ,
      '#pressure' => $jsonObj['main']['pressure'] ,
      '#humidity' => $jsonObj['main']['humidity'] ,
      '#wind' => $jsonObj['wind']['speed'] ,
      '#description' => $description,
      '#image' => $image
    );
  }
   
}