<?php

namespace Drupal\amap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;

use Drupal\Component\Utility\Bytes;

/**
 * Class ContractSubscriptionTableForm.
 */
class ContractSubscriptionTableForm extends FormBase {


  public function getFormId() {
    return 'contract_subscription_table_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $contract = NULL) {

    //  Détails du contrat
    $oContract = \Drupal::entityTypeManager()
      ->getStorage('contract')
      ->load($contract);
    $sContractIsOpenForSubscription = $oContract->get('isopenforsubscription')
      ->getString();
    $sContractType = $oContract->get('type')->getString();

    $aContractType = _detail_contract_type($sContractType);
    $iNumberOfQuantities = $aContractType[1];
    $aContractTypeHeader = $aContractType[2];

    $upload_max_filesize = Bytes::toNumber(ini_get('upload_max_filesize'));
    $post_max_size = Bytes::toNumber(ini_get('post_max_size'));
    $myMax = 2 * 1024 * 1024;
    $upload_max = min($upload_max_filesize, $post_max_size, $myMax);
    $upload_max_inMB = $upload_max / 1024 / 1024;

    $form['subscriptions'] = [
      '#type' => 'table',
      '#sticky' => TRUE,
      '#responsive' => TRUE,
      '#id' => 'subscriptions',
      '#quantities' => $iNumberOfQuantities,
      '#attributes' => ['style' => 'margin-left: calc(-100vw / 2 + 1170px / 2);',],
    ];

    $form['subscriptions']['#header'] = ['member' => t('Member'),];
    $form['subscriptions']['#header'] = array_merge($form['subscriptions']['#header'], $aContractTypeHeader);
    $aContractStandardHeader = [
      'sharedwith' => t('Shared with'),
      'comment' => t('Comment'),
      'file' => '•••',
      'addRow' => '',
    ];
    $form['subscriptions']['#header'] = array_merge($form['subscriptions']['#header'], $aContractStandardHeader);

    //  Liste des Adhérents pour 'Partagé avec'
    $query_am = \Drupal::database()->select('member', 'am');
    $query_am->fields('am', ['id', 'designation']);
    $query_am->condition('status', [2, 3, 4], 'IN')
      ->orderBy('designation', 'ASC');
    $results_am = $query_am->execute()->fetchAllKeyed();
    $results_am = ["0" => ""] + $results_am;

    //  Liste des Adhérents avec leur souscription éventuelle
    $sTemp1 = "";
    $sTemp2 = "";
    for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
      $sTemp1 .= 'quantity' . sprintf("%02d", $i) . ',';
      $sTemp2 .= "'',";
    }
    $query = "
      SELECT
          am.id as am_id,
          designation,
          sharedwith_member_id,
          cs.comment,
          cs.file__target_id,"
      . $sTemp1 .
      "    cs.id as cs_id
          FROM {member} as am
          LEFT JOIN {contract_subscription} as cs ON member_id = am.id
        WHERE cs.contract_id = " . $contract;

    if ($sContractIsOpenForSubscription) {
      $query .= "
      UNION
          SELECT
          am.id as am_id,
          designation,
          '0',
          '',
          '',"
        . $sTemp2 .
        "    ''
          FROM {member} as am
      WHERE status IN (2, 3, 4) AND designation NOT IN (
      SELECT
          designation
          FROM {member} as am
          LEFT JOIN {contract_subscription} as cs ON member_id = am.id
        WHERE cs.contract_id = " . $contract . "
      )";
    }

    $query .= "
      ORDER BY designation ASC
      ";
    $results = \Drupal::database()->query($query);

    //  Génération des lignes du tableau
    foreach ($results as $key => $value) {
      $myKey = 10 + $key * 10;
      $sharedwith_id = $value->sharedwith_member_id;
      if ($sharedwith_id == "0") {
        $iKey = 0;
      }
      else {
        if (!array_search($sharedwith_id, $results_am)) {
          $results_am = $results_am + [$sharedwith_id => t('Member') . sprintf("%03d", $sharedwith_id)];
        }
        $iKey = array_search($results_am[$sharedwith_id], $results_am);
      }

      $wrapper = 'formWrapper';
      $form['#prefix'] = '<div id="' . $wrapper . '">';
      $form['#suffix'] = '</div>';

      $form['subscriptions'][$myKey]['member'] = ['#markup' => $value->designation];
      $sQuantities4Hash = "";
      for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
        $sField = 'quantity' . sprintf("%02d", $i);
        $default_value = $value->$sField;
        if ($default_value == "" || $default_value == 0) {
          $default_value = "";
        }
        elseif ((int) $default_value == (float) $default_value) {
          $default_value = sprintf("%d", $default_value);
        }
        else {
          $default_value = sprintf("%01.2f", $value->$sField);
        }
        $sQuantities4Hash .= $default_value . "_";
        $form['subscriptions'][$myKey][$sField] = [
          '#type' => 'number',
          '#min' => 0.00,
          '#max' => 99.95,
          '#step' => 0.05,
          '#default_value' => $default_value,
        ];
      }
      $form['subscriptions'][$myKey]['sharedwith'] = [
        '#type' => 'select',
        '#options' => $results_am,
        '#default_value' => $iKey,
      ];
      $form['subscriptions'][$myKey]['comment'] = [
        '#type' => 'textarea',
        '#rows' => 1,
        '#default_value' => $value->comment,
      ];
      $fileId = (int) $value->file__target_id;
      $title = ($fileId == 0) ? '000' : sprintf("%03d", $fileId);
      $form['subscriptions'][$myKey]['filehead'] = [
        '#type' => 'details',
        '#title' => $title,
      ];
      $form['subscriptions'][$myKey]['filehead']['file'] = [
        '#type' => 'managed_file',
        '#description' => $this->t('Limited to @size MB.', ['@size' => $upload_max_inMB]) . '<br>' . $this->t('Allowed types: pdf.'),
        '#upload_location' => 'private://contracts/subscriptions/',
        '#upload_validators' => [
          'file_validate_extensions' => ['pdf'],
          'file_validate_size' => [$upload_max],
        ],
        '#default_value' => [$fileId],
      ];
      if ($sContractIsOpenForSubscription) {
        $form['subscriptions'][$myKey]['addRow'] = [
          '#type' => 'submit',
          '#name' => $myKey,
          '#value' => '+',
          '#ajax' => [
            'callback' => '::ajaxAddRow',
            'wrapper' => $wrapper,
            'progress' => [
              'type' => 'throbber',
              'message' => NULL,
            ],
          ],
        ];
      }
      $form['subscriptions'][$myKey]['am_id'] = [
        '#type' => 'hidden',
        '#default_value' => $value->am_id,
      ];
      $form['subscriptions'][$myKey]['cs_id'] = [
        '#type' => 'hidden',
        '#default_value' => $value->cs_id,
      ];
      $hash = $sQuantities4Hash . $iKey . $value->comment . $fileId;
      $form['subscriptions'][$myKey]['hash'] = [
        '#type' => 'hidden',
        '#default_value' => $hash,
      ];

    }

    if ($sContractIsOpenForSubscription) {
      $form['submit'] = [
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => $this->t('Save'),
        '#ajax' => [
          'wrapper' => $wrapper,
          'callback' => '::ajaxSubmit',
        ],
      ];
    }

    $form['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Leave'),
    ];

    $form['#attached']['library'][] = 'amap/subscriptions';

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()['#name'] == 'cancel') {
      return;
    }
    parent::validateForm($form, $form_state);

  }

  public function ajaxAddRow(array &$form, FormStateInterface $form_state) {
    $myKey = $form_state->getTriggeringElement()['#name'];
    $formBis = [];
    foreach ($form as $key => $value) {
      switch ($key) {
        case 'subscriptions':
          foreach ($form['subscriptions'] as $key2 => $value2) {
            $formBis['subscriptions'][$key2] = $form['subscriptions'][$key2];
            if (is_numeric($key2)) {
              if ($form['subscriptions'][$key2]['filehead']['#title'] == '000') {
                $aTemp = $form['subscriptions'][$key2]['filehead'];
              }
              if ($key2 == $myKey) {
                $keyfornewrow = $key2 + 5;
                $formBis['subscriptions'][$keyfornewrow] = $form['subscriptions'][$key2];
                foreach ($formBis['subscriptions'][$keyfornewrow] as $key3 => $value3) {
                  switch ($key3) {
                    case 'sharedwith':
                      $formBis['subscriptions'][$keyfornewrow][$key3]['#value'] = 0;
                      break;
                    case 'comment':
                      $formBis['subscriptions'][$keyfornewrow][$key3]['#value'] = NULL;
                      break;
                    case 'filehead':
                      break;
                    case 'addRow':
                      $formBis['subscriptions'][$keyfornewrow][$key3] = ['#type' => 'hidden',];
                      break;
                    case 'cs_id':
                      $formBis['subscriptions'][$keyfornewrow][$key3]['#value'] = NULL;
                      break;
                    default:
                      if (substr($key3, 0, 8) == 'quantity') {
                        $formBis['subscriptions'][$keyfornewrow][$key3]['#value'] = NULL;
                      }
                  }
                }
              }
              else {
              }
            }
          }
          $formBis['subscriptions'][$keyfornewrow]['filehead'] = $aTemp;
          break;
        default:
          $formBis[$key] = $form[$key];
      }
    }
    return $formBis;

  }

  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand(Url::fromRoute('amap.contracts')
      ->toString()));
    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()['#name'] == 'submit') {

      $args = $form_state->getBuildInfo()['args'];
      $storage = \Drupal::entityTypeManager()
        ->getStorage('contract_subscription');
      $iNumberOfQuantities = $form['subscriptions']['#quantities'];

      foreach ($form_state->getValue('subscriptions') as $key => $value) {
        $sQuantities = "";
        $sQuantities4Hash = "";
        for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
          $sTemp = 'quantity' . sprintf("%02d", $i);
          $sQuantities .= $value[$sTemp];
          $sQuantities4Hash .= $value[$sTemp] . "_";
        }
        $id = $value['cs_id'];

        if ($id == "") {
          if ($sQuantities != "") {
            $entity = $storage->create();
            $sAction = 'C';
          }
          else {
            $sAction = '0';
          }
        }
        else {
          $entity = $storage->load($id);
          if ($sQuantities != "") {
            $file = $value['filehead']['file'];
            $file = (count($file) == 0) ? '0' : $file[0];
            $hash = $sQuantities4Hash . $value['sharedwith'] . $value['comment'] . $file;
            if ($hash == $value['hash']) {
              $sAction = '0';
            }
            else {
              $sAction = 'M';
            }
          }
          else {
            $sAction = 'S';
          }
        }
        switch ($sAction) {
          case 'C':
          case 'M':
            $entity->contract_id = $args;
            $entity->member_id = $value['am_id'];
            $entity->sharedwith_member_id = $value['sharedwith'];
            $entity->comment = str_replace([
              "\r\n",
              "\n",
              "\r",
            ], " ", $value['comment']);
            $entity->file = $value['filehead']['file'];
            for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
              $sTemp = 'quantity' . sprintf("%02d", $i);
              $entity->$sTemp = $value[$sTemp];
            }
            $entity->save();
            break;
          case 'S':
            $entity->delete();
            break;
          default:
        }
      }

      //    Mise à jour de la liste des AMAPiens
      _setAmapien();

      _export_amap_CSV('amap_contracts_subscriptions', 'rest_export_1', $args[0]);
      \Drupal::messenger()
        ->addMessage($this->t('The changes have been saved.'));

    }
    elseif ($form_state->getTriggeringElement()['#name'] == 'cancel') {
      $form_state->setRedirect('amap.contracts');
    }
    else {
      $form_state->setRebuild();
    }
  }

}
