<?php
/**
 * Color Settings
 */
$form['colord']['color_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Color Scheme Settings'),
    '#description'   => t('These settings adjust the look and feel of the MiliPro theme. Changing the color below will change the color of MiliPro theme.'),
  ];
  $form['colord']['color_scheme_option'] = [
    '#type' => 'fieldset',
    '#title' => t('Color Scheme'),
  ];
  $form['colord']['color_scheme_option']['color_scheme'] = [
    '#type'          => 'select',
    '#title' => t('Select Color Scheme'),
    '#options' => array(
      'color_default' => t('Default'),
      'color_custom' => t('Custom'),
      ),
    '#default_value' => theme_get_setting('color_scheme', 'milipro'),
    '#description'   => t('Default will set the theme to default color scheme. Custom will set the theme color as set below.')
  ];
  $form['colord']['color_custom_section'] = [
    '#type' => 'fieldset',
    '#title' => t('Custom Color Scheme'),
    '#description'   => t('Customize color of the theme. This will work if you have selected <strong>Custom</strong> color scheme above.')
  ];
  $form['colord']['color_custom_section']['color_primary'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_primary', 'milipro'),
    '#title'       => t('Primary Color'),
    '#default_value' => theme_get_setting('color_primary', 'milipro'),
    '#description' => t('<p>Default value is <strong>#d1392b</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_secondary'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_secondary', 'milipro'),
    '#title'       => t('Secondary Color'),
    '#default_value' => theme_get_setting('color_secondary', 'milipro'),
    '#description' => t('<p>Default value is <strong>#FFA800</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['bg_body'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('bg_body', 'milipro'),
    '#title'       => t('Body Background'),
    '#default_value' => theme_get_setting('bg_body', 'milipro'),
    '#description' => t('<p>Default value is <strong>#fcfbf8</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['block_bg'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('block_bg', 'milipro'),
    '#title'       => t('Sidebar Block Background'),
    '#default_value' => theme_get_setting('block_bg', 'milipro'),
    '#description' => t('<p>Default value is <strong>#ffffff</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_text'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_text', 'milipro'),
    '#title'       => t('Text Color'),
    '#default_value' => theme_get_setting('color_text', 'milipro'),
    '#description' => t('<p>Default value is <strong>#404040</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_heading'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_heading', 'milipro'),
    '#title'       => t('Heading Color'),
    '#default_value' => theme_get_setting('color_heading', 'milipro'),
    '#description' => t('<p>Default value is <strong>#191919</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_light'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_light', 'milipro'),
    '#title'       => t('Light Color'),
    '#default_value' => theme_get_setting('color_light', 'milipro'),
    '#description' => t('<p>Default value is <strong>#f5f2ed</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_dark'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_dark', 'milipro'),
    '#title'       => t('Light Color'),
    '#default_value' => theme_get_setting('color_dark', 'milipro'),
    '#description' => t('<p>Default value is <strong>#4e100b</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['color_border'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('color_border', 'milipro'),
    '#title'       => t('Line and Border Color'),
    '#default_value' => theme_get_setting('color_border', 'milipro'),
    '#description' => t('<p>Default value is <strong>##d6d6d6</strong></p><p><hr /></p>'),
  ];
  $form['colord']['color_custom_section']['bg_header'] = [
    '#type'        => 'color',
    '#field_suffix' => theme_get_setting('bg_header', 'milipro'),
    '#title'       => t('Sticky Header Background'),
    '#default_value' => theme_get_setting('bg_header', 'milipro'),
    '#description' => t('<p>Default value is <strong>#232323</strong></p><p><hr /></p>'),
  ];