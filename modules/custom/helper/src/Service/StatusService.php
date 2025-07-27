<?php

namespace Drupal\helper\Service;

use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
 
class StatusService {


  private $mailer;
  private $configFactory;

  public function __construct($mailer, $configFactory) {
    $this->mailer = $mailer;
    $this->configFactory = $configFactory;
  }

  public function sendStatusRequestMail() {
    $message = $this->configFactory->get('helper.admin.emailcompteconfiguration')->get('my_mail_content');
    $subject = $this->configFactory->get('helper.admin.emailcompteconfiguration')->get('my_mail_subject');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    $requestEmail = $user->getEmail();
    $params = [
      'subject' => $subject,
      'message' => $message,
    ];
    $this->mailer->mail('helper', 'helper_my_status',$requestEmail, $langcode, $params);
  }

  public function sendStatusNotifMail($node) {
    $subjectSent = $this->configFactory->get('helper.admin.emailcompteconfiguration')->get('request_sent_subject');
    $contentSent = $this->configFactory->get('helper.admin.emailcompteconfiguration')->get('request_sent_content');
    $nodeAuthorId = User::load(\Drupal::currentUser()->id());
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $authorFirstName = $nodeAuthorId->get('field_nom')->getValue()[0]["value"];
    $authorlastName = $nodeAuthorId->get('field_prenom')->getValue()[0]["value"];
    $authorFullName = $authorFirstName . ' ' . $authorlastName;
    $requestOwnerEmail = $nodeAuthorId->getEmail();
      $params = [
        'subject' => $subjectSent,
        'message' => str_replace('{name}', $contentSent),
      ];
      $this->mailer->mail('helper', 'helper_my_status', $requestOwnerEmail, $langcode, $params);
    
    
  }
 

}
