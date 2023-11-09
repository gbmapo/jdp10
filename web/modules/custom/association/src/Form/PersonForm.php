<?php

namespace Drupal\association\Form;

use Drupal;
use Drupal\association\Entity\Person;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Form controller for Person edit forms.
 *
 * @ingroup association
 */
class PersonForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity Person */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    $form['wascontact'] = [
      '#type' => 'hidden',
      '#value' => $entity->iscontact->value,
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $person_id = $this->entity->id->value;
    $user_id = $form_state->getValue('user_id')['0']['target_id'];
    if ($user_id != $person_id) {
      $person = Drupal::entityTypeManager()
        ->getStorage('person')
        ->load($user_id);
      if ($person != NULL) {
        $form_state->setErrorByName('name', $this->t('This user is already associated to another person.<BR>Please choose another user.'));
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->entity;

    $user_id = $entity->user_id->target_id;

    $userofperson = User::load($user_id);
    $member = Drupal::entityTypeManager()
      ->getStorage('member')
      ->load($entity->member_id->target_id);

    // L'identifiant et l'email de la Personne sont hérités de ceux du User associé
    $entity->set('id', $user_id);
    $entity->set('email', $userofperson->getEmail());

    if ($entity->isactive->value) {
      if ($entity->iscontact->value) {
        if ($form['wascontact']['#value'] == 0) {
          _updatePersonToContact($entity);
        }
      }
      else {
        $userofperson->removeRole('contact_for_member');
        $member->contact_id = NULL;
      }
      $userofperson->set("status", 1);
    }
    else {
      $entity->set("iscontact", 0); // Inactive person can't be Contact
      $userofperson->set("status", 0);
      $userofperson->removeRole('contact_for_member');
      $member->contact_id = NULL;
    }

    $userofperson->save();
    $member->save();

    $status = parent::save($form, $form_state);
    switch ($status) {
      case SAVED_NEW:
        Drupal::messenger()
          ->addMessage($this->t('Person « %label » has been added.', [
            '%label' => $entity->label(),
          ]));
        break;

      default:
        Drupal::messenger()
          ->addMessage($this->t('Person « %label » has been updated.', [
            '%label' => $entity->label(),
          ]));
    }

    $form_state->setRedirect('view.association_persons.page_1');

  }

}
