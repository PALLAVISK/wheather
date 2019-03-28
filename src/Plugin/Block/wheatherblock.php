<?php
namespace Drupal\wheather\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface; 
use Drupal\Component\Serialization\Json;
/**
* Provides a weather_custom_block with a simple text.
*
* @Block(
*   id = "weather_custom_block",
*   admin_label = @Translation("wheather block"),
* )
*/
class wheatherblock extends BlockBase {
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
    $appid=$confi->get('app');

    $serv = \Drupal::service('wheather.client');
    $serviCall=$serv->test($city);
    $jsonObj=Json::decode($serviCall);
    $city_name = isset($config['city']) ? $config['city'] : '';
    $description = isset($config['description']) ? $config['description'] : '';
    $city_img = isset($config['image']) ? $config['image'] : '';
    $image = \Drupal\file\Entity\File::load($city_img[0]);
    $image = $image->uri->value;

    // kint($jsonObj['main']['pressure']);
    // exit();
    return array(
      '#theme' => 'wheatherblock',
      '#type' => 'markup',
      '#titles' => $city_name,
      '#temp_minimum' => $jsonObj['main']['temp_min'] ,
      '#temp_maximum' => $jsonObj['main']['temp_max'] ,
      '#pressure' => $jsonObj['main']['pressure'] ,
      '#humidity' => $jsonObj['main']['humidity'] ,
      '#wind' => $jsonObj['wind']['speed'] ,
      // '#api' => $jsonObj['main'],
      '#description' => $description,
      '#image' => $image
    );
  }
   
}