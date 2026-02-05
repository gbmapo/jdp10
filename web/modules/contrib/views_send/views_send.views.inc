<?php

/**
 * @file
 * Views hooks for the Views Send module.
 */

/**
 * Implements hook_views_data_alter().
 */
function views_send_views_data_alter(&$data) {
  $data['views']['views_send_bulk_form'] = [
    'title' => t('Send email'),
    'help' => t('Provide a checkbox to select the row for email sending.'),
    'field' => [
      'id' => 'views_send_bulk_form',
    ],
  ];
}
