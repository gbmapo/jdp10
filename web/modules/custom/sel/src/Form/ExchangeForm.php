<?php

namespace Drupal\sel\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for Exchange edit forms.
 *
 * @ingroup sel
 */
class ExchangeForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\sel\Entity\Exchange */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    $form['from_seliste_id']['#attributes'] = [
      'onchange' => "hasChanged(this)",
    ];

    $form['to_seliste_id']['#attributes'] = [
      'onchange' => "hasChanged(this)",
    ];

    $oCurrentUser = \Drupal::currentUser();
    $iCurrentUserId = $oCurrentUser->id();
    $form['current_user'] = [
      '#type' => 'hidden',
      '#default_value' => $iCurrentUserId,
      '#attributes' => ['id' => 'current-user-id'],
    ];

    $form['#attached']['library'][] = 'sel/echange';

    unset($form['actions']['delete']);

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $this->setUrl(),
      '#attributes' => [
        'class' => 'button',
      ],
      '#weight' => '20',
    ];

    return $form;
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

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    $values = $form_state->getValues();
    $sSeliste = $entity->from_seliste_id->target_id;
    $sSeliste .= '|' . $entity->to_seliste_id->target_id;
    $sDate = $values['date'][0]['value']->format("d/m/Y");

    switch ($status) {
      case SAVED_NEW:
        \Drupal::messenger()
          ->addMessage($this->t('Exchange « @label » has been added.', [
            '@label' => $entity->label(),
          ]));
        $sAction = 'ajouté';
        break;
      case SAVED_UPDATED:
        \Drupal::messenger()
          ->addMessage($this->t('Exchange « @label » has been updated.', [
            '@label' => $entity->label(),
          ]));
        $sAction = 'modifié';
      default:
        break;
    }
    _sendEmailForExchange([$sDate, $sAction, $sSeliste]);

    $form_state->setRedirectUrl($this->setUrl());

  }

}
