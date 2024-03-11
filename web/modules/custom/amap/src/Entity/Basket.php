<?php

namespace Drupal\amap\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\amap\BasketInterface;

/**
 * Defines the basket entity class.
 *
 * @ContentEntityType(
 *   id = "basket",
 *   label = @Translation("Basket"),
 *   label_collection = @Translation("Baskets"),
 *   label_singular = @Translation("basket"),
 *   label_plural = @Translation("baskets"),
 *   label_count = @PluralTranslation(
 *     singular = "@count baskets",
 *     plural = "@count baskets",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\amap\BasketListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\amap\BasketAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\amap\Form\BasketForm",
 *       "edit" = "Drupal\amap\Form\BasketForm",
 *       "delete" = "Drupal\amap\Form\BasketDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\amap\BasketHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "basket",
 *   admin_permission = "administer basket entities",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "collection" = "/amap/basket",
 *     "add-form" = "/amap/basket/add",
 *     "canonical" = "/basket/{basket}",
 *     "edit-form" = "/amap/basket/{basket}/edit",
 *     "delete-form" = "/amap/basket/{basket}/delete",
 *   },
 *   field_ui_base_route = "basket.settings"
 * )
 */
class Basket extends ContentEntityBase implements BasketInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);
    // ----------------------------------------------------------------------------
    $weight = 0;
    // ----------------------------------------------------------------------------
    $weight++;
    $fields['distributiondate'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Distribution Date'))
      ->setSetting('target_type', 'distribution_date')
      ->setSetting('handler', 'views')
      ->setSetting('handler_settings', [
        'view' => [
          'view_name' => 'amap_distributions',
          'display_name' => 'entity_reference_1',
        ],
      ])
      ->setDisplayOptions('view', [
        'weight' => $weight,
      ])
      ->setDisplayOptions('form', [
        'weight' => $weight,
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
    // ----------------------------------------------------------------------------
    $weight++;
    $fields['product'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Product'))
      ->setRequired(TRUE)
      ->setSettings(['allowed_values_function' => 'amap_allowed_values_function'])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => $weight,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => $weight,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // ----------------------------------------------------------------------------
    $weight++;
    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Quantity or description'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => $weight,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => $weight,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // ----------------------------------------------------------------------------
    $weight++;
    $fields['price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Price'))
      ->setSettings([
        'precision' => 4,
        'scale' => 2,
        'min' => 0,
      ])
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => $weight,
      ])
      ->setDisplayOptions('form', [
        'weight' => $weight,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // ----------------------------------------------------------------------------
    $weight++;
    $fields['comment'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Comment'))
      ->setSettings([
        'max_length' => 1024,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'type' => 'string_long',
        'weight' => $weight,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => $weight,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // ----------------------------------------------------------------------------
    $fields['seller'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Seller'))
      ->setSetting('target_type', 'person')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\amap\Entity\Basket::getCurrentUserId')
      ->setReadOnly(TRUE);
    // ----------------------------------------------------------------------------
    $fields['buyer'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Buyer'))
      ->setSettings([
        'max_length' => 1024,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setReadOnly(TRUE);
    // ----------------------------------------------------------------------------
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'));
    // ----------------------------------------------------------------------------
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'));
    // ----------------------------------------------------------------------------
    return $fields;
  }

  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
