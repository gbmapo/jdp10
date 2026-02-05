<?php

/**
 * @file
 * Rules hooks for the Views Send module.
 */

/**
 * Implements hook_rules_event_info().
 */
function views_send_rules_event_info() {
  $defaults = [
    'group' => t('Views Send'),
  ];
  return [
    'views_send_email_sent' => $defaults + [
      'label' => t('After sending an individual email'),
      'variables' => [
        'views_send_email_message' => [
          'type' => 'views_send_email_message',
          'label' => t('email message'),
          'description' => t('All information about the message.'),
        ],
      ],
    ],
    'views_send_all_email_added_to_spool' => $defaults + [
      'label' => t('After adding all email messages to the spool'),
      'variables' => [
        'views_send_email_count' => [
          'type' => 'integer',
          'label' => t('message count'),
          'description' => t('The number of messages added to the spool.'),
        ],
      ],
    ],
    'views_send_email_added_to_spool' => $defaults + [
      'label' => t('After adding an individual email to the spool'),
      'variables' => [
        'views_send_email_message' => [
          'type' => 'views_send_email_message',
          'label' => t('email message'),
          'description' => t('All information about the message.'),
        ],
      ],
    ],
  ];
}

/**
 * Implements hook_rules_data_info().
 */
function views_send_rules_data_info() {
  return [
    'views_send_email_message' => [
      'label' => t('Views Send email message'),
      'group' => t('Views Send'),
      'wrap' => TRUE,
      'property info' => _views_send_email_message_property_info(),
    ],
  ];
}
