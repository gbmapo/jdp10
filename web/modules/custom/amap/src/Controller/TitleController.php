<?php

namespace Drupal\amap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class TitleController.
 */
class TitleController extends ControllerBase {

  public function changeTitle($page, $contract = NULL) {

    switch ($page) {

      case 'signupForPlanningMany':
        $title = $this->t('Sign up for Planning <a class="button-dark" href="/amap/signupForPlanningOne">Little screen</a>');
        break;
      case 'signupForPlanningOne':
        $title = $this->t('Sign up for Planning <a class="button-dark" href="/amap/signupForPlanningMany">Big screen</a>');
        break;

      case 'subscribeMany':
        $title = $this->t('Change subscriptions <a class="form-submit" href="/amap/contract/subscribeOne/@contract/subscribeOne">Little screen</a>', ['@contract' => $contract]);
        break;
      case 'subscribeOne':
        $title = $this->t('Change a subscription <a class="form-submit" href="/amap/contract/subscribeMany/@contract">Big screen</a>', ['@contract' => $contract]);
        break;
      case 'one':
        $title = $this->t('Change subscriptions');
        break;

      default:
    }
    return $title;

  }

}
