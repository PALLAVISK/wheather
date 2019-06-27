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
 *   id = "foot2_block",
 *   admin_label = @Translation("recive updates form"),
 *   category = @Translation("send your email address to get updates related offers")
 * )
 */

class FootBlock extends BlockBase {
    public function blockForm($form, FormStateInterface $form_state) {
        $form['title'] = [  
            '#type' => 'textfield', 
            '#placeholder' => $this->t('Block Title')
          ];
        $form['body'] = [  
            '#type' => 'textfield',  
            '#placeholder' => $this->t('Body')
          ];
         
        return $form;
    }
    public function blockSubmit($form, FormStateInterface $form_state) {  
        $this->setConfigurationValue('title',$form_state->getValue('title'));
        $this->setConfigurationValue('body',$form_state->getValue('body'));
      }
    public function build() {
        $config=$this->getConfiguration();
        $title = ($config['title']);
        $body = ($config['body']);
        $form['email'] = [  
            '#type' => 'textfield', 
            '#placeholder' => t('EMAIL ADDRESS'), 
            '#required' => TRUE
          ];
          print_r($title);
          print_r($body);
        $email = ($config['email']);
        

        
        
        return $form;
    }  
      
}
