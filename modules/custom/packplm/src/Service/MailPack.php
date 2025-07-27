<?php

namespace Drupal\packplm\Service;

use Drupal\user\Entity\User;
  
class MailPack {


  private $mailer;
  private $configFactory;

  public function __construct($mailer, $configFactory) {
    $this->mailer = $mailer;
    $this->configFactory = $configFactory;
  }

  public function sendPackTwoRequestMail($pack, $mail) {
    $message = $this->configFactory->get('packplm.admin.professionnel')->get('two_mail_content');
    $subject = $this->configFactory->get('packplm.admin.professionnel')->get('two_mail_subject');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $nodeAuthorId = User::load(\Drupal::currentUser()->id());
    $authorFirstName = $nodeAuthorId->get('field_nom')->getValue()[0]["value"];
    $authorlastName = $nodeAuthorId->get('field_prenom')->getValue()[0]["value"];
    $authorFullName = $authorFirstName . ' ' . $authorlastName;
    $message = str_replace('{name}', $authorFullName, $message);
    $message = str_replace('{pack}', $pack, $message);
    $params = [
      'subject' => $subject,
      'message' => $message,
    ];
    $this->mailer->mail('packplm', 'packplm_two_pack', $mail, $langcode, $params);
  }

  public function sendStatusTwoNotifMail() {
    $address = $this->configFactory->get('packplm.admin.professionnel')->get('req_sent_address');
    $subjectSent = $this->configFactory->get('packplm.admin.professionnel')->get('req_sent_subject');
    $contentSent = $this->configFactory->get('packplm.admin.professionnel')->get('req_sent_content');
    $nodeAuthorId = User::load(\Drupal::currentUser()->id());
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $authorFirstName = $nodeAuthorId->get('field_nom')->getValue()[0]["value"];
    $authorlastName = $nodeAuthorId->get('field_prenom')->getValue()[0]["value"];
    $authorFullName = $authorFirstName . ' ' . $authorlastName;
    //$requestOwnerEmail = $nodeAuthorId->getEmail();
    $params = [
      'subject' => $subjectSent,
      'message' => str_replace('{name}', $authorFullName, $contentSent),
    ];
    $this->mailer->mail('packplm', 'packplm_status_two_pack', $address, $langcode, $params);
  }

  public function sendPackRequestMail($mail) {
    $message = $this->configFactory->get('packplm.admin.management')->get('new_mail_content');
    $subject = $this->configFactory->get('packplm.admin.management')->get('new_mail_subject');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $nodeAuthorId = User::load(\Drupal::currentUser()->id());
    $authorFirstName = $nodeAuthorId->get('field_nom')->getValue()[0]["value"];
    $authorlastName = $nodeAuthorId->get('field_prenom')->getValue()[0]["value"];
    $authorFullName = $authorFirstName . ' ' . $authorlastName;
    $params = [
      'subject' => $subject,
      'message' => str_replace('{name}', $authorFullName, $message),
    ];
    $this->mailer->mail('packplm', 'packplm_new_pack', $mail, $langcode, $params);
  }

  public function sendStatusNotifMail() {
    $address = $this->configFactory->get('packplm.admin.management')->get('request_sent_address');
    $subjectSent = $this->configFactory->get('packplm.admin.management')->get('request_sent_subject');
    $contentSent = $this->configFactory->get('packplm.admin.management')->get('request_sent_content');
    $nodeAuthorId = User::load(\Drupal::currentUser()->id());
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $authorFirstName = $nodeAuthorId->get('field_nom')->getValue()[0]["value"];
    $authorlastName = $nodeAuthorId->get('field_prenom')->getValue()[0]["value"];
    $authorFullName = $authorFirstName . ' ' . $authorlastName;
    //$requestOwnerEmail = $nodeAuthorId->getEmail();
    $params = [
      'subject' => $subjectSent,
      'message' => str_replace('{name}', $authorFullName, $contentSent),
    ];
    $this->mailer->mail('packplm', 'packplm_status_new_pack', $address, $langcode, $params);
  }
}
