<?php

namespace Drupal\amap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ContractsController.
 */
class ContractsController extends ControllerBase {

  public function showContracts() {

    $oCurrentUser = $this->currentUser();

    switch (TRUE) {

      case ($oCurrentUser->isAnonymous()):
        return $this->redirect('view.amap_contracts.page_3');
        break;

      case ($oCurrentUser->hasPermission('add contract entities')):
        return $this->redirect('view.amap_contracts.page_1');
        break;

      default:
        return $this->redirect('view.amap_contracts.page_2');
    }
  }

  public function export_subscriptions($contract, $type = 'csv') {

    switch ($type) {

      case 'csv':
        _export_amap_CSV('amap_contracts_subscriptions', 'rest_export_1', $contract);
        $sFileName = 'Contract-' . $contract . '-export.csv';
        break;

      case 'xls':
        _export_amap_CSV('amap_contracts_subscriptions', 'rest_export_2', $contract);
        $sFileName = 'Contract-' . $contract . '-export.xlsx';
        break;

      default:
        break;
    }

    $sFileNameWithPath = 'sites/default/files/_private/contracts/' . $sFileName;
    $response = new BinaryFileResponse($sFileNameWithPath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $sFileName);

    return $response;

  }

  public function enterSubscriptions($contract) {

    $oContract = \Drupal::entityTypeManager()
      ->getStorage('contract')
      ->load($contract);
    if ($oContract) {
      $sContractType = $oContract->get('type')->getString();
      $aContractType = _detail_contract_type($sContractType);
      $iNumberOfQuantities = (int) $aContractType[1];
      if ($iNumberOfQuantities < 29) {
        return $this->redirect('amap.contract_subscription_table_form', ['contract' => $contract]);
      }
      else {
        return $this->redirect('amap.contract_subcription_one_form', [
          'contract' => $contract,
          'page' => 'one',
        ]);
      }
    }
    else {
      /* TODO by zonfal
       *
       */
      \Drupal::logger('amap')
        ->error('Enter subscriptions: Contract @number not found.', ['@number' => $contract]);
    }
  }

}
