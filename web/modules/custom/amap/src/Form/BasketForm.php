<?php

namespace Drupal\amap\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the basket entity edit forms.
 */
class BasketForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['@id' => $entity->id(),];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()
          ->addStatus($this->t('The basket « @id » has been added.', $message_arguments));
        break;

      case SAVED_UPDATED:
        $this->messenger()
          ->addStatus($this->t('The basket « @id » has been updated.', $message_arguments));
        break;
    }

    $form_state->setRedirect('view.amap_baskets.page_1');

    return $result;
  }

}
