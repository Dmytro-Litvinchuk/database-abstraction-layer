<?php

namespace Drupal\pets_owners_storage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class PetsDeleteController.
 *
 * @package Drupal\pets_owners_storage\Controller
 */
class PetsDeleteController extends ControllerBase {

  /**
   * @param null $pid
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function delete($pid = NULL) {
    $query = \Drupal::database();
    $query->delete('pets_owners_storage')
      ->condition('pid', $pid)
      ->execute();
    $text = 'Record pid => ' . $pid . ' was removed from database.';
    \Drupal::messenger()->addMessage($text);
    // Redirect to a page that show all the records.
    return $this->redirect('pets_owners_storage.content');
  }

}
