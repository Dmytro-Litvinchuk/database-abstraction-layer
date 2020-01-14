<?php

namespace Drupal\pets_owners_storage\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pets_owners\Form\FormPetsOwners;

/**
 * Class PetsOwnersForm.
 *
 * @package Drupal\pets_owners_storage\Form
 */
class PetsOwnersForm extends FormPetsOwners {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'pets_owners_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state, $pid = NULL) {
    // Get values from DB.
    $values = Database::getConnection()
      ->select('pets_owners_storage', 'p')
      ->fields('p', [
        'pid',
        'prefix',
        'name',
        'gender',
        'age',
        'father',
        'mother',
        'pets_name',
        'email',
      ])->condition('pid', $pid)
      ->execute()->fetchAssoc();
    // Used all fields of parent form.
    $form = parent::buildForm($form, $form_state);
    if (!empty($values)) {
      $form['name']['#default_value'] = $values['name'];
      $form['prefix']['#default_value'] = $values['prefix'];
      $form['gender']['#default_value'] = $values['gender'];
      $form['age']['#default_value'] = $values['age'];
      $form['parents']['father']['#default_value'] = $values['father'];
      $form['parents']['mother']['#default_value'] = $values['mother'];
      $form['have_pets']['#default_value'] = 1;
      $form['pets_name']['#default_value'] = $values['pets_name'];
      $form['email']['#default_value'] = $values['email'];
      $form['actions']['submit']['#value'] = $this->t('Change');
      // Pass value $pid to submit form.
      $form_state->set('pid', $pid);
    }
    $form['actions']['delete'] = [
      '#type' => 'submit',
      '#value' => 'delete',
      // Custom function on submit.
      '#submit' => ['::delete'],
    ];
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * Custom submit function delete from DB.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function delete(array &$form, FormStateInterface $form_state) {
    $pid = $form_state->get('pid');
    $query = \Drupal::database();
    $query->delete('pets_owners_storage')
      ->condition('pid', $pid)
      ->execute();
    $text = 'Record pid => ' . $pid . ' was removed from database.';
    \Drupal::messenger()->addMessage($text);
    $form_state->setRedirect('pets_owners_storage.content');
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $pid = $form_state->get('pid');
    $query = \Drupal::database();
    $query->update('pets_owners_storage')
      ->fields([
        'prefix' => $form_state->getValue('prefix'),
        'name' => $form_state->getValue('name'),
        'gender' => $form_state->getValue('gender'),
        'age' => $form_state->getValue('age'),
        'father' => $form_state->getValue('father'),
        'mother' => $form_state->getValue('mother'),
        'pets_name' => $form_state->getValue('pets_name'),
        'email' => $form_state->getValue('email'),
      ])
      ->condition('pid', $pid)
      ->execute();
    $text = $this->t('The value changed');
    \Drupal::messenger()->addMessage($text);
    $form_state->setRedirect('pets_owners_storage.content');
  }

}
