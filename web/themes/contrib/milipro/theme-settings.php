<?php
use Drupal\Core\Form\FormStateInterface;
/**
 * @file
 * Custom setting for MiliPro theme.
 */

function milipro_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $ver = "11.1.0";
  $form['#attached']['library'][] = 'milipro/theme-settings';
  $theme_update_info = file_get_contents("https://drupar.com/theme-update-info/milipro.txt");
  $form['milipro'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3 class="settings-form-title"></h3>',
    '#default_tab' => 'general',
  ];

  // General settings tab.
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h3>Thanks for using MiliPro Theme</h3>MiliPro is a premium Drupal 9, 10, 11 theme designed and developed by <a href="https://drupar.com" target="_blank">Drupar.com</a>'),
    '#group' => 'milipro',
  ];
  // Main Tabs -> color
  $form['colord'] = [
    '#type'  => 'details',
    '#title' => t('Theme Color'),
    '#group' => 'milipro',
  ];
  // Slider tab.
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Slider'),
    '#group' => 'milipro',
  ];

  // Header tab.
  $form['header'] = [
    '#type'  => 'details',
    '#title' => t('Header'),
    '#group' => 'milipro',
  ];

  // Sidebar tab.
  $form['sidebar'] = [
    '#type'  => 'details',
    '#title' => t('Sidebar'),
    '#group' => 'milipro',
  ];

  // Content tab.
  $form['content'] = [
    '#type'  => 'details',
    '#title' => t('Content'),
    '#group' => 'milipro',
  ];

  // Footer tab.
  $form['footer'] = [
    '#type'  => 'details',
    '#title' => t('Footer'),
    '#group' => 'milipro',
  ];
  $form['comment'] = [
    '#type'  => 'details',
    '#title' => t('Comment'),
    '#group' => 'milipro',
  ];
  $form['components'] = [
    '#type'  => 'details',
    '#title' => t('Components'),
    '#group' => 'milipro',
  ];
  // Main Tabs ->Insert codes
  $form['insert_codes'] = [
    '#type'  => 'details',
    '#title' => t('Insert Codes'),
    '#group' => 'milipro',
  ];
  // Main Tabs -> Licensing.
  $form['license'] = [
    '#type'  => 'details',
    '#title' => t('Theme License'),
    '#group' => 'milipro',
  ];

  // Main Tabs -> Update.
  $form['update'] = [
    '#type'  => 'details',
    '#title' => t('Update'),
    '#description' => t('<h4>Check For Update</h4>'),
    '#group' => 'milipro',
  ];

  // Support tab.
  $form['support'] = [
    '#type'  => 'details',
    '#title' => t('Support'),
    '#description' => t('For any support related to MiliPro theme, please post on our <a href="https://drupar.com/node/add/ticket" target="_blank">open a support ticket</a>.'),
    '#group' => 'milipro',
  ];

  // General tab -> Theme version info.
  $form['general']['general_version'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Current Theme Version'),
    '#description' => t("$ver"),
  ];
  // Color tab -> Info.
  include_once 'inc/settings/color.php';

  // General -> info.
  $form['general']['general_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Info'),
    '#description' => t('<a href="https://drupar.com/theme/milipro" target="_blank">Theme Homepage</a> || <a href="https://demo2.drupar.com/milipro/" target="_blank">Theme Demo</a> || <a href="https://drupar.com/milipro-documentation" target="_blank">Theme Documentation</a> || <a href="https://drupar.com/support" target="_blank">Theme Support</a>'),
  ];
  // Slider
  $form['slider']['slider_type'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Types'),
    '#description'   => t('<ul><li>Basic Slider (text only)</li><li>Basic Slider (text and image)</li><li>Classic Slider</li><li>Layered Slider</li></ul>'),
  ];
  $form['slider']['slider_faq'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Frequently Asked Questions'),
    '#description'   => t('<h6>Can I create more than one slider?</h6>
    <p>Yes</p>
    <hr />
    <h6>Can I create slider in inner pages?</h6>
    <p>Yes</p>
    <hr />
    <h6>Does the slider support Drupal multilingual?</h6>
    <p>Yes</p>'),
  ];
  $form['slider']['slider_code'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Code'),
    '#description'   => t('<p>Please refer to below links for slider codes.<ul>
    <li><a href="https://drupar.com/node/3413" target="_blank">Slider Basic</a></li>
    <li><a href="https://drupar.com/node/3414" target="_blank">Slider Basic With Image</a></li>
    <li><a href="https://drupar.com/node/3415" target="_blank">Slider Style - Classic</a></li>
    <li><a href="https://drupar.com/node/3416" target="_blank">Slider Style - Layered</a></li>
    </ul>'),
  ];
  $form['slider']['slider_doc'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Documentation'),
    '#description'   => t('Please refer to <a href="https://drupar.com/node/673/" target="_blank">slider documentation page</a> for detailed information.'),
  ];

  /**
   * Settings under header tab.
   */

  // Header -> sticky header.
  $form['header']['sticky_header'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sticky Header'),
  ];
  $form['header']['sticky_header']['sticky_header_option'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Sticky Header'),
    '#default_value' => theme_get_setting('sticky_header_option', 'milipro'),
    '#description'   => t("Check this option to enable sticky header. Uncheck to disable."),
  ];
  $form['header']['header_links'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Header Links'),
    '#description'   => t('<a href="https://drupar.com/node/749/" target="_blank">Change Logo</a> || <a href="https://drupar.com/node/739/" target="_blank">Change Favicon Icon</a> || <a href="https://drupar.com/node/740/" target="_blank">Manage Main Menu</a> || <a href="https://drupar.com/node/741/" target="_blank">Sliding Search Form</a>'),
  ];
  /**
   * Sidebar
   */
  $form['sidebar']['front_sidebar_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Homepage Sidebar'),
  ];
  $form['sidebar']['front_sidebar_section']['front_sidebar'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Sidebars On Homepage'),
    '#default_value' => theme_get_setting('front_sidebar', 'milipro'),
    '#description'   => t("<p>Check this option to enable left and right sidebar on homepage.</p>"),
  ];
  // Sidebar -> Animated sidebar.
  $form['sidebar']['animated_sidebar'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Sidebar'),
  ];
  $form['sidebar']['animated_sidebar']['animated_sidebar_option'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable animated sidebar'),
    '#default_value' => theme_get_setting('animated_sidebar_option', 'milipro'),
    '#description'   => t("Check this option to enable animated sidebar feature. Uncheck to hide.<br />Please refer to this tutorial for details. <a href='https://drupar.com/milipro-documentation/how-create-animated-sidebar' target='_blank'>How To Create Animated Sidebar</a>"),
  ];

  /**
   * Content
   */
  $form['content']['content_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Content -> Homepage.
  $form['content']['homepage'] = [
    '#type'        => 'details',
    '#title'       => t('Homepage Content'),
    '#description'   => t('Please follow this tutorials to add content on homepage.</p><ul>
    <li><a href="https://drupar.com/node/752/" target="_blank">How To Create Homepage</a></li>
    <li><a href="https://drupar.com/node/753/" target="_blank">How to add content on homepage</a></li>
  </ul>'),
    '#group' => 'content_tab',
  ];
  // Content -> demo site
  $form['content']['demo_site'] = [
    '#type'        => 'details',
    '#title'       => t('Demo Site Content'),
    '#description'   => t('<p>Please find sample content of theme demo site here:</p><p><a href="https://drupar.com/node/882" target="_blank">MiliPro Demo Content</a></p>'),
    '#group' => 'content_tab',
  ];
  // content -> Shortcodes
  $form['content']['shortcodes'] = [
    '#type'          => 'details',
    '#title'         => t('Shortcodes'),
    '#description'   => t('<p>MiliPro theme has many custom shortcodes which you can use for creating contents. Please visit this page for list of all available shortcodes and how to use these shortcodes.</p><ul><li><a href="https://drupar.com/node/766/" target="_blank">MiliPro Shortcodes</a></li></ul>'),
    '#group' => 'content_tab',
  ];
  // Content -> Animated Content.
  $form['content']['animated_content_tab'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Page Content'),
    '#group' => 'content_tab',
  ];
  $form['content']['animated_content_tab']['animated_content'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Page Content'),
    '#description'   => t('Please visit this tutorial page for details. <a href="https://drupar.com/node/734/" target="_blank">How to create animated content</a>.'),
  ];
  // Content-> Submitted Details
  $form['content']['submitted_details'] = [
    '#type'  => 'details',
    '#title' => t('Submitted Details'),
    '#group' => 'content_tab',
  ];
  $form['content']['submitted_details']['node_tags_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Node Tags'),
  ];
  $form['content']['submitted_details']['node_tags_section']['node_tags'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Node Tags in Submitted Details.'),
    '#default_value' => theme_get_setting('node_tags', 'milipro'),
    '#description'   => t("Check this option to show node tags (if any) in submitted details. Uncheck to hide."),
  ];
  // Scroll to top.
  $form['footer']['scrolltotop'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Scroll To Top'),
  ];

  $form['footer']['scrolltotop']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable scroll to top feature.'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'milipro'),
    '#description'   => t("Check this option to enable scroll to top feature. Uncheck to disable this feature and hide scroll to top icon."),
  ];
  // Footer -> copyright
  $form['footer']['copyright'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Website Copyright Text'),
  ];
  $form['footer']['copyright']['copyright_text'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show website copyright text in footer.'),
    '#default_value' => theme_get_setting('copyright_text', 'milipro'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide."),
  ];
  $form['footer']['copyright']['copyright_custom'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show custom copyright text from copyright block region.'),
    '#default_value' => theme_get_setting('copyright_custom', 'milipro'),
    '#description'   => t('<p>Check this option to show custom copyright text. Create a new block and place the block in copyright region. Uncheck this option to show default copyright text.</p><p>For more details, please refer to the <a href="https://drupar.com/node/760/" target="_blank">documentation page</a></p>'),
  ];
  // Footer -> Cookie
  $form['footer']['cookie'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Cookie Consent message'),
  ];
  $form['footer']['cookie']['cookie_message'] = [
    '#type'          => 'checkbox',
    '#title'       => t('Show Cookie Consent Popup Message'),
    '#default_value' => theme_get_setting('cookie_message', 'milipro'),
    '#description'   => t('Required to place a Cookie Consent message on your site, as per the EU cookie law? Make your website EU Cookie Law Compliant.<br />According to EU cookies law, websites need to get consent from visitors to store or retrieve cookies.'),
  ];
  $form['footer']['cookie']['cookie_custom'] = [
    '#type'          => 'checkbox',
    '#title'       => t('Show Custom Cookie Consent Message'),
    '#default_value' => theme_get_setting('cookie_custom', 'milipro'),
    '#description'   => t('<p>Check this option to show custom cookie consent message. Create a new block and place the block in Cookie Consent Message region. Uncheck this option to show default message text.</p><p>For more details, please refer to the <a href="https://drupar.com/node/3477/" target="_blank">documentation page</a></p>'),
  ];

  // Show user picture in comment.
  $form['comment']['comment_photo'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Comment User Picture'),
  ];

  $form['comment']['comment_photo']['comment_user_pic'] = [
    '#type'          => 'checkbox',
    '#title'         => t('User Picture in comments'),
    '#default_value' => theme_get_setting('comment_user_pic', 'milipro'),
    '#description'   => t("Check this option to show user picture in comment. Uncheck to hide."),
  ];
  // Hightlight Node author comment.
  $form['comment']['comment_author'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Author Comment'),
  ];

  $form['comment']['comment_author']['highlight_author_comment'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Highlight Author Comments'),
    '#default_value' => theme_get_setting('highlight_author_comment', 'milipro'),
    '#description'   => t("Check this option to highlight node author comments."),
  ];
  /*
   * Components
   */
  $form['components']['components_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Social tab.
  $form['components']['social'] = [
    '#type'  => 'details',
    '#title' => t('Social'),
    '#description' => t('Social icons settings. These icons appear in footer region.'),
    '#group' => 'components_tab',
  ];
  $form['components']['social']['all_icons'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Social Icons'),
  ];

  $form['components']['social']['all_icons']['all_icons_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show social icons in footer'),
    '#default_value' => theme_get_setting('all_icons_show', 'milipro'),
    '#description'   => t("Check this option to show social icons in footer. Uncheck to hide."),
  ];
  // Facebook.
  $form['components']['social']['facebook'] = [
    '#type'        => 'details',
    '#title'       => t("Facebook"),
  ];

  $form['components']['social']['facebook']['facebook_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Facebook Url'),
    '#description'   => t("Enter yours facebook profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('facebook_url', 'milipro'),
  ];

  // Twitter.
  $form['components']['social']['twitter'] = [
    '#type'        => 'details',
    '#title'       => t("Twitter"),
  ];
  $form['components']['social']['twitter']['twitter_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Twitter Url'),
    '#description'   => t("Enter yours twitter page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('twitter_url', 'milipro'),
  ];
  // Instagram.
  $form['components']['social']['instagram'] = [
    '#type'        => 'details',
    '#title'       => t("Instagram"),
  ];

  $form['components']['social']['instagram']['instagram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Instagram Url'),
    '#description'   => t("Enter yours instagram page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('instagram_url', 'milipro'),
  ];
  // Linkedin.
  $form['components']['social']['linkedin'] = [
    '#type'        => 'details',
    '#title'       => t("Linkedin"),
  ];
  $form['components']['social']['linkedin']['linkedin_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Linkedin Url'),
    '#description'   => t("Enter yours linkedin page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('linkedin_url', 'milipro'),
  ];

  // YouTube.
  $form['components']['social']['youtube'] = [
    '#type'        => 'details',
    '#title'       => t("YouTube"),
  ];

  $form['components']['social']['youtube']['youtube_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('YouTube Url'),
    '#description'   => t("Enter yours youtube.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('youtube_url', 'milipro'),
  ];

  // Vimeo
  $form['components']['social']['vimeo'] = [
    '#type'        => 'details',
    '#title'       => t("Vimeo"),
  ];

  $form['components']['social']['vimeo']['vimeo_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Vimeo Url'),
    '#description'   => t("Enter yours vimeo.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('vimeo_url', 'milipro'),
  ];

  // telegram.
    $form['components']['social']['telegram'] = [
    '#type'        => 'details',
    '#title'       => t("Telegram"),
  ];

  $form['components']['social']['telegram']['telegram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Telegram Url'),
    '#description'   => t("Enter yours Telegram profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('telegram_url', 'milipro'),
  ];

  // WhatsApp.
    $form['components']['social']['whatsapp'] = [
    '#type'        => 'details',
    '#title'       => t("WhatsApp"),
  ];

  $form['components']['social']['whatsapp']['whatsapp_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('WhatsApp Url'),
    '#description'   => t("Enter yours whatsapp message url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('whatsapp_url', 'milipro'),
  ];

  // Github.
    $form['components']['social']['github'] = [
    '#type'        => 'details',
    '#title'       => t("GitHub"),
  ];

  $form['components']['social']['github']['github_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('GitHub Url'),
    '#description'   => t("Enter yours github page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('github_url', 'milipro'),
  ];

  // Social -> vk.com url.
  $form['components']['social']['vk'] = [
    '#type'        => 'details',
    '#title'       => t("vk.com"),
  ];
  $form['components']['social']['vk']['vk_url'] = [
      '#type'          => 'textfield',
      '#title'         => t('vk.com'),
      '#description'   => t("Enter yours vk.com page url. Leave the url field blank to hide this icon."),
      '#default_value' => theme_get_setting('vk_url', 'milipro'),
  ];
  $form['components']['social']['social_new'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Add More Social Icons'),
    '#description' => t('<p>Please refer to the documentation page for social icon code: <a href="https://drupar.com/node/761/" target="_blank">Add new social icon</a>.'),
  ];
  $form['components']['social']['social_new']['social_new_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('New Social Icons Code'),
    '#default_value' => theme_get_setting('social_new_code', 'milipro'),
  ];
  // Node share
  $form['components']['node_share'] = [
    '#type'        => 'details',
    '#title'       => t('Share Page'),
    '#group' => 'components_tab',
  ];
  $form['components']['node_share']['page_share'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Node Sharing on Social Networking Websites'),
  ];
  $form['components']['node_share']['page_share']['node_share_page'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Share Page Content Type'),
    '#default_value' => theme_get_setting('node_share_page', 'milipro'),
    '#description'   => t("Check this option to show social sharing buttons (facebook, twitter, Instagram etc) on <strong>Basic page</strong> content type nodes. Uncheck to hide."),
  ];

  $form['components']['node_share']['page_share']['node_share_article'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Share Article Content Type'),
    '#default_value' => theme_get_setting('node_share_article', 'milipro'),
    '#description'   => t("Check this option to show social sharing buttons (facebook, twitter, Instagram etc) on <strong>Article</strong> content type nodes. Uncheck to hide."),
  ];

  $form['components']['node_share']['page_share']['node_share_other'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Share Other Content Types'),
    '#default_value' => theme_get_setting('node_share_other', 'milipro'),
    '#description'   => t("Check this option to show social sharing buttons (facebook, twitter, Instagram etc) on other content type nodes. Uncheck to hide."),
  ];

  $form['components']['node_share']['page_share']['node_share_front'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Share Homepage'),
    '#default_value' => theme_get_setting('node_share_front', 'milipro'),
    '#description'   => t("Check this option to show social sharing buttons (facebook, twitter, Instagram etc) on <strong>Homepage</strong>. Uncheck to hide."),
  ];
  // Components -> Font icons
  $form['components']['font_icons'] = [
    '#type'  => 'details',
    '#title' => t('Font Icons'),
    '#group' => 'components_tab',
    '#description'   => t('Following fonts icons libraries are included in the theme. For more details, please refer to the documentation page: <a href="https://drupar.com/node/674/" target="_blank">Font Icons</a>'),
  ];
  $form['components']['font_icons']['fontawesome4'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 4'),
  ];
  $form['components']['font_icons']['fontawesome4']['fontawesome_four'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 4 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_four', 'milipro'),
    '#description'   => t('<p>Check this option to enable fontawesome version 4 font icons.</p><p><a href="https://drupar.com/node/2861/">How to use FontAwesome 4</a></p>'),
  ];
  $form['components']['font_icons']['fontawesome5'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 5'),
    '#description'   => t("<mark>Do not enable both FontAwesome 5 and FontAwesome 6</mark>")
  ];
  $form['components']['font_icons']['fontawesome5']['fontawesome_five'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 5 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_five', 'milipro'),
    '#description'   => t('<p>Check this option to enable fontawesome version 5 font icons.</p><p><a href="https://drupar.com/node/674/">How to use FontAwesome 5</a></p>'),
  ];
  $form['components']['font_icons']['fontawesome6'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 6'),
    '#description'   => t("<mark>Do not enable both FontAwesome 5 and FontAwesome 6</mark>")
  ];
  $form['components']['font_icons']['fontawesome6']['fontawesome_six'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 6 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_six', 'milipro'),
    '#description'   => t('<p>Check this option to enable fontawesome version 6 font icons.</p><p><a href="https://drupar.com/node/674/">How to use FontAwesome 6</a></p>'),
  ];
	$form['components']['font_icons']['bootstrap_icons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Bootstrap Font Icons'),
  ];
  $form['components']['font_icons']['bootstrap_icons']['bootstrapicons'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Bootstrap Icons'),
    '#default_value' => theme_get_setting('bootstrapicons', 'milipro'),
    '#description'   => t('<p>Check this option to enable Bootstrap Font Icons.</p><p><a href="https://drupar.com/node/674/">How to use Bootstrap Font Icons</a></p>'),
  ];
  $form['components']['font_icons']['material'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Google Material Font Icons'),
    '#description'   => t('<a href="https://drupar.com/node/2865" target="_blank">How to use Google Material font icons</a>'),
  ];
  $form['components']['font_icons']['material']['material_icon_outlined'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Google Material Font Icons - Outlined'),
    '#default_value' => theme_get_setting('material_icon_outlined'),
    '#description'   => t('Check this option to enable Google Material Outlined Font Icons. Uncheck to disable.'),
  ];
  $form['components']['font_icons']['material']['material_icon_filled'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Google Material Font Icons - Filled'),
    '#default_value' => theme_get_setting('material_icon_filled'),
    '#description'   => t('Check this option to enable Google Material Filled Font Icons. Uncheck to disable.'),
  ];
  $form['components']['font_icons']['material']['material_icon_round'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Google Material Font Icons - Round'),
    '#default_value' => theme_get_setting('material_icon_round'),
    '#description'   => t('Check this option to enable Google Material Round Font Icons. Uncheck to disable.'),
  ];
  $form['components']['font_icons']['material']['material_icon_sharp'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Google Material Font Icons - Sharp'),
    '#default_value' => theme_get_setting('material_icon_sharp'),
    '#description'   => t('Check this option to enable Google Material Sharp Font Icons. Uncheck to disable.'),
  ];
  $form['components']['font_icons']['material']['material_icon_tone'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Google Material Font Icons - Two Tone'),
    '#default_value' => theme_get_setting('material_icon_tone'),
    '#description'   => t('Check this option to enable Google Material Two Tone Font Icons. Uncheck to disable.'),
  ];
  // Content -> font icons - iconmonstr
  $form['components']['font_icons']['font_icons_iconmonstr'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Iconmonstr Font Icons'),
    '#description'   => t('<br /><hr /><br />MiliPro theme has included iconmonstr font icons v1.3.0. You can create 300+ icons with iconmonstr font icons.<br />Please visit this tutorial page for details. <a href="https://drupar.com/custom-shortcodes-set-two/iconmonstr-font-icons" target="_blank">How To Use Iconmonstr Icons</a>.<br /><strong>Please Note:</strong> This will increase page load by about 50 KB.'),
  ];
  $form['components']['font_icons']['font_icons_iconmonstr']['iconmonstr'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Iconmonstr Font Icons'),
    '#default_value' => theme_get_setting('iconmonstr', 'milipro'),
    '#description'   => t("Check this option to enable Iconmonstr Font Icons. Uncheck to disable."),
  ];
  // Components -> Page loader.
  $form['components']['preloader'] = [
    '#type'        => 'details',
    '#title'       => t('Pre Page Loader'),
    '#group' => 'components_tab',
  ];
  $form['components']['preloader']['preloader_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Pre Page Loader'),
    '#open' => true,
  ];
  $form['components']['preloader']['preloader_section']['preloader_option'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show a loading icon before page loads.'),
    '#default_value' => theme_get_setting('preloader_option', 'milipro'),
    '#description'   => t('Check this option to show a cool animated image until your website is loading. Uncheck to disable this feature. For more details, please refer to the <a href="#" target="_blank">documentation page</a>.'),
  ];
  /**
   * Settings under Custom Styling tab.
   */
  $form['insert_codes']['insert_codes_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Insert Codes -> CSS
  $form['insert_codes']['css'] = [
    '#type'        => 'details',
    '#title'       => t('CSS Codes'),
    '#group'       => 'insert_codes_tab',
  ];
  // Insert Codes -> Head
  $form['insert_codes']['head'] = [
    '#type'        => 'details',
    '#title'       => t('Head'),
    '#description' => t('<h3>Insert Codes Before &lt;/HEAD&gt;</h3><hr />'),
    '#group' => 'insert_codes_tab',
  ];
  // Insert Codes -> Body
  $form['insert_codes']['body'] = [
    '#type'        => 'details',
    '#title'       => t('Body'),
    '#group' => 'insert_codes_tab',
  ];
  // Insert Codes -> css
  $form['insert_codes']['css']['css_custom'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Addtional CSS'),
  ];

  $form['insert_codes']['css']['css_custom']['styling'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable custom css'),
    '#default_value' => theme_get_setting('styling', 'milipro'),
    '#description'   => t("Check this option to enable custom styling. Uncheck to disable this feature.<br />Please refer to this tutorial page. <a href='https://drupar.com/milipro-documentation/custom-css' target='_blank'>How To Use Custom Styling</a>"),
  ];

  $form['insert_codes']['css']['css_custom']['styling_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Custom CSS Codes'),
    '#default_value' => theme_get_setting('styling_code', 'milipro'),
    '#description'   => t('Please enter your custom css codes in this text box. You can use it to customize the appearance of your site.<br />Please refer to this tutorial for detail: <a href="https://drupar.com/milipro-documentation/custom-css" target="_blank">Custom CSS</a>'),
  ];
  // Insert Codes -> Head -> Head codes
  $form['insert_codes']['head']['insert_head'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable custom codes in &lt;head&gt; section'),
    '#default_value' => theme_get_setting('insert_head'),
    '#description'   => t("Check this option to enable custom codes in &lt;head&gt; section. Uncheck to disable this feature."),
  ];
  $form['insert_codes']['head']['head_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('&lt;head&gt; Codes'),
    '#default_value' => theme_get_setting('head_code'),
    '#description'   => t("Please enter your custom codes for &lt;head&gt; section. These codes will be inserted just before <strong>&lt;/head&gt;</strong>."),
  ];
  // Insert Codes -> Body -> Body start codes
  $form['insert_codes']['body']['insert_body_start_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code after &lt;BODY&gt; tag'),
  ];
  $form['insert_codes']['body']['insert_body_start_section']['insert_body_start'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable custom codes after &lt;body&gt; tag'),
    '#default_value' => theme_get_setting('insert_body_start'),
    '#description'   => t("Check this option to enable custom codes after &lt;body&gt; tag. Uncheck to disable this feature."),
  ];
  $form['insert_codes']['body']['insert_body_start_section']['body_start_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Codes'),
    '#default_value' => theme_get_setting('body_start_code'),
    '#description'   => t("Please enter your custom codes after &lt;body&gt; tag. These codes will be inserted just after <strong>&lt;body&gt;</strong> tag."),
  ];
  // Insert Codes -> Body -> Body end codes
  $form['insert_codes']['body']['insert_body_end_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code before &lt;/BODY&gt; tag'),
  ];
  $form['insert_codes']['body']['insert_body_end_section']['insert_body_end'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable custom codes before &lt;/body&gt; tag.'),
    '#default_value' => theme_get_setting('insert_body_end'),
    '#description'   => t("Check this option to enable custom codes before &lt;/body&gt; tag. Uncheck to disable this feature."),
  ];
  $form['insert_codes']['body']['insert_body_end_section']['body_end_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Codes'),
    '#default_value' => theme_get_setting('body_end_code'),
    '#description'   => t("Please enter your custom codes before &lt;/body&gt; tag. These codes will be inserted just before <strong>&lt;/body&gt;</strong>."),
  ];

  /**
   * Settings under License tab.
   */
  $form['license']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('License Type'),
    '#description' => t('<p>Your theme license is: <strong>Single Domain License</strong></p>
    <p>You are allowed to use this theme on a single website.</p>
    <hr /><br /><a href="https://drupar.com/upgrade/milipro" target="_blank">Upgrade to unlimited domain license</a>. Upgrade fee is $30 only.'),
  ];
  $form['license']['upgrade'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Upgrade'),
    '#description' => t('<p>You can upgrade to unlimited domain license. Upgrade price is $30 only.</p><p><hr /></p><p><a href="https://drupar.com/upgrade/milipro" target="_blank">Upgrade to unlimited domain license</a>.</p>'),
  ];
  /**
   * Settings under update tab.
   */
  $form['update']['update_version'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Current Theme Version'),
    '#description' => t("$ver"),
  ];
  $form['update']['update_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Latest MiliPro Version'),
    '#description' => t("<pre>$theme_update_info</pre>"),
  ];
  // Settings under support tab.
  $form['support']['info'] = [
    '#type'        => 'fieldset',
    '#description' => t('<h4>Documentation</h4>
    <p>We have a detailed documentation about how to use theme. Please read the <a href="https://drupar.com/milipro-documentation" target="_blank">MiliPro Theme Documentation</a>.</p>
    <hr />
    <h4>Open Support Ticket</h4>
    <p>If you need support that is beyond our theme documentation, please open a support ticket.<br /><a href="https://drupar.com/node/add/ticket" target="_blank">Create a support ticket</a></p>'),
  ];
// End form.
}
