<?php

namespace Drupal\amap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;

use Drupal\Component\Utility\Bytes;

/**
 * Class ContractSubscriptionOneForm.
 */
class ContractSubscriptionOneForm extends FormBase {


  public function getFormId() {
    return 'contract_subscription_one_form';
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
    $aContractTypeHeader = $aContractType[3];

    $upload_max_filesize = Bytes::toNumber(ini_get('upload_max_filesize'));
    $post_max_size = Bytes::toNumber(ini_get('post_max_size'));
    $myMax = 2 * 1024 * 1024;
    $upload_max = min($upload_max_filesize, $post_max_size, $myMax);
    $upload_max_inMB = $upload_max / 1024 / 1024;

    $database = \Drupal::database();

    //  Liste des Adhérents
    $query_am = $database->select('member', 'am');
    $query_am->fields('am', ['id', 'designation']);
    $query_am->condition('status', [2, 3, 4], 'IN')
      ->orderBy('designation', 'ASC');
    $aMembers = $query_am->execute()->fetchAllKeyed();
    $aMembersforsharing = ["0" => ""] + $aMembers;

    $subscription = FALSE;
    $memberId = $form_state->getValue('designation');
    if ($memberId) {
      $query = $database->select('contract_subscription', 'cs')
        ->fields('cs')
        ->condition('contract_id', $contract)
        ->condition('member_id', $memberId);
      $subscription = $query->execute()->fetchAssoc();
    }
    if ($subscription == FALSE) {
      $subscription = [
        'id' => 0,
        'contract_id' => 0,
        'member_id' => 0,
        'sharedwith_member_id' => 0,
        'comment' => '',
        'file__target_id' => 0,
        'file__display' => 0,
        'file__description' => '',
        'quantity01' => 0,
        'quantity02' => 0,
        'quantity03' => 0,
        'quantity04' => 0,
        'quantity05' => 0,
        'quantity06' => 0,
        'quantity07' => 0,
        'quantity08' => 0,
        'quantity09' => 0,
        'quantity10' => 0,
        'quantity11' => 0,
        'quantity12' => 0,
        'quantity13' => 0,
        'quantity14' => 0,
        'quantity15' => 0,
        'quantity16' => 0,
        'quantity17' => 0,
        'quantity18' => 0,
        'quantity19' => 0,
        'quantity20' => 0,
        'quantity21' => 0,
        'quantity22' => 0,
        'quantity23' => 0,
        'quantity24' => 0,
        'quantity25' => 0,
        'quantity26' => 0,
        'quantity27' => 0,
        'quantity28' => 0,
        'quantity29' => 0,
        'quantity30' => 0,
        'quantity31' => 0,
        'quantity32' => 0,
        'quantity33' => 0,
        'quantity34' => 0,
        'quantity35' => 0,
        'quantity36' => 0,
        'owner_id' => 0,
        'created' => 0,
        'changed' => 0,
      ];
    }

    /*
        $wrapper = 'formWrapper';
        $form['#prefix'] = '<div id="' . $wrapper . '">';
        $form['#suffix'] = '</div>';
     */

    $form['designation'] = [
      '#type' => 'select',
      '#options' => $aMembers,
      '#title' => $this->t('Member'),
      '#required' => TRUE,
      '#empty_option' => t('- Select member -'),
      '#default_value' => $memberId,
      '#ajax' => [
        'callback' => '::memberCallback',
        'wrapper' => 'theSubscription',
      ],
    ];

    $form['theSubscription'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'theSubscription'],
      '#quantities' => $iNumberOfQuantities,
    ];

    $sQuantities4Hash = "";
    for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
      $sField = 'quantity' . sprintf("%02d", $i);
      $value = $subscription[$sField];
      if ($value == "" || $value == 0) {
        $value = "";
      }
      elseif ((int) $value == (float) $value) {
        $value = sprintf("%d", $value);
      }
      else {
        $value = sprintf("%01.2f", $subscription[$sField]);
      }
      $sQuantities4Hash .= ($value == "") ? "." : $value;
      $form['theSubscription'][$sField] = [
        '#type' => 'number',
        '#title' => $aContractTypeHeader[$i - 1],
        '#title_display' => 'after',
        '#min' => 0.00,
        '#max' => 99.95,
        '#step' => 0.05,
        '#value' => $value,
        '#wrapper_attributes' => [
          'id' => 'edit-' . $sField,
          'class' => ['container-inline'],
        ],
      ];
    }

    $sharedwith_id = $subscription['sharedwith_member_id'];
    $form['theSubscription']['sharedwith'] = [
      '#type' => 'select',
      '#title' => t('Shared with'),
      '#options' => $aMembersforsharing,
      '#value' => $sharedwith_id,
    ];

    $form['theSubscription']['comment'] = [
      '#type' => 'textarea',
      '#title' => t('Comment'),
      '#rows' => 1,
      '#value' => $subscription['comment'],
    ];

    $fileId = (int) $subscription['file__target_id'];
    $form['theSubscription'][$fileId] = [
      '#type' => 'managed_file',
      '#description' => $this->t('Limited to @size MB.', ['@size' => $upload_max_inMB]) . '<br>' . $this->t('Allowed types: pdf.'),
      '#upload_location' => 'private://contracts/subscriptions/',
      '#upload_validators' => [
        'file_validate_extensions' => ['pdf'],
        'file_validate_size' => [$upload_max],
      ],
      '#default_value' => [$fileId],
    ];

    /*
        if ($sContractIsOpenForSubscription) {
          $form['theSubscription']['addRow'] = [
            '#type' => 'submit',
            '#name' => 'addrow',
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
     */

    $form['theSubscription']['cs_id'] = [
      '#type' => 'hidden',
      '#value' => $subscription['id'],
    ];

    $form['theSubscription']['file_id'] = [
      '#type' => 'hidden',
      '#value' => $fileId,
    ];

    $hash = $sQuantities4Hash . "_" . $sharedwith_id . "_" . $subscription['comment'] . "_" . $fileId;
    $form['theSubscription']['hash'] = [
      '#type' => 'hidden',
      '#value' => $hash,
    ];

    if ($sContractIsOpenForSubscription) {
      $form['save'] = [
        '#type' => 'submit',
        '#name' => 'save',
        '#value' => $this->t('Save'),
      ];
    }

    $form['leave'] = [
      '#type' => 'button',
      '#name' => 'leave',
      '#value' => $this->t('Leave'),
      '#ajax' => [
        'callback' => '::leaveCallback',
      ],
      '#limit_validation_errors' => [],
    ];

    $form['#attached']['library'][] = 'amap/subscriptions';

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getTriggeringElement()['#name'] == 'save') {
      parent::validateForm($form, $form_state);
    }
    return;

  }

  public function memberCallback($form, FormStateInterface $form_state) {

    return $form['theSubscription'];

  }

  public function leaveCallback($form, FormStateInterface $form_state) {

    $response = new AjaxResponse();
    $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand(Url::fromRoute('amap.contracts')
      ->toString()));
    return $response;

  }

  /*
    public function ajaxAddRow(array &$form, FormStateInterface $form_state) {
      $myKey = $form_state->getTriggeringElement()['#name'];
      $formBis = [];
      foreach ($form as $key => $value) {
        switch ($key) {
          case 'theSubscription':
            foreach ($form['theSubscription'] as $key2 => $value2) {
              $formBis['theSubscription'][$key2] = $form['theSubscription'][$key2];
              if (is_numeric($key2)) {
                if ($form['theSubscription'][$key2]['filehead']['#title'] == '000') {
                  $aTemp = $form['theSubscription'][$key2]['filehead'];
                }
                if ($key2 == $myKey) {
                  $keyfornewrow = $key2 + 5;
                  $formBis['theSubscription'][$keyfornewrow] = $form['theSubscription'][$key2];
                  foreach ($formBis['theSubscription'][$keyfornewrow] as $key3 => $value3) {
                    switch ($key3) {
                      case 'sharedwith':
                        $formBis['theSubscription'][$keyfornewrow][$key3]['#value'] = 0;
                        break;
                      case 'comment':
                        $formBis['theSubscription'][$keyfornewrow][$key3]['#value'] = NULL;
                        break;
                      case 'filehead':
                        break;
                      case 'addRow':
                        $formBis['theSubscription'][$keyfornewrow][$key3] = ['#type' => 'hidden',];
                        break;
                      case 'cs_id':
                        $formBis['theSubscription'][$keyfornewrow][$key3]['#value'] = NULL;
                        break;
                      default:
                        if (substr($key3, 0, 8) == 'quantity') {
                          $formBis['theSubscription'][$keyfornewrow][$key3]['#value'] = NULL;
                        }
                    }
                  }
                }
                else {
                }
              }
            }
            $formBis['theSubscription'][$keyfornewrow]['filehead'] = $aTemp;
            break;
          default:
            $formBis[$key] = $form[$key];
        }
      }
      return $formBis;

    }
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getTriggeringElement()['#name'] == 'save') {

      $iNumberOfQuantities = $form['theSubscription']['#quantities'];
      $result = $form_state->getUserInput();

      $sQuantities = "";
      $sQuantities4Hash = "";
      for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
        $sField = 'quantity' . sprintf("%02d", $i);
        $sQuantity = $result[$sField];
        $sQuantities4Hash .= ($sQuantity == "") ? "." : $sQuantity;
        $sQuantities .= $sQuantity;
      }
      if ($form["theSubscription"]["file_id"]["#value"] == 0) {
        // Pas de fichier en entrée
        $fileId = (int) $result[0]["fids"];
      }
      else {
        $fileId = (int) $result["file_id"];
      }

      $hash = $sQuantities4Hash . "_" . $result['sharedwith'] . "_" . $result['comment'] . "_" . $fileId;
      if ($hash != $result['hash']) {

        $contract = (int) $form_state->getBuildInfo()['args'][0];
        $storage = \Drupal::entityTypeManager()
          ->getStorage('contract_subscription');
        $id = (int) $result['cs_id'];


        if ($id == 0) {
          $sAction = 'C';
          $entity = $storage->create();
        }
        else {
          if ($sQuantities == "") {
            $sAction = 'S';
          }
          else {
            $sAction = 'M';
          }
          $entity = $storage->load($id);
        }

        switch ($sAction) {
          case 'C':
          case 'M':
            $entity->contract_id = $contract;
            $entity->member_id = (int) $result['designation'];
            $entity->sharedwith_member_id = (int) $result['sharedwith'];
            $entity->comment = str_replace([
              "\r\n",
              "\n",
              "\r",
            ], " ", $result['comment']);
            $entity->file = $fileId;
            for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
              $sField = 'quantity' . sprintf("%02d", $i);
              $entity->$sField = $result[$sField];
            }
            $entity->save();
            break;
          case 'S':
            $entity->delete();
            break;
          default:
        }

        //    Mise à jour de la liste des AMAPiens
        _setAmapien();

        _export_amap_CSV('amap_contracts_subscriptions', 'rest_export_1', $contract);

        \Drupal::messenger()
          ->addMessage($this->t('The changes have been saved for « %designation ».', ['%designation' => $form["designation"]["#options"][$form_state->getValues()['designation']]]));

      }

    }

  }

}
