<?php

namespace Drupal\amap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DistributionInscriptionOneForm.
 */
class DistributionInscriptionOneForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'distribution_inscription_one_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $rows = \Drupal::service('listenrolments')->list();

    $form['rows'] = [
      '#type' => 'value',
      '#value' => $rows,
    ];

    $options = [];
    foreach ($rows as $key => $row) {
      $options[$key] = $row[0];
    }
    $date = $form_state->getValue('distributiondate');
    if ($date !== NULL) {
      $key = $date;
    }
    else {
      $key = 1;
    }
    $row = $rows[$key];

    $currentDay = date('Y-m-d');
    $currentUserRoles = \Drupal::currentUser()->getRoles();
    $bReferentDistrib = (in_array("referent_of_distribution", $currentUserRoles)) ? TRUE : FALSE;
    if ($row[0] < $currentDay) {
      $bDisabledD = TRUE;
      $bDisabledR = TRUE;
      $bDisabledX = TRUE;
    }
    else {
      $bDisabledD = (($row[1] == AMAP_AMAPIEN_PER_DISTRIBUTION && !$row[4])) || $row[6] || $row[7];
      $bDisabledR = (($row[2] == AMAP_RESERVE_PER_DISTRIBUTION && !$row[4])) || $row[5] || $row[7];
      $bDisabledX = !$bReferentDistrib || ($row[3] == AMAP_REFERENT_PER_DISTRIBUTION && !$row[4]) || $row[5] || $row[6];
    }

    $form['distributiondate'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Distribution Date'),
      '#required' => TRUE,
      '#default_value' => $key,
      '#ajax' => [
        'callback' => '::dateCallback',
        'wrapper' => 'inscriptions',
      ],
    ];

    $form['inscriptions'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'inscriptions'],
    ];

    $form['inscriptions']['distributiondate_id'] = [
      '#type' => 'hidden',
      '#value' => $row[11],
    ];

    $form['inscriptions']['D'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Distribution'),
      '#title_display' => 'before',
      '#value' => $row[5],
      '#attributes' => [
        'onchange' => 'hasChanged(this)',
        'id' => 'inscriptions[' . $key . '][' . 'd]',
      ],
      '#disabled' => $bDisabledD,
    ];
    $form['inscriptions']['D2'] = [
      '#type' => 'textfield',
      '#size' => 2,
      '#disabled' => TRUE,
      '#value' => $row[1],
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
    $form['inscriptions']['D3'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $row[12] - 1,
      '#attributes' => [
        'id' => 'inscriptions[' . $key . '][' . 'd3]',
        'style' => ['display: none;'],
      ],
    ];

    $form['inscriptions']['X'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Referent'),
      '#title_display' => 'before',
      '#value' => $row[7],
      '#attributes' => [
        'onchange' => 'hasChanged(this)',
        'id' => 'inscriptions[' . $key . '][' . 'x]',
      ],
      '#disabled' => $bDisabledX,
    ];
    $form['inscriptions']['X2'] = [
      '#type' => 'textfield',
      '#size' => 2,
      '#disabled' => TRUE,
      '#value' => $row[3],
      '#attributes' => [
        'id' => 'inscriptions[' . $key . '][' . 'x2]',
      ],
      '#suffix' => $row[10],
    ];

    $form['referent'] = [
      '#type' => 'hidden',
      '#default_value' => ($bReferentDistrib) ? "Y" : "N",
      '#attributes' => [
        'id' => 'breferentdistrib',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $form['#attached']['library'][] = 'amap/planning-of-distributions';

    $form['#attached']['drupalSettings']['myConstants'] = [
      'nbmaxD' => AMAP_AMAPIEN_PER_DISTRIBUTION,
      'nbmaxR' => AMAP_RESERVE_PER_DISTRIBUTION,
      'nbmaxX' => AMAP_REFERENT_PER_DISTRIBUTION,
    ];

    return $form;

  }

  public function dateCallback($form, FormStateInterface $form_state) {
    return $form['inscriptions'];
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

    $iCurrentUserId = \Drupal::currentUser()->id();
    $distributiondate_id = $form_state->getUserInput()['distributiondate_id'];

    \Drupal::database()
      ->delete('distribution_inscription')
      ->condition('amapien_id', $iCurrentUserId)
      ->condition('distributiondate_id', $distributiondate_id)
      ->execute();

    foreach ($form_state->getUserInput() as $key => $value) {
      switch ($key) {
        case 'distributiondate_id':
          //        $distributiondate_id = $value; Nothing to do: already set
          break;
        case 'D':
        case 'X':
          if ($value == 1) {
            $data = [
              'distributiondate_id' => $distributiondate_id,
              'amapien_id' => $iCurrentUserId,
              'role' => $key,
            ];
            for ($i = 0; $i < $form_state->getValue('D3') + 1; $i++) {
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
    \Drupal::messenger()->addMessage($this->t('The changes have been saved.'));

  }

}
