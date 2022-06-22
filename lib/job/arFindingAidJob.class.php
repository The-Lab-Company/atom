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
 * Finding aid job manager.
 *
 * @author     Mike G <mikeg@artefactual.com>
 */
class arFindingAidJob extends arBaseJob
{
    /**
     * @see arBaseJob::$requiredParameters
     */
    protected $extraRequiredParameters = ['objectId'];

    private $resource;
    private $appRoot;

    public function runJob($parameters)
    {
        $this->resource = QubitInformationObject::getById($parameters['objectId']);

        // Check that object exists and that it is not the root
        if (!isset($this->resource) || !isset($this->resource->parent)) {
            $this->error($this->i18n->__('Error: Could not find an information object with id: %1', ['%1' => $parameters['objectId']]));

            return false;
        }

        if (isset($parameters['delete']) && $parameters['delete']) {
            $findingAid = new QubitFindingAid($this->resource);
            $findingAid->setLogger($this->logger);
            $result = $findingAid->delete();
        } elseif (isset($parameters['uploadPath'])) {
            $findingAid = new QubitFindingAid($this->resource);
            $findingAid->setLogger($this->logger);
            $result = $findingAid->upload($parameters['uploadPath']);
        } else {
            $generator = new QubitFindingAidGenerator($this->resource);
            $generator->setLogger($this->logger);
            $generator->setAuthLevel(
                QubitFindingAidGenerator::getPublicSetting()
            );
            $generator->setFormat(QubitFindingAidGenerator::getFormatSetting());
            $generator->setModel(QubitFindingAidGenerator::getModelSetting());
            $result = $generator->generate();
        }

        if (!$result) {
            return false;
        }

        $this->job->setStatusCompleted();
        $this->job->save();

        return true;
    }

    public static function getStatus($id)
    {
        $sql = 'SELECT j.status_id as statusId
            FROM job j JOIN object o ON j.id = o.id
            WHERE j.name = ? AND j.object_id = ?
            ORDER BY o.created_at DESC';

        $ret = QubitPdo::fetchOne($sql, [get_class(), $id]);

        return $ret ? (int) $ret->statusId : null;
    }
}
