<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Finding Aid form definition for settings module - with validation.
 *
 * @package    AccesstoMemory
 * @subpackage settings
 */
class SettingsXeDamForm extends sfForm
{
  public function configure()
  {
    $i18n = sfContext::getInstance()->i18n;

    // Build widgets
    $this->setWidgets(array(
      'xe_dam_api_url' => new sfWidgetFormInput,
      'xe_dam_user' => new sfWidgetFormInput,
      'xe_dam_private_key' => new sfWidgetFormInput
    ));

    // Add labels
    $this->widgetSchema->setLabels(array(
      'xe_dam_api_url' => $i18n->__('XeDAM API URL'),
      'xe_dam_user' => $i18n->__('XeDAM User'),
      'xe_dam_private_key' => $i18n->__('XeDAM Private key')
    ));

    // Add helper text
    $this->widgetSchema->setHelps(array(
      'xe_dam_api_url' => '',
      'xe_dam_user' => '',
      'xe_dam_private_key' => ''
    ));

    $this->validatorSchema['xe_dam_api_url'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['xe_dam_user'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['xe_dam_private_key'] = new sfValidatorString(array('required' => false));

    // Set decorator
    $decorator = new QubitWidgetFormSchemaFormatterList($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('list', $decorator);
    $this->widgetSchema->setFormFormatterName('list');

    // Set wrapper text for Finding Aid settings
    $this->widgetSchema->setNameFormat('xe_dam[%s]');
  }
}
