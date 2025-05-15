<?php

namespace Drupal\amap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class DistributionInscriptionManyForm.
 */
class DistributionInscriptionManyForm extends FormBase {


  public function getFormId() {
    return 'distribution_inscription_many_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $oCurrentUser = \Drupal::currentUser();

    if ($oCurrentUser->hasPermission('add distribution date entities')) {
      $data = [
        '#type' => 'inline_template',
        '#template' => '<input type="submit" id="edit-toggle" name="toggleButton" value="{{ foo }}" class="button-dark">',
        '#context' => ['foo' => $this->t('Toggle')],
      ];
    }
    else {
      $data = '';
    }

    $form['inscriptions'] = [
      '#type' => 'table',
      '#header' => [
        [
          'data' => $data,
          'colspan' => 2,
        ],
        [
          'data' => 'Distribution',
          'colspan' => 3,
        ],
        [
          'data' => 'Référent',
          'colspan' => 2,
        ],
      ],
      '#id' => 'planningofdistributions',
    ];

    $currentDay = date('Y-m-d');
    $currentUserRoles = $oCurrentUser->getRoles();
    $bReferentDistrib = (in_array("referent_of_distribution", $currentUserRoles)) ? TRUE : FALSE;

    if (isset($form_state->getStorage()['toggle'])) {
      $filter = $form_state->getStorage()['toggle'];
    }
    else {
      $filter = '';
      $form_state->set('toggle', '');
    }
    $service = \Drupal::service('listenrolments');
    $service->setFilter($filter);

    $rows = $service->list();
    foreach ($rows as $key => $row) {

      if ($row[0] < $currentDay) {
        $bDisabledD = TRUE;
        $bDisabledX = TRUE;
      }
      else {
        $bDisabledD = (($row[1] == AMAP_AMAPIEN_PER_DISTRIBUTION && !$row[4])) || $row[6] || $row[7];
        $bDisabledX = !$bReferentDistrib || ($row[3] == AMAP_REFERENT_PER_DISTRIBUTION && !$row[4]) || $row[5] || $row[6];
      }
      setlocale(LC_TIME, "fr_FR.UTF8");
      $sDate = DrupalDateTime::createFromTimestamp(strtotime(substr($row[0], 5, 2) . "/" . substr($row[0], 8, 2) . "/" . substr($row[0], 0, 4)), new \DateTimeZone('Europe/Paris'))
        ->format('d/m/Y');

      $form['inscriptions'][$key]['distributiondatelong'] = [
        '#markup' => $sDate,
      ];
      $form['inscriptions'][$key]['distributiondate_id'] = [
        '#type' => 'hidden',
        '#value' => $row[11],
      ];
      $form['inscriptions'][$key]['D'] = [
        '#type' => 'checkbox',
        '#default_value' => $row[5],
        '#attributes' => [
          'id' => 'inscriptions[' . $key . '][' . 'd]',
          'onchange' => 'hasChanged(this)',
        ],
        '#disabled' => $bDisabledD,
      ];
      $form['inscriptions'][$key]['D2'] = [
        '#type' => 'textfield',
        '#size' => 2,
        '#disabled' => TRUE,
        '#default_value' => $row[1],
        '#attributes' => [
          'id' => 'inscriptions[' . $key . '][' . 'd2]',
        ],
        '#suffix' => $row[8],
      ];
      $availableSlots = AMAP_AMAPIEN_PER_DISTRIBUTION - $row[1] + $row[12];
      $options = [];
      switch ($availableSlots) {
        case 5:
          $options[4] = $this->t('I will come with four people');
        case 4:
          $options[3] = $this->t('I will come with three people');
        case 3:
          $options[2] = $this->t('I will come with two people');
        case 2:
          $options[1] = $this->t('I will come with one people');
        case 1:
          $options[0] = $this->t('I will come alone');
          break;
        default:
          break;
      }
      ksort($options);
      $form['inscriptions'][$key]['D3'] = [
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => $row[12] - 1,
        '#attributes' => [
          'id' => 'inscriptions[' . $key . '][' . 'd3]',
          'style' => ['display: none;'],
        ],
      ];

      $form['inscriptions'][$key]['X'] = [
        '#type' => 'checkbox',
        '#default_value' => $row[7],
        '#attributes' => [
          'id' => 'inscriptions[' . $key . '][' . 'x]',
          'onchange' => 'hasChanged(this)',
        ],
        '#disabled' => $bDisabledX,
      ];
      $form['inscriptions'][$key]['X2'] = [
        '#type' => 'textfield',
        '#size' => 2,
        '#disabled' => TRUE,
        '#default_value' => $row[3],
        '#attributes' => [
          'id' => 'inscriptions[' . $key . '][' . 'x2]',
        ],
        '#suffix' => $row[10],
      ];
    }

    $form['referent'] = [
      '#type' => 'hidden',
      '#default_value' => ($bReferentDistrib) ? "Y" : "N",
    ];
    $form['referent']['#attributes']['id'] = 'breferentdistrib';

    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#attached']['library'][] = 'amap/planning-of-distributions';

    $form['#attached']['drupalSettings']['myConstants'] = [
      'nbmaxD' => AMAP_AMAPIEN_PER_DISTRIBUTION,
      'nbmaxX' => AMAP_REFERENT_PER_DISTRIBUTION,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $input = $form_state->getUserInput();
    if (isset($input['toggleButton'])) {
      $toggle = ($form_state->getStorage()['toggle'] == 'all') ? '' : 'all';
      $form_state->set('toggle', $toggle);
      $form_state->setRebuild();
    }
    else {

      $iCurrentUserId = \Drupal::currentUser()->id();
      $iNextWed = $form_state->getValue('inscriptions')[0]['distributiondate_id'];

      $query = \Drupal::database()->delete('distribution_inscription');
      $query->condition('amapien_id', $iCurrentUserId);
      $query->condition('distributiondate_id', $iNextWed, '>=');
      $query->execute();

      foreach ($form_state->getValue('inscriptions') as $key => $value) {
        foreach ($value as $key2 => $value2) {
          switch ($key2) {
            case 'distributiondate_id':
              $distributiondate_id = $value2;
              break;
            case 'D':
            case 'X':
              if ($value2 == 1) {
                $data = [
                  'distributiondate_id' => $distributiondate_id,
                  'amapien_id' => $iCurrentUserId,
                  'role' => $key2,
                ];
                for ($i = 0; $i < $value['D3'] + 1; $i++) {
                  $entity = \Drupal::entityTypeManager()
                    ->getStorage('distribution_inscription')
                    ->create($data);
                  $entity->save();
                }
              }
              break;
            default:
          }
        }
      }

      \Drupal::messenger()
        ->addMessage($this->t('The changes have been saved.'));

    }
  }

}



