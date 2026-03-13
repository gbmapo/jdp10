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

    if ($form_state->get('allData') === NULL) {
      $allData = $this->loadData($contract);
      $form_state->set('allData', $allData);
    }
    else {
      $allData = $form_state->get('allData');
    }
    $allRows = $allData[0];
    $aContractType = $allData[1];
    $results_am = $allData[2];
    $sContractIsOpenForSubscription = $allData[3];

    $iNumberOfQuantities = $aContractType[1];
    $aContractTypeHeader = $aContractType[2];

    $upload_max_filesize = Bytes::toNumber(ini_get('upload_max_filesize'));
    $post_max_size = Bytes::toNumber(ini_get('post_max_size'));
    $myMax = 2 * 1024 * 1024;
    $upload_max = min($upload_max_filesize, $post_max_size, $myMax);
    $upload_max_inMB = $upload_max / 1024 / 1024;

    if ($form_state->has('align')) {
      $image = '/sites/default/files/images/' . $form_state->get('image') . '.svg';
      $style = $form_state->get('align');
    }
    else {
      $image = '/sites/default/files/images/align-left.svg';
      $form_state->set('image', 'align-left');
      $style = '';
      $form_state->set('align', $style);
    }

    $form['subscriptions'] = [
      '#type' => 'table',
      '#sticky' => TRUE,
      '#responsive' => TRUE,
      '#id' => 'subscriptions',
      '#quantities' => $iNumberOfQuantities,
      '#attributes' => ['style' => $style,],
    ];

    $data = [
      '#type' => 'inline_template',
      '#template' => '{{ text }} <input type="image" name="alignButton" src="{{ image }}" class="image-button">',
      '#context' => [
        'text' => $this->t('Member'),
        'image' => $image,
      ],
    ];
    $form['subscriptions']['#header'] = [['data' => $data]];
    $form['subscriptions']['#header'] = array_merge($form['subscriptions']['#header'], $aContractTypeHeader);
    $aContractStandardHeader = [
      'sharedwith' => t('Shared with'),
      'comment' => t('Comment'),
      'file' => '•••',
      'addRow' => '',
    ];
    $form['subscriptions']['#header'] = array_merge($form['subscriptions']['#header'], $aContractStandardHeader);

    $wrapper = 'formWrapper';
    $form['#prefix'] = '<div id="' . $wrapper . '">';
    $form['#suffix'] = '</div>';

    //  Génération des lignes du tableau
    foreach ($allRows as $key => $value) {

      $myKey = 10 + $key * 10;

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
      $form['subscriptions'][$myKey]['sharedwith'] = [
        '#type' => 'select',
        '#options' => $results_am,
        '#default_value' => $iKey,
        '#ajax' => [
          'callback' => '::ajaxSharedwith',
          'wrapper' => $wrapper,
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
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
          'FileExtension' => ['extensions' => 'pdf'],
          'FileSizeLimit' => ['fileLimit' => $upload_max],
        ],
        '#default_value' => [$fileId],
      ];

      if ($sContractIsOpenForSubscription) {
        $form['subscriptions'][$myKey]['addRow'] = [
          '#type' => 'submit',
          '#name' => $myKey,
          '#value' => '+',
          '#submit' => ['::ajaxAddRow'],
          '#disabled' => isset($value->hideplus) ? TRUE : FALSE,
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

  private function loadData($contract) {

    //  Détails du contrat
    $oContract = \Drupal::entityTypeManager()
      ->getStorage('contract')
      ->load($contract);
    $sContractIsOpenForSubscription = $oContract->get('isopenforsubscription')
      ->getString();
    $sContractType = $oContract->get('type')->getString();

    $aContractType = _detail_contract_type($sContractType);
    $iNumberOfQuantities = $aContractType[1];

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
    $allRows = \Drupal::database()->query($query)->fetchAll();

    return [
      $allRows,
      $aContractType,
      $results_am,
      $sContractIsOpenForSubscription,
    ];

  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getTriggeringElement()['#name'] == 'cancel') {
      return;
    }
    $input = $form_state->getUserInput();
    if (isset($input['alignButton_x'])) {
      $image = ($form_state->get('image') == 'align-left') ? 'align-center' : 'align-left';
      $form_state->set('image', $image);
      $align = ($form_state->get('align') == '') ? 'margin-left: calc(-100vw / 2 + 1170px / 2);' : '';
      $form_state->set('align', $align);
      $form_state->setRebuild();
      return;
    }
    parent::validateForm($form, $form_state);

  }

  public function ajaxAddRow(array &$form, FormStateInterface $form_state) {

    $myKey = $form_state->getTriggeringElement()['#name'];
    $key = ($myKey - 10) / 10;
    $allData = $form_state->get('allData');
    $allRows = $allData[0];
    $iNumberOfQuantities = $allData[1][1];
    $results_am = $allData[2];

    $am_id = $allRows[$key]->am_id;
    $designation = $allRows[$key]->designation;
    $am_id_fornewrow = $am_id . '-2';

    $temp = [
      'am_id' => $am_id_fornewrow,
      'designation' => $designation,
      'sharedwith_member_id' => "0",
      'file__target_id' => NULL,
      'comment' => "",
      'cs_id' => "",
      'hideplus' => TRUE,
    ];
    for ($i = 1; $i <= $iNumberOfQuantities; $i++) {
      $sField = 'quantity' . sprintf("%02d", $i);
      $temp[$sField] = "";
    }
    $newRow = (object) $temp;
    array_splice($allRows, $key + 1, 0, [$newRow]);
    $allData[0] = $allRows;

    $newArr = [];
    $key_int = (int) $am_id;
    foreach ($results_am as $k => $v) {
      $newArr[$k] = $v;
      if ($k === $key_int) {
        $newArr[$am_id_fornewrow] = $designation;
      }
    }
    $results_am = $newArr;
    $allData[2] = $results_am;

    $form_state->set('allData', $allData);
    $form_state->setUserInput([]);
    $form_state->setRebuild(TRUE);

  }

  public function ajaxSharedwith(array &$form, FormStateInterface $form_state) {

    $currentRow = $form_state->getTriggeringElement()["#parents"][1];
    $memberOfCurrentRow = (int) $form['subscriptions'][$currentRow]['am_id']['#default_value'];
    $memberToBeSharedWith = $form['subscriptions'][$currentRow]['sharedwith']['#value'];

    foreach ($form['subscriptions'] as $key => $value) {
      if (is_numeric($key)) {
        if ($key != $currentRow) {
          switch (TRUE) {
            case ($form['subscriptions'][$key]['am_id']['#default_value'] == $memberToBeSharedWith):
              $form['subscriptions'][$key]['sharedwith']['#value'] = $memberOfCurrentRow;
              break;
            case ($form['subscriptions'][$key]['sharedwith']['#value'] == $memberOfCurrentRow):
            case ($form['subscriptions'][$key]['sharedwith']['#value'] == $memberToBeSharedWith):
              $form['subscriptions'][$key]['sharedwith']['#value'] = 0;
              break;
            default:
              break;
          }
        }
      }
    }
    $form_state->setRebuild(TRUE);
    return $form;

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
