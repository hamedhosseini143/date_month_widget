<?php

namespace Drupal\date_month_widget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeWidgetBase;

/**
 * Plugin implementation of the 'date_month_widget' widget.
 *
 * @FieldWidget(
 *   id = "date_month_widget",
 *   module = "date_month_widget",
 *   label = @Translation("Date month widget"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateMonthWidget extends DateTimeWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'increment' => '15',
        'date_order' => 'YD',
        'time_type' => '24',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Wrap all of the select elements with a fieldset.
    $element['#theme_wrappers'][] = 'fieldset';

    $date_order = $this->getSetting('date_order');

    if ($this->getFieldSetting('datetime_type') == 'datetime') {
      $time_type = $this->getSetting('time_type');
      $increment = $this->getSetting('increment');
    }
    else {
      $time_type = '';
      $increment = '';
    }

    // Set up the date part order array.
    switch ($date_order) {
      case 'YD':
        $date_part_order = ['year', 'month'];
        break;

      case 'MY':
        $date_part_order = ['month', 'year'];
        break;

    }

    $element['value'] = [
        '#type' => 'datelist',
        '#date_increment' => $increment,
        '#date_part_order' => ['year', 'month'],
      ] + $element['value'];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['date_order'] = [
      '#type' => 'select',
      '#title' => t('Date part order'),
      '#default_value' => $this->getSetting('date_order'),
      '#options' => ['MY' => t('Month/Year'), 'YM' => t('Year/Month')],
    ];

    if ($this->getFieldSetting('datetime_type') == 'datetime') {
      $element['time_type'] = [
        '#type' => 'select',
        '#title' => t('Time type'),
        '#default_value' => $this->getSetting('time_type'),
        '#options' => ['24' => t('24 hour time'), '12' => t('12 hour time')],
      ];

      $element['increment'] = [
        '#type' => 'select',
        '#title' => t('Time increments'),
        '#default_value' => $this->getSetting('increment'),
        '#options' => [
          1 => t('1 minute'),
          5 => t('5 minute'),
          10 => t('10 minute'),
          15 => t('15 minute'),
          30 => t('30 minute'),
        ],
      ];
    }
    else {
      $element['time_type'] = [
        '#type' => 'hidden',
        '#value' => 'none',
      ];

      $element['increment'] = [
        '#type' => 'hidden',
        '#value' => $this->getSetting('increment'),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Date part order: @order', ['@order' => $this->getSetting('date_order')]);
    if ($this->getFieldSetting('datetime_type') == 'datetime') {
      $summary[] = t('Time type: @time_type', ['@time_type' => $this->getSetting('time_type')]);
      $summary[] = t('Time increments: @increment', ['@increment' => $this->getSetting('increment')]);
    }

    return $summary;
  }

}
