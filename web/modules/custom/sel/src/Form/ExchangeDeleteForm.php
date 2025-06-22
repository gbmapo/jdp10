<?php

namespace Drupal\sel\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting Exchange entities.
 *
 * @ingroup sel
 */
class ExchangeDeleteForm extends ContentEntityDeleteForm {

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $entity = $this->getEntity();

    $sSeliste = $entity->from_seliste_id->target_id;
    $sSeliste .= '|' . $entity->to_seliste_id->target_id;
    $sDate = $entity->date->value;
    $aDate = explode("-", $sDate);
    $sDate = implode("/", array_reverse($aDate));
    $sAction = 'supprimé';
    _sendEmailForExchange([
      $sDate,
      $sAction,
      $sSeliste,
    ]);

    $entity->delete();

    $form_state->setRedirectUrl($this->setUrl());
    \Drupal::messenger()
      ->addMessage($this->t('Exchange « @label » has been deleted.', [
        '@label' => $entity->label(),
      ]));

  }

  public function setUrl() {
    $id = \Drupal::currentUser()->id();
    switch ($_GET['origin']) {
      case 1:
        $url = Url::fromRoute('view.sel_echanges.page_1');
        break;
      case 2:
        $url = Url::fromRoute('view.sel_echanges.page_2', [
          'arg_0' => $id,
          'arg_1' => $id,
        ]);
        break;
      default:
        break;
    }
    return $url;
  }

  public function getQuestion() {
    return $this->t('Are you sure you want to delete exchange « @label »?', [
      '@label' => $this->getEntity()->label(),
    ]);
  }

  public function getCancelUrl() {
    return $this->setUrl();
  }

}
