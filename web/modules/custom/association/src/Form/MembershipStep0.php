<?php

namespace Drupal\association\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Class MembershipStep0.
 */
class MembershipStep0 extends FormBase {

  protected $step = 0;

  public function getFormId() {
    return 'membership_step0';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['step'] = [
      '#type' => 'hidden',
      '#value' => [$this->step],
    ];

    switch ($this->step) {
      case 0:
        if ($this->currentUser()->isAnonymous()) {
          $form['didyouread'] = [
            '#type' => 'radios',
            '#title' => $this->t('Did you read Frequently Asked Questions (FAQ)?'),
            '#options' => [
              0 => $this->t('No'),
              1 => $this->t('Yes'),
            ],
            '#required' => TRUE,
          ];
          $form['didyoumeet'] = [
            '#type' => 'radios',
            '#title' => $this->t('Did you meet someone of the organisation?'),
            '#options' => [
              0 => $this->t('No'),
              1 => $this->t('Yes'),
            ],
            '#required' => TRUE,
          ];
          $form['areyousure'] = [
            '#type' => 'radios',
            '#title' => $this->t('Are you sure about this?'),
            '#options' => [
              0 => $this->t('No'),
              1 => $this->t('Yes'),
            ],
            '#required' => TRUE,
          ];
          $form['mode'] = [
            '#type' => 'radios',
            '#title' => $this->t('Mode of membership'),
            '#options' => [
              0 => $this->t('Online'),
              1 => $this->t('Membership form'),
            ],
            '#required' => TRUE,
          ];
          $nextStepNotice = $this->t('enter');
          $markup0 = $this->t('After submitting this form, you will be redirected to the membership process where you can @str your personal information then choose your subscription payment mode.', [
              '@str' => $nextStepNotice,
            ]) . '<BR><BR>';
          $form['footer0'] = [
            '#type' => 'item',
            '#markup' => $markup0,
            '#states' => [
              'visible' => [
                ':input[name="mode"]' => ['value' => 0],
              ],
            ],
          ];
          $markup1 = $this->t('After submitting this form, you will be redirected to a page where you can download the membership form.') . '<BR><BR>';
          $form['footer1'] = [
            '#type' => 'item',
            '#markup' => $markup1,
            '#states' => [
              'visible' => [
                ':input[name="mode"]' => ['value' => 1],
              ],
            ],
          ];
        }
        else {
          $storage = Drupal::entityTypeManager()->getStorage('person');
          $person = $storage->load($this->currentUser()->id());
          $member_id = $person->get("member_id")->target_id;
          if ($member_id) {
            $database = Drupal::database();
            $query = $database->select('member', 'am');
            $query->leftJoin('person', 'ap', 'ap.member_id = am.id');
            $query->leftJoin('person__field_sel_isseliste', 'ps', 'ap.id = ps.entity_id');
            $query->leftJoin('users_field_data', 'us', 'us.uid = ps.entity_id');
            $query->fields('am', [
              'id',
              'designation',
              'addresssupplement',
              'street',
              'postalcode',
              'city',
              'contact_id',
              'telephone',
              'status',
            ]);
            $query->fields('ap', [
              'id',
              'lastname',
              'firstname',
              'email',
              'cellphone',
              'iscontact',
            ]);
            $query->fields('ps', ['field_sel_isseliste_value',]);
            $query->fields('us', ['uid', 'name']);
            $query->condition('am.id', $member_id, '=');
            $query->orderBy('iscontact', 'DESC');
            $results = $query->execute()->fetchAll();
            $form_state->set('am_id', $results[0]->id);
            $form_state->set('designation', $results[0]->designation);
            $form_state->set('addresssupplement', $results[0]->addresssupplement);
            $form_state->set('street', $results[0]->street);
            $form_state->set('postalcode', $results[0]->postalcode);
            $form_state->set('city', $results[0]->city);
            $form_state->set('telephone', $results[0]->telephone);
            $form_state->set('status', $results[0]->status);
            $form_state->set('ap_id1', $results[0]->ap_id);
            $form_state->set('lastname1', $results[0]->lastname);
            $form_state->set('firstname1', $results[0]->firstname);
            $form_state->set('email1', $results[0]->email);
            $form_state->set('cellphone1', $results[0]->cellphone);
            $form_state->set('seliste1', $results[0]->field_sel_isseliste_value);
            $form_state->set('name1', $results[0]->name);
            if (count($results) > 1) {
              $form_state->set('ap_id2', $results[1]->ap_id);
              $form_state->set('lastname2', $results[1]->lastname);
              $form_state->set('firstname2', $results[1]->firstname);
              $form_state->set('email2', $results[1]->email);
              $form_state->set('cellphone2', $results[1]->cellphone);
              $form_state->set('seliste2', $results[1]->field_sel_isseliste_value);
              $form_state->set('name2', $results[1]->name);
            }
            $iTemp = ($this->currentUser()
                ->getDisplayName() == $results[0]->name) ? 0 : 1;
            $form_state->set('undersigned', $iTemp);
            $form_state->set('lastname', $results[$iTemp]->lastname);
            $form_state->set('firstname', $results[$iTemp]->firstname);
            $form_state->set('email', $results[$iTemp]->email);
          }
          else {
            $form_state->set('status', 4);
          }
          $nextStepNotice = $this->t('correct, if needed,');
          $config = Drupal::config('association.renewalperiod');
          $rpYear = $config->get('year');
          $rpStatus = $config->get('status');

          switch (TRUE) {
            case ($rpStatus == 'Closed'):
              $form['footer'] = [
                '#markup' => $this->t("There is currently no renewal period opened.") . '<BR>',
              ];
              break;
            case (!$member_id):
            case (!$this->currentUser()->hasPermission("renew membership")):
              $form['footer'] = [
                '#markup' => $this->t("You're not allowed to renew membership. Only the persons associated to a member are allowed to do it.") . '<BR>',
              ];
              break;
            default:
              switch ($form_state->getStorage()['status']) {
                case 1:
                  $iWish = 0;
                  break;
                case 3:
                  $iWish = 1;
                  break;
                case 2:
                default:
                  $iWish = NULL;
                  break;
              }
              $sMember = new FormattableMarkup('<span style="color: #0000ff;">' . $form_state->getStorage()['designation'] . '</span>', []);
              $sPerson = new FormattableMarkup('<span style="color: #0000ff;">' . $form_state->getStorage()['firstname'] . ' ' . $form_state->getStorage()['lastname'] . '</span>', []);
              if ($form_state->getStorage()['status'] == 4) {
                $sTemp = $this->t('The member «&nbsp;%member&nbsp;» has already renewed his membership to the association <I>Le Jardin de Poissy</I> for year « %year ».', [
                  '%member' => $sMember,
                  '%year' => $rpYear,
                ]);
                $form['header'] = [
                  '#type' => 'inline_template',
                  '#template' => $sTemp,
                ];
              }
              else {
                $sTemp = $this->t("Here’s your wish as recorded. You can change it as many times as you like: only the last change will be taken into account.<BR><BR>");
                $sTemp = ($iWish == -1) ? "" : $sTemp;
                $sTemp2 = $this->t('I, the undersigned «&nbsp;%person&nbsp;», representing the member «&nbsp;%member&nbsp;», wishes to renew my membership to the association <I>Le Jardin de Poissy</I> for year « %year ».', [
                  '%person' => $sPerson,
                  '%member' => $sMember,
                  '%year' => $rpYear,
                ]);
                $sTemp = $sTemp . $sTemp2;
                $form['header'] = [
                  '#type' => 'inline_template',
                  '#template' => $sTemp,
                ];
                $form['suscribe'] = [
                  '#type' => 'radios',
                  '#title' => '',
                  '#options' => [
                    0 => $this->t('No'),
                    1 => $this->t('Yes'),
                  ],
                  '#default_value' => $iWish,
                  '#validated' => TRUE,
                ];
                $markup = $this->t('After submitting this form, you will be able to @str your personal information then choose your subscription payment mode.', [
                    '@str' => $nextStepNotice,
                  ]) . '<BR>';
                $form['suscribedyes'] = [
                  '#type' => 'item',
                  '#markup' => $markup,
                  '#states' => [
                    'visible' => [
                      ':input[name="suscribe"]' => ['value' => 1],
                    ],
                  ],
                ];
              }
          }
        }
        break;

      case 1:
        $weight = 0;
        $form['person1'] = [
          '#type' => 'fieldset',
          '#title' => t('Person') . ' 1 (' . t('Contact') . ') <a class="use-ajax" data-dialog-options="{&quot;width&quot;:440}" data-dialog-type="modal" href="/node/139" id="modal-dialog"><img src="/sites/default/files/images/info.svg"></a>',
        ];
        $form['person1']['#attached'] = ['library' => ['core/drupal.dialog.ajax']];
        $weight++;
        $temp = isset($form_state->getStorage()['lastname1']) ? $form_state->getStorage()['lastname1'] : '';
        $form['person1']['lastname1'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Last Name'),
          '#size' => 32,
          '#required' => TRUE,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['firstname1']) ? $form_state->getStorage()['firstname1'] : '';
        $form['person1']['firstname1'] = [
          '#type' => 'textfield',
          '#title' => $this->t('First Name'),
          '#size' => 32,
          '#required' => TRUE,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['email1']) ? $form_state->getStorage()['email1'] : '';
        $form['person1']['email1'] = [
          '#type' => 'email',
          '#title' => $this->t('Email'),
          '#size' => 64,
          '#required' => TRUE,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['cellphone1']) ? $form_state->getStorage()['cellphone1'] : '';
        $form['person1']['cellphone1'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Cellphone'),
          '#size' => 16,
          '#required' => TRUE,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['seliste1']) ? $form_state->getStorage()['seliste1'] : 0;
        $form['person1']['seliste1'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I wish to be SÉListe'),
          '#default_value' => $temp,
          '#weight' => $weight,
        ];
        $weight++;
        $form['person2'] = [
          '#type' => 'fieldset',
          '#title' => t('Person') . ' 2 ',
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['lastname2']) ? $form_state->getStorage()['lastname2'] : '';
        $form['person2']['lastname2'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Last Name'),
          '#size' => 32,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['firstname2']) ? $form_state->getStorage()['firstname2'] : '';
        $form['person2']['firstname2'] = [
          '#type' => 'textfield',
          '#title' => $this->t('First Name'),
          '#size' => 32,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['email2']) ? $form_state->getStorage()['email2'] : '';
        $form['person2']['email2'] = [
          '#type' => 'email',
          '#title' => $this->t('Email'),
          '#size' => 64,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['cellphone2']) ? $form_state->getStorage()['cellphone2'] : '';
        $form['person2']['cellphone2'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Cellphone'),
          '#size' => 26,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['seliste2']) ? $form_state->getStorage()['seliste2'] : 0;
        $form['person2']['seliste2'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I wish to be SÉListe'),
          '#default_value' => $temp,
          '#weight' => $weight,
        ];
        if (!$this->currentUser()->isAnonymous()) {
          $weight++;
          $temp = isset($form_state->getStorage()['newcontact']) ? $form_state->getStorage()['newcontact'] : 0;
          $form['person2']['newcontact'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('I wish to be contact for member'),
            '#default_value' => $temp,
            '#weight' => $weight,
          ];
        }
        break;

      case 2:
        $weight = 0;
        $weight++;
        $temp = $form_state->getStorage()['lastname1'] . ' ' . $form_state->getStorage()['firstname1'];
        if ($form_state->getStorage()['lastname2'] != "") {
          if ($form_state->getStorage()['lastname2'] == $form_state->getStorage()['lastname1']) {
            $temp .= ' et ' . $form_state->getStorage()['firstname2'];
          }
          else {
            $temp .= ' et ' . $form_state->getStorage()['lastname2'] . ' ' . $form_state->getStorage()['firstname2'];
          }
        }
        $form['designation'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Designation'),
          '#size' => 64,
          '#required' => TRUE,
          '#default_value' => $temp,
          '#weight' => $weight,
        ];
        $form['address'] = [
          '#type' => 'fieldset',
          '#title' => t('Address'),
          '#weight' => $weight,
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['addresssupplement']) ? $form_state->getStorage()['addresssupplement'] : '';
        $form['address']['addresssupplement'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Address supplement'),
          '#size' => 64,
          '#default_value' => $temp,
          '#placeholder' => 'Batiment B',
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['street']) ? $form_state->getStorage()['street'] : '';
        $form['address']['street'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Street'),
          '#size' => 64,
          '#required' => TRUE,
          '#placeholder' => '28 bis boulevard Victor Hugo',
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['postalcode']) ? $form_state->getStorage()['postalcode'] : '';
        $form['address']['postalcode'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Postal Code'),
          '#size' => 10,
          '#required' => TRUE,
          '#placeholder' => '78300',
          '#default_value' => $temp,
          '#weight' => $weight,
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['city']) ? $form_state->getStorage()['city'] : '';
        $form['address']['city'] = [
          '#type' => 'textfield',
          '#title' => $this->t('City'),
          '#size' => 64,
          '#required' => TRUE,
          '#placeholder' => 'Poissy',
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        $weight++;
        $temp = isset($form_state->getStorage()['telephone']) ? $form_state->getStorage()['telephone'] : '';
        $form['telephone'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Landline Phone'),
          '#size' => 16,
          '#default_value' => $temp,
          '#weight' => $weight,
          '#attributes' => ['onchange' => 'hasChanged(this)',],
        ];
        break;

      case 3:
        $form['#prefix'] = '<div id="membership_step3">';
        $form['#suffix'] = '</div>';
        $form['anonymous'] = [
          '#type' => 'hidden',
          '#value' => [$this->currentUser()->isAnonymous() ? 'Y' : 'N'],
        ];
        $i1 = $form_state->get('undersigned') + 1;
        $markup = implode("", [
          $form_state->getStorage()['lastname' . $i1],
          ' ',
          $form_state->getStorage()['firstname' . $i1],
          ' (',
          $form_state->getStorage()['cellphone' . $i1],
          ' - ',
          $form_state->getStorage()['email' . $i1],
          ')',
        ]);
        $form['person1'] = [
          '#type' => 'item',
          '#markup' => $markup,
        ];
        $markup = $form_state->getStorage()['designation'];
        $form['member'] = [
          '#type' => 'item',
          '#markup' => $markup,
        ];
        $i2 = ($i1 % 2) + 1;
        $markup = implode("", [
          $form_state->getStorage()['lastname' . $i2],
          ' ',
          $form_state->getStorage()['firstname' . $i2],
          ' (',
          $form_state->getStorage()['cellphone' . $i2],
          ' - ',
          $form_state->getStorage()['email' . $i2],
          ')',
        ]);
        $markup = $form_state->getStorage()['lastname2'] ? $markup : '';
        $form['person2'] = [
          '#type' => 'item',
          '#markup' => $markup,
        ];
        $markup = implode("", [
          $form_state->getStorage()['addresssupplement'] ? $form_state->getStorage()['addresssupplement'] . '<BR>' : '',
          $form_state->getStorage()['street'] . '<BR>',
          $form_state->getStorage()['postalcode'] . ' ' . $form_state->getStorage()['city'] . '<BR>',
          $form_state->getStorage()['telephone'] ? $form_state->getStorage()['telephone'] : '',
        ]);
        $form['address'] = [
          '#type' => 'item',
          '#markup' => $markup,
        ];
        $form['commitment1'] = [
          '#type' => 'checkbox',
          '#required' => TRUE,
          '#required_error' => t('You must commit to observe the statuses of the association.'),
        ];
        $form['authorisation'] = [
          '#type' => 'checkbox',
          '#required' => TRUE,
          '#required_error' => t('You must authorise the association to use your personal information.'),
        ];
        $form['commitment2'] = [
          '#type' => 'checkbox',
          '#required' => TRUE,
          '#required_error' => t('You must commit not to use the information of other members for personal purposes.'),
        ];
        $form['sel'] = [
          '#type' => 'checkboxes',
          '#options' => [
            'sel1' => $form_state->getStorage()['lastname1'] . ' ' . $form_state->getStorage()['firstname1'],
            'sel2' => $form_state->getStorage()['lastname2'] ? $form_state->getStorage()['lastname2'] . ' ' . $form_state->getStorage()['firstname2'] : '',
          ],
          '#default_value' => [
            $form_state->getStorage()['seliste1'] ? 'sel1' : 0,
            $form_state->getStorage()['seliste2'] ? 'sel2' : 0,
          ],
          '#disabled' => TRUE,
        ];
        $form['amap'] = ['#type' => 'checkbox',];
        $form['amap_legumes'] = ['#type' => 'checkbox',];
        $form['amap_fruits'] = ['#type' => 'checkbox',];
        $form['amap_pain'] = ['#type' => 'checkbox',];
        $form['amap_viandedebœuf'] = ['#type' => 'checkbox',];
        $form['amap_œufs'] = ['#type' => 'checkbox',];
        $form['amap_volaille'] = ['#type' => 'checkbox',];
        $form['amap_champignons'] = ['#type' => 'checkbox',];
        $form['amap_lentilles'] = ['#type' => 'checkbox',];
        $form['amap_viandedeporc'] = ['#type' => 'checkbox',];
        $form['amap_produitslaitiersvache'] = ['#type' => 'checkbox',];
        $form['amap_pommesdeterre'] = ['#type' => 'checkbox',];
        $form['amap_produitslaitiersbrebis'] = ['#type' => 'checkbox',];
        $form['amap_farine'] = ['#type' => 'checkbox',];
        $form['amap_miel'] = ['#type' => 'checkbox',];
        $form['amap_jusdepomme'] = ['#type' => 'checkbox',];
        $form['amap_cidre'] = ['#type' => 'checkbox',];
        $form['how_boucheaoreille'] = ['#type' => 'checkbox',];
        $form['how_moteurderechercheinternet'] = ['#type' => 'checkbox',];
        $form['how_pagefacebook'] = ['#type' => 'checkbox',];
        $form['how_pageinstagram'] = ['#type' => 'checkbox',];
        $form['how_autre'] = ['#type' => 'textfield', '#size' => 64,];
        $form['ifbasket'] = ['#type' => 'checkbox',];
        $form['payment'] = [
          '#type' => 'radios',
          '#title' => $this->t('Subscription Payment Method'),
          '#options' => [
            0 => $this->t('by check'),
            1 => $this->t('by bank transfer'),
            2 => $this->t('by card'),
          ],
          '#required' => TRUE,
          '#required_error' => t('You must choose a subscription payment method.'),
        ];
        $markup = $this->t('After submitting this form, you will be redirected to the online payment process.');
        $form['paymentcrd'] = [
          '#type' => 'item',
          '#states' => [
            'visible' => [
              ':input[name="payment"]' => ['value' => 2],
            ],
          ],
        ];
        break;

      case 4:
        break;

      default:

    }

    if ($this->step > 0) {
      $form['previous'] = [
        '#type' => 'submit',
        '#value' => $this->t('Previous'),
        '#submit' => ['::goto_previous_step'],
        '#limit_validation_errors' => [],
        '#weight' => 98,
      ];
    }

    switch ($this->step) {
      case 0:
        if (!$this->currentUser()->isAnonymous()) {
          if ($form_state->getStorage()['status'] == 4) {
            $label = '';
            $type = 'hidden';
          }
          else {
            $label = $this->t('Submit');
            $type = 'submit';
          }
        }
        else {
          $label = $this->t('Submit');
          $type = 'submit';
        }
        break;
      case 1:
      case 2:
        $label = $this->t('Next');
        $type = 'submit';
        break;
      case 3:
        $label = $this->t('Submit');
        $type = 'button';
        break;
      default:
    }
    if ($this->step == 3) {
      $form['submit'] = [
        '#type' => $type,
        '#value' => $label,
        '#weight' => 99,
        '#ajax' => [
          'wrapper' => 'membership_step3',
          'callback' => '::ajaxSubmit',
        ],
      ];
    }
    else {
      $form['submit'] = [
        '#type' => $type,
        '#value' => $label,
        '#weight' => 99,
      ];
    }

    $form['#attached']['library'][] = 'association/membership';

    return $form;
  }

  public function goto_previous_step($form, $form_state) {
    $form_state->setRebuild();
    $this->step = $this->step - 1;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    switch ($this->step) {
      case 0:
        if ($this->currentUser()->isAnonymous()) {
          if ($form_state->getValue('didyouread') == '0') {
            $form_state->setErrorByName('didyouread', $this->t('Some of your questions might be unanswered, maybe you should not apply now.'));
          }
          if ($form_state->getValue('didyoumeet') == '0') {
            $form_state->setErrorByName('didyoumeet', $this->t('Without meeting someone of our organisation, maybe you should not apply now.'));
          }
          if ($form_state->getValue('areyousure') == '0') {
            $form_state->setErrorByName('areyousure', $this->t('If you\'re not sure, maybe you should not apply now.'));
          }
        }
        else {
          if ($form_state->getValue('suscribe') == NULL) {
            $form_state->setErrorByName('suscribe', $this->t('Please choose one option.'));
          }
        }
        break;

      case 1:
        $values = $form_state->cleanValues()->getValues();
        $person2 = array_intersect_key($values, array_flip([
          'lastname2',
          'firstname2',
          'email2',
          'cellphone2',
        ]));
        if (implode("", $person2)) {
          $message = $this->t('If one of the fields Lastname, Firstname, Email, Cellphone is filled, the others must be filled too.');
          if (!$form_state->getValue('lastname2') || !$form_state->getValue('firstname2') || !$form_state->getValue('email2') || !$form_state->getValue('cellphone2')) {
            $form_state->setErrorByName('lastname2', $message);
            $form_state->setErrorByName('firstname2');
            $form_state->setErrorByName('email2');
            $form_state->setErrorByName('cellphone2');
          }
        }
        $email1 = $form_state->getValue('email1');
        if ($this->currentUser()->isAnonymous()) {
          $sTemp = $this->_existsEmail($email1);
          if ($sTemp) {
            $form_state->setErrorByName('email1', $sTemp);
          }
        }
        $email2 = $form_state->getValue('email2');
        if ($email2) {
          if ($email2 == $email1) {
            $message = $this->t('The two persons can\'t have the same email address.');
            $form_state->setErrorByName('email2', $message);
          }
          else {
            if ($this->currentUser()
                ->isAnonymous() || (is_null($form_state->getStorage()['ap_id2']))) {
              $sTemp = $this->_existsEmail($email2);
              if ($sTemp) {
                $form_state->setErrorByName('email2', $sTemp);
              }
            }
          }
        }
        $cellphone2 = $form_state->getValue('cellphone2');
        if ($cellphone2) {
          if ($cellphone2 == $form_state->getValue('cellphone1')) {
            $message = $this->t('The two persons can\'t have the same cellphone number.');
            $form_state->setErrorByName('cellphone2', $message);
          }
        }
        break;

      case 2:
      case 4:
        break;

      case 3:
        if ($form_state->getValue('amap') == '1') {
          if ($form_state->getValue('ifbasket') == '0') {
            $form_state->setErrorByName('ifbasket', $this->t('You must commit to distributions and educational workshops.'));
          }
        }
        break;

      default:

    }
    parent::validateForm($form, $form_state);
  }

  public function _existsEmail($email) {
    $database = Drupal::database();
    $query = $database->select('users_field_data', 'us');
    $query->fields('us', ['uid', 'name', 'mail'])
      ->condition('us.mail', $email, '=');
    $results = $query->execute()->fetchAll();
    if (count($results) == 0) {
      $output = FALSE;
    }
    else {
      $url = Url::fromUri('base:/user/login');
      $link = Drupal\Core\Link::fromTextAndUrl($this->t('here'), $url)
        ->toString();
      $output = $this->t('This email is already registered for « %user ».<BR>If you are already a member, please log in %link.', [
        '%user' => $results[0]->name,
        '%link' => $link,
      ]);
    }
    return $output;
  }

  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      $messages = Drupal::messenger()->deleteAll();
      $messages = [
        '#theme' => 'status_messages',
        '#message_list' => $messages,
      ];
      $response->addCommand(new ReplaceCommand('#messages', ''));
      $response->addCommand(new AppendCommand('.region-highlighted', $messages));
    }
    else {
      $contracts = '';
      $how = '';
      $aCleanValues = $form_state->cleanValues()->getValues();
      foreach ($aCleanValues as $key => $value) {
        if (substr($key, 0, 5) == 'amap_' && $value == 1) {
          $contracts .= substr($key, 5, 999) . ', ';
        }
        if (substr($key, 0, 4) == 'how_') {
          if ($value == 1) {
            $how .= substr($key, 4, 999) . ', ';
          }
          elseif ($value != 0) {
            $how .= $value . ', ';
          }
        }
      }
      $contracts = substr($contracts, 0, strlen($contracts) - 2);
      $contracts = ($contracts) ? 'AMAP : ' . $contracts . '.' : '';
      $aCleanValues['contracts'] = $contracts;
      $how = substr($how, 0, strlen($how) - 2);
      $how = ($how) ? 'Connu par : ' . $how . '.' : '';
      $aCleanValues['how'] = $how;
      $payment = (string) $form['payment']['#title'] . ' : ' . (string) $form['payment']['#options'][$form_state->getValue('payment')] . '.';
      $aCleanValues['payment'] = $payment;
      $form_state->setStorage(array_merge($form_state->getStorage(), $aCleanValues));
      $this->saveData($form_state);
      switch ($form_state->getValue('payment')) {
        case '2':
          $session = \Drupal::request()->getSession();
          $session->set('association.anonymous', $this->currentUser()
            ->isAnonymous());
          $session->set('association.designation', $form_state->getStorage()['designation']);
          $session->set('association.lastname', $form_state->getStorage()['lastname']);
          $session->set('association.firstname', $form_state->getStorage()['firstname']);
          $session->set('association.email', $form_state->getStorage()['email']);
          $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand(Url::fromRoute('association.membership4')
            ->toString()));
          break;
        default:
          $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand(Url::fromRoute('<front>')
            ->toString()));
      }
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   * $form_state->set('key', 'value'). The value ends up in
   * $form_state->getStorage()['value'].
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $aCleanValues = $form_state->cleanValues()->getValues();
    switch ($this->step) {
      case 0:
        if (!$this->currentUser()->isAnonymous()) {
          switch ($form_state->getValue('suscribe')) {
            case 0:
              $form_state->set('status', 1);
              $this->saveData($form_state);
              $form_state->setRedirectUrl(Url::fromRoute('<front>'));
              return;
              break;
            case 1:
              $form_state->set('status', 3);
              break;
            default:
              $form_state->setRedirectUrl(Url::fromRoute('<front>'));
              return;
          }
        }
        else {
          if ($aCleanValues["mode"] == "1") {
            $form_state->setRedirectUrl(Url::fromUri('base:/MembershipWithForm'));
            return;
          }
        }
      case 1:
        if ($this->currentUser()->isAnonymous()) {
          $aCleanValues['lastname'] = $form_state->getValue('lastname1');
          $aCleanValues['firstname'] = $form_state->getValue('firstname1');
          $aCleanValues['email'] = $form_state->getValue('email1');
        }
      case 2:
        $form_state->setStorage(array_merge($form_state->getStorage(), $aCleanValues));
        $form_state->setRebuild();
        $this->step++;
        break;
      case 3:
        break;
      default:
    }

  }

  public function saveData(FormStateInterface $form_state) {

    $database = Drupal::database();

    if ($this->step == 0) {

      $database->update('member')
        ->condition('id', $form_state->getStorage()['am_id'])
        ->fields(['status' => $form_state->getStorage()['status']])
        ->execute();

    }
    else {

      $numberOfPersons = $form_state->getStorage()['lastname2'] ? 2 : 1;
      $now = Drupal::time()->getRequestTime();
      $anon = $this->currentUser()->isAnonymous();

      // User(s) --------------------------------------------------------------
      $uid = [];
      for ($i = 1; $i <= $numberOfPersons; $i++) {
        if ($anon || ($i == 2 && is_null($form_state->getStorage()['ap_id2']))) {
          $user = User::create();
          $user->setPassword('passwordtobechanged');
          $user->enforceIsNew();
          $user->setEmail($form_state->getStorage()['email' . $i]);
          $user->setUsername($this->_generateName($form_state->getStorage()['firstname' . $i], $form_state->getStorage()['lastname' . $i]));
          $user->block();
          if ($i == 1) {
            $user->addRole('contact_for_member');
          }
          if ($form_state->getStorage()['seliste' . $i] == 1) {
            $user->addRole('seliste');
          }
          $user->save();
          $uid[$i] = $user->id();
          $mail = _user_mail_notify('register_pending_approval', $user);
        }
        else {
          $uid[$i] = $form_state->getStorage()['ap_id' . $i];
        }
      }

      // Member ---------------------------------------------------------------

      $storage = Drupal::entityTypeManager()
        ->getStorage('member');

      if ($anon) {
        $member = $storage->create();
        $comment = ($form_state->getStorage()['contracts'] ? $form_state->getStorage()['contracts'] . ' ' : '') . ($form_state->getStorage()['how'] ? $form_state->getStorage()['how'] . ' ' : '') . $form_state->getStorage()['payment'];
        $member->comment = $comment;
        $member->contact_id = $uid['1'];
        $member->created = $now;
        $member->enddate = '2037-12-30';
        $member->owner_id = $uid['1'];
        $member->startdate = date('Y-m-d');
        $form_state->set('status', 5);
      }
      else {
        $idM = $form_state->getStorage()['am_id'];
        $member = $storage->load($idM);
      }
      $member->addresssupplement = $form_state->getStorage()['addresssupplement'];
      $member->changed = $now;
      $member->city = $form_state->getStorage()['city'];
      $member->country = 'FR';
      $member->designation = $form_state->getStorage()['designation'];
      $member->postalcode = $form_state->getStorage()['postalcode'];
      $member->street = $form_state->getStorage()['street'];
      $member->telephone = $form_state->getStorage()['telephone'];
      $member->status = $form_state->getStorage()['status'];
      $member->save();
      if ($anon) {
        $idM = $member->id();
      }

      // Person(s) ------------------------------------------------------------

      $storage = Drupal::entityTypeManager()
        ->getStorage('person');

      $idP = [];
      for ($i = 1; $i <= $numberOfPersons; $i++) {
        if ($anon || ($i == 2 && is_null($form_state->getStorage()['ap_id2']))) {
          switch ($i) {
            case 1:
              $person1 = $storage->create();
              $person1->id = $uid[1];
              $person = $person1;
              break;
            case 2:
              $person2 = $storage->create();
              $person2->id = $uid[2];
              $person = $person2;
              break;
            default:
              break;
          }
          $person->comment = NULL;
          $person->created = $now;
          $person->isactive = 0;
          $person->iscontact = $i == 1 ? 1 : 0;
          $person->member_id = $idM;
          $person->owner_id = $uid['1'];
          $person->user_id = $uid[$i];
        }
        else {
          $idP[$i] = $form_state->getStorage()['ap_id' . $i];
          switch ($i) {
            case 1:
              $person1 = $storage->load($idP[1]);
              $person = $person1;
              break;
            case 2:
              $person2 = $storage->load($idP[2]);
              $person = $person2;
              break;
            default:
              break;
          }
        }

        $person->cellphone = $form_state->getStorage()['cellphone' . $i];
        $person->changed = $now;
        $person->email = $form_state->getStorage()['email' . $i];
        $person->firstname = $form_state->getStorage()['firstname' . $i];
        $person->lastname = $form_state->getStorage()['lastname' . $i];
        $person->field_sel_isseliste = $form_state->getStorage()['seliste' . $i];
        if ($form_state->getStorage()['seliste' . $i] == 1) {
          $person->field_sel_balance = 180;
        }
        $person->save();

        $user = User::load($uid[$i]);
        $user->setEmail($form_state->getStorage()['email' . $i]);
        if ($form_state->getStorage()['seliste' . $i] == 1) {
          $user->addRole('seliste');
        }
        else {
          $user->removeRole('seliste');
        }
        $user->save();
      }

      if (!$anon) {
        if ($form_state->getStorage()['newcontact']) {
          $entity = Drupal::entityTypeManager()
            ->getStorage('person')
            ->load($uid[2]);
          _updatePersonToContact($entity);
        }
      }
    }

    // Completion message -----------------------------------------------------
    if ($this->currentUser()->isAnonymous()) {
      $str = $this->t('membership request');
      $sMessage = $this->t('Your @str has been registered.<BR>It will be effective after reception of your payment.', ['@str' => $str]);
      Drupal::messenger()->addMessage($sMessage);
      $sMessage = $this->t('A confimation email has been sent.');
      Drupal::messenger()->addMessage($sMessage);
    }
    else {
      switch ($form_state->getStorage()['status']) {
        case 1:
          $sMessage = $this->t('Your wish has been recorded.');
          break;
        case 3:
          $str = $this->t('membership renewal request');
          $sMessage = $this->t('Your @str has been registered.<BR>It will be effective after reception of your payment.', ['@str' => $str]);
          break;
        default:
          $sMessage = $this->t('Renew membership: Unexpected case.');
      }
      Drupal::messenger()->addMessage($sMessage);
    }

  }

  public function _generateName($firstname, $lastname) {
    $database = Drupal::database();
    $query = $database->select('users_field_data', 'us');
    $query->fields('us', ['name']);
    $query->condition('name', $firstname . '%', 'like');
    $results = $query->execute()->fetchCol();
    for ($i = 0; $i <= strlen($lastname); $i++) {
      $temp = $firstname . strtoupper(substr($lastname, 0, $i));
      $key = array_search($temp, $results);
      if ($key === FALSE) {
        break;
      }
    }
    return $temp;
  }

}

