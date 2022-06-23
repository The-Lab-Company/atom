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

class DigitalObjectImageflowComponent extends sfComponent
{
  public function execute($request)
  {

    $response = sfContext::getInstance()->getResponse();

    $response->addJavascript('/plugins/xeCarouselPlugin/js/jquery.jcarousel.min.js');
    $response->addJavascript('/plugins/xeCarouselPlugin/js/jcarousel.connected-carousels.js');
    $response->addJavascript('/plugins/xeCarouselPlugin/js/lightgallery.js');
    $response->addJavascript('/plugins/xeCarouselPlugin/js/lg-thumbnail.js');
    $response->addJavascript('/plugins/xeCarouselPlugin/js/lg-fullscreen.js');
    $response->addJavascript('/plugins/xeCarouselPlugin/js/lg-zoom.js');

    $response->addStylesheet('/plugins/xeCarouselPlugin/css/jcarousel.connected-carousels.css');
    $response->addStylesheet('/plugins/xeCarouselPlugin/css/lightgallery.css');

    $institutionIdentifier = $this->resource->getInstitutionResponsibleIdentifier(array('cultureFallback' => true));

    if (!(($this->resource->levelOfDescriptionId == 235) ||
          ($this->resource->levelOfDescriptionId == 236) ||
          ($this->resource->levelOfDescriptionId == 237) ||
          ($this->resource->levelOfDescriptionId == 230)))
    {
      return;
    }

    $xeDamService = new XeDamService();

    $this->objects = array();

    $data = $xeDamService->getCoverImages($institutionIdentifier);

    if($data == false)
    {
      return;
    }
    else
    {
      foreach($data as $item)
      {
        if(isset($item['url_min']) && isset($item['url_jpg']))
        {
          $this->objects[] = array( 'ruta' => $institutionIdentifier,
                                    'thumbnail' => $item['url_min'],
                                    'reference' => $item['url_jpg']);
        }
      }
    }

    $this->total = count($this->objects);

    $this->pdf = $xeDamService->getPdfLink($institutionIdentifier);

    if ($this->pdf == false)
    {
      return $this->pdf = null;
    }
  }
}
