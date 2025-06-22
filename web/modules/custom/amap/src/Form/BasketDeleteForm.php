<?php

namespace Drupal\amap\Form;

use \Drupal\Core\Entity\ContentEntityDeleteForm;
use \Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Url;

/**
 * Provides a form for deleting Basket entities.
 *
 * @ingroup amap
 */
class BasketDeleteForm extends ContentEntityDeleteForm {

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $entity->delete();

    $form_state->setRedirect('view.amap_baskets.page_1');
    \Drupal::messenger()
      ->addMessage($this->t('The basket « @id » has been deleted.', ['@id' => $entity->id()]));

  }

  public function getQuestion() {
    return $this->t('Are you sure you want to delete basket « @id »?', ['@id' => $this->getEntity()->id->value]);
  }

  public function getCancelUrl() {
    return Url::fromRoute('view.amap_baskets.page_1');
  }

}
