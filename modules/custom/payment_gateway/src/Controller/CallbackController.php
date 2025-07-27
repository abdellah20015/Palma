<?php

namespace Drupal\payment_gateway\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class CallbackController.
 */
class CallbackController extends ControllerBase {

  /**
   * Callback.
   *
   * @return string
   *   Return Hello string.
   */
  public function Callback() {
    header('Access-Control-Allow-Origin: *');  
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

    $storeKey = "Pal912IMQL6G";
    $text="";
    $postParams = array();
    foreach ($_POST as $key => $value){
      array_push($postParams, $key);        
    }
  
    natcasesort($postParams);   
    $hach = "";
    $hashval = "";          
    foreach ($postParams as $param){        
      $paramValue = trim(html_entity_decode($_POST[$param], ENT_QUOTES, 'UTF-8')); 
      $hach = $hach . "(!".$param."!:!".$_POST[$param]."!)";
      $escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue)); 
        
      $lowerParam = strtolower($param);
      if($lowerParam != "hash" && $lowerParam != "encoding" ) {
        $hashval = $hashval . $escapedParamValue . "|";
      }
    }
  
    $escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey)); 
    $hashval = $hashval . $escapedStoreKey;
    $calculatedHashValue = hash('sha512', $hashval);  
    $actualHash = base64_encode (pack('H*',$calculatedHashValue));
    $retrievedHash = $_POST["HASH"];
    if($retrievedHash == $actualHash && $_POST["ProcReturnCode"] == "00" )  {
      //  "Il faut absolument verifier toutes les informations envoyées par MTC (requete server-to-server) avec les données du site avant de procéder à la confirmation de la transaction!"
      //  "Par exemple le montant envoyé dans la requête de MTC doit correspondre exactement au montant de la commande enregistré dans la BDD du site marchand.
      //  "Mettre à jour la base de données du site marchand en vérifiant si la commande existe et correspond au retour MTC!"
      //  "Dans cette MAJ, il faut enregistrer le n° du Bon de commande de paiement envoyé dans le paramètre ""orderNumber"" "
      $user_id = explode('-', $_POST['oid']);
      $user = User::load($user_id['0']);
      $solde = $user->field_credit->value;
      $amount = isset( $_POST['amount'] ) ? $_POST['amount'] : 0;
      $solde = $solde + $amount;
      $user->set('field_credit', $solde);
      $user->save();
      $text = "ACTION=POSTAUTH";
    }else{
        $text = "APPROVED";
    }
    return new Response($text);
  }
}
