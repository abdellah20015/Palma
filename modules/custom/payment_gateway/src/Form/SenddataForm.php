<?php

namespace Drupal\payment_gateway\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SenddataForm.
 */
class SenddataForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'senddata_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $storeKey = "Pal912IMQL6G";

      $elementdrupal = ['form_build_id','form_token','form_id','op'];
      $postParams = array();
      foreach ($_POST as $key => $value){
        if (!in_array($key, $elementdrupal )) {
          array_push($postParams, $key);
          $form[$key] = [
            '#type' => 'hidden',
            '#title' => $key,
            '#default_value'=>trim($value),
          ];
        }
        
      }
      
      natcasesort($postParams);   
      
      $hashval = "";          
      foreach ($postParams as $param){        
        $paramValue = trim($_POST[$param]);
        $escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue)); 
          
        $lowerParam = strtolower($param);
        if($lowerParam != "hash" && $lowerParam != "encoding" ) {
          $hashval = $hashval . $escapedParamValue . "|";
        }
      }
      
      $escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey)); 
      $hashval = $hashval . $escapedStoreKey;
      
      $calculatedHashValue = hash('sha512', $hashval);  
      $hash = base64_encode (pack('H*',$calculatedHashValue));
      $form['HASH'] = [
        '#type' => 'hidden',
        '#title' => $this->t('HASH'),
        '#default_value'=> $hash,
      ];
      $form['#action'] = 'https://payment.cmi.co.ma/fim/est3Dgate';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
