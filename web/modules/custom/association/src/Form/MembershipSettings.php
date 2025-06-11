<?php

namespace Drupal\association\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * Class MembershipSettings.
 */
class MembershipSettings extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'membership_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = Drupal::config('association.renewalperiod');
    $rpStep = $config->get('step');
    $rpYear = $config->get('year');
    $rpStatus = $config->get('status');
    $rpFirstEmail = $config->get('firstemail');
    $rpReminder = $config->get('reminder');

    if ($rpStep == 0) {
      $iY1 = (int) DrupalDateTime::createFromTimestamp(strtotime("now"), new \DateTimeZone('Europe/Paris'))
        ->format('Y');
      $iY2 = $iY1 + 1;
      $form['actions']['1B'] = [
        '#type' => 'select',
        '#title' => "<BR>1. " . $this->t("Choose year and open renewal period."),
        '#options' => [
          "" => "--",
          $iY1 => $iY1,
          $iY2 => $iY2,
        ],
        '#default_value' => $rpYear,
      ];
    }
    else {
      $form['actions']['1A'] = [
        '#markup' => "<BR>1. " . $this->t('The renewal period has been opened for year « %year ».', ['%year' => $rpYear]),
      ];


      if ($rpStep == 1) {
        $form['actions']['2B'] = [
          '#type' => 'radios',
          '#title' => "2. " . $this->t('Do you want to send the first email?'),
          '#options' => [
            0 => $this->t('No'),
            1 => $this->t('Yes'),
          ],
          '#default_value' => 0,
        ];
      }
      else {
        $form['actions']['2A'] = [
          '#markup' => '<BR>' . "2. " . $this->t('The first email has been sent.'),
        ];
        if ($rpStep == 2) {
        }
        else {
          $form['actions']['3A'] = [
            '#markup' => '<BR>' . "3. " . $this->t('The reminder email has been sent (total number of reminders: %reminders).', ['%reminders' => $rpReminder]),
          ];
        }
        $form['actions']['3B'] = [
          '#type' => 'radios',
          '#title' => "3. " . $this->t('Do you want to send the reminder email?'),
          '#options' => [
            0 => $this->t('No'),
            1 => $this->t('Yes'),
          ],
          '#default_value' => 0,
        ];
      }
      $form['actions']['4B'] = [
        '#type' => 'radios',
        '#title' => $this->t('Do you want to close the renewal period?'),
        '#options' => [
          0 => $this->t('No'),
          1 => $this->t('Yes'),
        ],
        '#default_value' => 0,
      ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#prefix' => '<BR>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $config = Drupal::config('association.renewalperiod');
    $rpStep = $config->get('step');
    switch ($rpStep) {

      case 0:
        if ($form_state->getValue('1B') == "") {
          $form_state->setErrorByName('1A', $this->t('Please choose one option.'));
        }
        break;

    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = Drupal::service('config.factory')
      ->getEditable('association.renewalperiod');
    $rpStep = $config->get('step');
    $rpYear = $config->get('year');
    $rpStatus = $config->get('status');
    $rpFirstEmail = $config->get('firstemail');
    $rpReminder = $config->get('reminder');

    $sTo = Drupal::config('system.site')->get('mail');

    $sMessage = '';
    $sType = 'status';
    switch ($rpStep) {

      case 0:
        // Autoriser les personnes associées aux adhérents à accéder au formulaire
        $role = Role::create(['id' => 'member', 'label' => t('Member')]);
        $role->grantPermission('renew membership');
        $role->save();

        $database = Drupal::database();
        $query = $database->select('person', 'pe');
        $query->fields('pe', ['id', 'member_id ']);
        $query->condition('member_id', 0, '>');
        $ids = $query->execute()->fetchCol(0);
        $users = User::loadMultiple($ids);
        foreach ($users as $user) {
          $user->addRole('member');
          $user->save();
        }

        // Mettre le statut de tous les adhérents actifs à 'Adhésion en attente'
        // et enlever les commentaires éventuels
        $database = Drupal::database();
        $number_updated = $database->update('member')
          ->fields([
            'status' => 2,
            'comment' => '',
          ])
          ->condition('status', 4, '=')
          ->execute();

        $config->set('step', 1);
        $config->set('year', $form_state->getValue('1B'));
        $config->set('status', 'Opened');
        $config->set('firstemail', FALSE);
        $config->set('reminder', 0);
        $config->save();

        drupal_flush_all_caches();

        $sMessage = $this->t('Renew membership: Period has been opened.');
        Drupal::logger('association')
          ->info($sMessage . ' ' . Drupal::translation()
              ->formatPlural($number_updated,
                'One member updated.',
                '@count members updated.'));
        break;

      case 1:
        if ($form_state->getValue('2B') == "1") {
          // Envoyer le premier courriel
          $sRecipients = $this->_setListOfRecipients();
          $aParams = [$sRecipients, $rpYear];
          $message = [
            'module' => 'association',
            'key' => 'membershipfirstemail',
            'to' => 'batch',
            'params' => $aParams,
            'reply' => 'Le Jardin de Poissy',
          ];
          $aResults = shared_send_email($message);
          $config->set('step', 2);
          $config->set('firstemail', TRUE);
          $config->save();
          $sMessage = $this->t('Renew membership: First email has been sent.');
          Drupal::logger('association')
            ->info($sMessage);
        }
        break;

      case 2:
      case 3:
        if ($form_state->getValue('3B') == "1") {
          $config->set('step', 3);
          $rpReminder = $config->get('reminder') + 1;
          $config->set('reminder', $rpReminder);
          // Envoyer un courriel de relance
          $sRecipients = $this->_setListOfRecipients();
          $aParams = [$sRecipients, $rpYear, $rpReminder];
          $message = [
            'module' => 'association',
            'key' => 'membershipreminderemail',
            'to' => 'batch',
            'params' => $aParams,
            'reply' => 'Le Jardin de Poissy',
          ];
          $aResults = shared_send_email($message);
          $config->save();
          $sMessage = $this->t('Renew membership: Reminder email @number has been sent.', ['@number' => $rpReminder]);
          Drupal::logger('association')
            ->info($sMessage);
        }
        break;

      default:
        $sMessage = $this->t('Renew membership: Unexpected case.');
        $sType = 'warning';
    }

    if ($form_state->getValue('4B') == "1") {

      // Enlever l'accès au formulaire aux personnes associées aux adhérents
      $role = Role::load('member');
      $role->delete();

      $config->set('step', 0);
      $config->set('year', '');
      $config->set('status', 'Closed');
      $config->set('firstemail', FALSE);
      $config->set('reminder', 0);
      $config->save();

      $sMessage = $this->t('Renew membership: Period has been closed.');
      Drupal::logger('association')
        ->info($sMessage);

      drupal_flush_all_caches();

    }

    if ($sMessage != '') {
      Drupal::messenger()->addMessage($sMessage, $sType);
    }

  }

  function _setListOfRecipients() {

    $sRecipients = '';
    $database = Drupal::database();
    $query = $database->select('person', 'ap');
    $query->leftJoin('member', 'am', 'ap.member_id = am.id');
    $query->fields('am', ['id', 'status'])->fields('ap', [
      'id',
      'lastname',
      'firstname',
      'email',
    ])->condition('status', 2, '=');
    $results = $query->execute();
    foreach ($results as $key => $result) {
      $sRecipients .= $result->email . ", ";
    }
    $sRecipients = substr($sRecipients, 0, strlen($sRecipients) - 2);
    return $sRecipients;

  }

}
