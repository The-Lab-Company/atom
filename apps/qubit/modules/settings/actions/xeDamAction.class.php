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
 * XeDAM settings
 *
 * @package    AccesstoMemory
 * @subpackage settings
 */

class SettingsXeDamAction extends sfAction
{
  public function execute($request)
  {
    $this->xeDamForm = new SettingsXeDamForm;

    // Handle POST data (form submit)
    if ($request->isMethod('post'))
    {
      QubitCache::getInstance()->removePattern('settings:i18n:*');

      // Handle Finding Aid form submission
      if (null !== $request->xe_dam)
      {
        $this->xeDamForm->bind($request->xe_dam);
        if ($this->xeDamForm->isValid())
        {
          // Do update and redirect to avoid repeat submit wackiness
          $this->updateXeDamSettings();

          $notice = sfContext::getInstance()->i18n->__('XeDAM settings saved.');
          $this->getUser()->setFlash('notice', $notice);

          $this->redirect('settings/xeDam');
        }
      }
    }

    $this->populateXeDamForm();
  }

  /**
   * Populate the Finding Aid form
   */
  protected function populateXeDamForm()
  {
    $xeDamApiUrl = QubitSetting::getByName('xe_dam_api_url');
    $xeDamUser = QubitSetting::getByName('xe_dam_user');
    $xeDamPrivateKey = QubitSetting::getByName('xe_dam_private_key');

    $this->xeDamForm->setDefaults(array(
      'xe_dam_api_url' => (isset($xeDamApiUrl)) ? $xeDamApiUrl->getValue(array('sourceCulture'=>true)) : null,
      'xe_dam_user' => (isset($xeDamUser)) ? $xeDamUser->getValue(array('sourceCulture'=>true)) : null,
      'xe_dam_private_key' => (isset($xeDamPrivateKey)) ? $xeDamPrivateKey->getValue(array('sourceCulture'=>true)) : null
    ));
  }

  /**
   * Update the Finding Aid settings
   */
  protected function updateXeDamSettings()
  {
    $thisForm = $this->xeDamForm;

    if (null !== $xeDamApiUrl = $thisForm->getValue('xe_dam_api_url'))
    {
      $setting = QubitSetting::getByName('xe_dam_api_url');

      if (null === $setting = QubitSetting::getByName('xe_dam_api_url'))
      {
        $setting = QubitSetting::createNewSetting('xe_dam_api_url', null, array('deleteable'=>false));
      }

      $setting->setValue($xeDamApiUrl, array('sourceCulture' => true));
      $setting->save();
    }

    if (null !== $xeDamUser = $thisForm->getValue('xe_dam_user'))
    {
      $setting = QubitSetting::getByName('xe_dam_user');

      if (null === $setting = QubitSetting::getByName('xe_dam_user'))
      {
        $setting = QubitSetting::createNewSetting('xe_dam_user', null, array('deleteable'=>false));
      }

      $setting->setValue($xeDamUser, array('sourceCulture' => true));
      $setting->save();
    }

    if (null !== $xeDamPrivateKey = $thisForm->getValue('xe_dam_private_key'))
    {
      $setting = QubitSetting::getByName('xe_dam_private_key');

      if (null === $setting = QubitSetting::getByName('xe_dam_private_key'))
      {
        $setting = QubitSetting::createNewSetting('xe_dam_private_key', null, array('deleteable'=>false));
      }

      $setting->setValue($xeDamPrivateKey, array('sourceCulture' => true));
      $setting->save();
    }

    return $this;
  }
}

