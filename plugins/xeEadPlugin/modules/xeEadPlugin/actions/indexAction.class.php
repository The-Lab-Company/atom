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
 * Render resource in EAD XML format
 *
 * @package    AccesstoMemory
 * @subpackage xeEadPlugin
 * @author     Peter Van Garderen <peter@artefactual.com>
 */

class xeEadPluginIndexAction extends InformationObjectIndexAction
{
  public function execute($request)
  {

    if (!$this->authenticateUser())
    {
      throw new QubitApiNotAuthorizedException;
    }

    sfConfig::set('sf_escaping_strategy', false);

   
    parent::execute($request);


    $this->ead = new xeEadPlugin($this->resource);

    $this->context->getUser()->setCulture('gl');

    // Determine language(s) used in the export
    $this->exportLanguage = sfContext::getInstance()->user->getCulture();
    $this->sourceLanguage = $this->resource->getSourceCulture();
    // Instantiate Object to use in Converting ISO 639-1 language codes to 639-2
    $this->iso639convertor = new fbISO639_Map;

    // Set array with valid EAD level values (see ead.dtd line 2220)
    $this->eadLevels = array('class', 'collection', 'file', 'fonds', 'item', 'otherlevel', 'recordgrp', 'series', 'subfonds', 'subgrp', 'subseries');
    $this->options = array('current-level-only' => false);
  }

  private function authenticateUser()
  {
    // Cookie-based authentication (already signed)
    if ($this->context->user->isAuthenticated())
    {
      return true;
    }

    // Basic authentication
    if (isset($_SERVER['PHP_AUTH_USER']))
    {
      if ($this->context->user->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
      {
        return true;
      }
    }

    // X_REST_API_KEY is and old name still checked for backward compatibility. Last attempt!
    if (null !== $key = Qubit::getHttpHeader(array('REST-API-Key', 'HTTP_X_REST_API_KEY')))
    {
      $criteria = new Criteria;
      $criteria->add(QubitProperty::NAME, 'restApiKey');
      $criteria->add(QubitPropertyI18n::VALUE, $key);
      if (null === $restApiKeyProperty = QubitProperty::getOne($criteria))
      {
        return false;
      }

      if (null === $user = QubitUser::getById($restApiKeyProperty->objectId))
      {
        return false;
      }

      $this->context->user->signIn($user);

      return true;
    }

    return false;
  }      
}
