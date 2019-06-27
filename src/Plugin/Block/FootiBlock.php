<?php

namespace Drupal\wheather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides a 'form for receive updates' block.
 *
 * @Block(
 *   id = "footi_block",
 *   admin_label = @Translation("get updates form"),
 *   category = @Translation("send your email address to get updates related offers")
 * )
 */
class FootiBlock extends BlockBase {
    public function blockForm($form, FormStateInterface $form_state) {
       
        return $form;
    }
    public function blockSubmit($form, FormStateInterface $form_state) {  
        $c = $form_state->getValues();
       
      }
    public function build() {
        $configs=$this->getConfiguration();
        $form['title'] = [  
            '#type' => 'label',   
            '#title' => $this->t('Newsletter'),
          ];
        $form['body'] = [  
            '#type' => 'label',   
            '#title' => $this->t('Subscribe to our mailing list to receive new updates and special offers:'),
          ];  
        $form['email'] = [  
            '#type' => 'textfield', 
            '#placeholder' => t('EMAIL ADDRESS'), 
            '#required' => TRUE
          ]; 
        return $form;
    }  
      
}
