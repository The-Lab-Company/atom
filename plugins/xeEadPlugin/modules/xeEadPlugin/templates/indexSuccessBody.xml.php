<ead:ead xmlns:ead="http://ead3.archivists.org/schema/">

    <ead:control script="Latn" repositoryencoding="iso15511" langencoding="iso639-2b" dateencoding="iso8601" countryencoding="iso3166-1" audience="external">
        <?php echo $ead->renderEadId() ?>
        <?php $identifier = explode('/', $resource->getIdentifier())  ?>

        <ead:filedesc>
            <ead:titlestmt>
                <?php if (0 < strlen($value = $resource->getTitle(array('cultureFallback' => true)))) : ?>
                    <ead:titleproper><?php echo escape_dc(esc_specialchars($value)) ?></ead:titleproper>
                <?php endif; ?>
            </ead:titlestmt>
        </ead:filedesc>

        <ead:maintenancestatus value="new" />
	<ead:maintenanceagency>
            <?php $agencycode = substr($resource->getRepository(array('inherit' => true))->getIdentifier(), 0) ?>
	        <?php $agencycode = rtrim($agencycode) ?>
                <?php $agencycode = rtrim($agencycode, ".-") ?>
                <?php $agencycode = explode(".", $agencycode) ?>
                <?php $agencycode = $agencycode[0] . "." . $agencycode[1] . "." . $agencycode[2] . "." . $agencycode[3]?>
		<ead:agencycode><?php echo $agencycode ?></ead:agencycode>
            <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                <ead:agencyname><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:agencyname>
            <?php endif; ?>
        </ead:maintenanceagency>


        <ead:languagedeclaration>
            <ead:language langcode="<?php echo strtolower($iso639convertor->getID2($exportLanguage)) ?>">Galician</ead:language>
            <ead:script scriptcode="Latn">Alfabeto latino</ead:script>
        </ead:languagedeclaration>


        <ead:maintenancehistory>
            <ead:maintenanceevent>
                <ead:eventtype value="created" />
                <?php $dt = QubitOai::getDate(); ?>
                <?php $dt = preg_replace('/\-+/', '', $dt);
                $dt = preg_replace('/\T+/', '', $dt);
                $dt = preg_replace('/\:+/', '', $dt);
                $dt = preg_replace('/Z+/', '.', $dt);
                $dt = substr($dt, 0, -1);
                $dt = $dt . ".0" ?>
                <ead:eventdatetime><?php echo $dt ?></ead:eventdatetime>
                <ead:agenttype value="machine" />
                <ead:agent><?php echo "Xercode Media Software S.L. - Xearchive" ?></ead:agent>
            </ead:maintenanceevent>
        </ead:maintenancehistory>

    </ead:control>

    <ead:archdesc level="fonds">
        <?php

        $resourceVar = 'resource';
        $counter = 0;
        $counterVar = 'counter';

        $creators = $$resourceVar->getCreators();
        $events = $$resourceVar->getActorEvents(array('eventTypeId' => QubitTerm::CREATION_ID));

        ?>

        <?php $repository = null; ?>
        <?php if (0 < strlen($$resourceVar->getIdentifier())) : ?>
            <?php foreach ($$resourceVar->ancestors->andSelf()->orderBy('rgt') as $item) : ?>
                <?php if (isset($item->repository)) : ?>
                    <?php $repository = $item->repository; ?>
                    <?php break; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php foreach ($$resourceVar->ancestors->andSelf()->orderBy('lft') as $item) : ?>

            <?php if (isset($item->repository)) : ?>
                <?php $repository = $item->repository; ?>
                <?php if ($item->levelOfDescription == "Fondos") : ?>
                    <ead:did>

                        <ead:unitid label="Código de referencia" <?php if (isset($repository)) : ?><?php if ($countrycode = $repository->getCountryCode()) : ?><?php echo 'countrycode="' . $countrycode . '" ' ?><?php endif; ?><?php if ($repocode = $repository->getIdentifier()) : ?><?php echo 'repositorycode="' . escape_dc(esc_specialchars(substr($repocode,0,-1))) . '" ' ?><?php endif; ?><?php endif; ?>><?php echo substr($item['referenceCode'],0,-2) ?></ead:unitid>
                        <?php if (0 < strlen($value = $item->getTitle(array('cultureFallback' => true)))) : ?>
                            <ead:unittitle><?php echo esc_specialchars($value) ?></ead:unittitle>
                        <?php endif; ?>

                        <?php foreach ($item->getDates() as $date) : ?>
                            <ead:unitdatestructured datechar="Creación">
                                <ead:daterange>
                                    <?php if ($startdate = $date->getStartDate()) : ?><ead:fromdate><?php echo Qubit::renderDate($startdate)  ?></ead:fromdate><?php endif; ?>
                                    <?php if (0 < strlen($enddate = $date->getEndDate())) : ?><ead:todate><?php echo Qubit::renderDate($enddate) ?></ead:todate><?php endif; ?>
                                </ead:daterange>
                            </ead:unitdatestructured>
                        <?php endforeach; ?>

                        <ead:physdescstructured physdescstructuredtype="materialtype" coverage="whole">
                            <ead:quantity><?php echo $identifier[0]  ?></ead:quantity><?php if (0 < strlen($value = $item->getExtentAndMedium(array('cultureFallback' => true)))) : ?><ead:unittype <?php if (0 < strlen($encoding = $ead->getMetadataParameter('extent'))) : ?>encodinganalog="<?php echo $encoding ?>" <?php endif; ?>><?php echo escape_dc(esc_specialchars(trim($value))) ?></ead:unittype>
                            <?php endif; ?>
                        </ead:physdescstructured>

                        <?php $objects = $item->getPhysicalObjects() ?>
                        <?php foreach ($objects as $object) : ?>
                            <?php if (0 < strlen($location = $object->getLocation(array('cultureFallback' => true)))) : ?>
                                <ead:physloc id="<?php echo 'physloc' . str_pad(++$$counterVar, 4, '0', STR_PAD_LEFT) ?>"><?php echo escape_dc(esc_specialchars(trim($location))) ?></ead:physloc>
                            <?php endif; ?>
                            <ead:physloc <?php echo $ead->getEadContainerAttributes($object) ?><?php if (0 < strlen($location)) : ?> parent="<?php echo 'physloc' . str_pad($$counterVar, 4, '0', STR_PAD_LEFT) ?>" <?php endif; ?>>
                                <?php if (0 < strlen($name = $object->getName(array('cultureFallback' => true)))) : ?><?php echo escape_dc(esc_specialchars(trim($name))) ?>
                            <?php endif; ?>
                            </ead:physloc>
                        <?php endforeach; ?>

                        <ead:repository>
                            <ead:corpname>
                                <ead:part><?php echo $item->getRepository(array('inherit' => true))->getAuthorizedFormOfName() ?></ead:part>
                            </ead:corpname>
                        </ead:repository>

                    </ead:did>
                <?php endif; ?>

                <?php if ($item->levelOfDescription == "Fondos") : ?>

                    <ead:altformavail localtype="Localización de copias">
                        <ead:p>No existen copias</ead:p>
                    </ead:altformavail>

                    <ead:processinfo>
                        <ead:chronlist>
                            <ead:chronitem>
                                <?php $dt = $item->createdAt ?>
                                <?php $dt = preg_replace('/\s+/', '', $dt);
                                $dt = preg_replace('/\-+/', '', $dt);
                                $dt = preg_replace('/\T+/', '', $dt);
                                $dt = preg_replace('/\:+/', '', $dt);
                                $dt = preg_replace('/Z+/', '.', $dt);
                                $dt = substr($dt, 0, -1);
                                $dt = $dt . ".0" ?>
                                <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                <ead:event>creación</ead:event>
                            </ead:chronitem>
                            <ead:chronitem>
                                <?php $dt = $item->updatedAt ?>
                                <?php $dt = preg_replace('/\s+/', '', $dt);
                                $dt = preg_replace('/\-+/', '', $dt);
                                $dt = preg_replace('/\T+/', '', $dt);
                                $dt = preg_replace('/\:+/', '', $dt);
                                $dt = preg_replace('/Z+/', '.', $dt);
                                $dt = substr($dt, 0, -1);
                                $dt = $dt . ".0" ?>

                                <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                <ead:event>modificación</ead:event>
                            </ead:chronitem>
                        </ead:chronlist>
                    </ead:processinfo>
                    <ead:processinfo>
                        <ead:p>
                            <ead:name localtype="Creador de la descripción">
                                <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                    <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                <?php endif; ?>
                            </ead:name>
                        </ead:p>
                        <ead:p>
                            <ead:name localtype="Modificador de la descripción">
                                <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                    <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                <?php endif; ?>
                            </ead:name>
                        </ead:p>
                    </ead:processinfo>

                <?php endif; ?>

            <?php endif; ?>



        <?php endforeach; ?>


        <?php $levels = count($$resourceVar->ancestors->andSelf()->orderBy('lft')) ?>

        <?php $repository = null; ?>
        <?php $i = 0 ?>
        <ead:dsc>
            <?php if (0 < strlen($$resourceVar->getIdentifier())) : ?>
                <?php foreach ($$resourceVar->ancestors->andSelf()->orderBy('lft') as $item) : ?>

                   <?php if ($item->levelOfDescription == "Fondos") : ?>
			<?php continue ?>
                   <?php endif; ?>


                    <?php if ($item->levelOfDescription == "Sección") : ?>
                        <?php $level = "recordgrp" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "1ª División de Sección" || $item->levelOfDescription == "2ª División de Sección" || $item->levelOfDescription == "3ª División de Sección" || $item->levelOfDescription == "4ª División de Sección" || $item->levelOfDescription == "Subsección") : ?>
                        <?php $level = "subgrp" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "Colección") : ?>
                        <?php $level = "collection" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "Series") : ?>
                        <?php $level = "series" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "Fracción de serie" || $item->levelOfDescription == "Sub-series") : ?>
                        <?php $level = "subseries" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "Unidade documental composta") : ?>
                        <?php $level = "file" ?>
                    <?php endif; ?>
                    <?php if ($item->levelOfDescription == "Unidade documental simple") : ?>
                        <?php $level = "item" ?>
                    <?php endif; ?>

                    <?php if ($item->levelOfDescription <> "Fondos" && $item->levelOfDescription <> "") : ?>
                        <?php $i += 1 ?>
                        <?php echo "<ead:c0$i  level='$level'>" ?>

                        <?php if ($resource->levelOfDescription <> $item->levelOfDescription) : ?>
                            <ead:did>

                                <ead:unitid label="Código de referencia" <?php if (isset($repository)) : ?><?php if ($countrycode = $repository->getCountryCode()) : ?><?php echo 'countrycode="' . $countrycode . '" ' ?><?php endif; ?><?php if ($repocode = $repository->getIdentifier()) : ?><?php echo 'repositorycode="' . escape_dc(esc_specialchars($repocode)) . '" ' ?><?php endif; ?><?php endif; ?>><?php echo substr($item['referenceCode'],0,-1) ?></ead:unitid>

                                <?php if (0 < strlen($value = $item->getTitle(array('cultureFallback' => true)))) : ?>
                                    <ead:unittitle><?php echo esc_specialchars($value) ?></ead:unittitle>
                                <?php endif; ?>


                                <?php foreach ($item->getDates() as $date) : ?>
                                    <ead:unitdatestructured datechar="Creación">
                                        <ead:daterange>
                                            <?php if ($startdate = $date->getStartDate()) : ?><ead:fromdate><?php echo Qubit::renderDate($startdate)  ?></ead:fromdate><?php endif; ?>
                                            <?php if (0 < strlen($enddate = $date->getEndDate())) : ?><ead:todate><?php echo Qubit::renderDate($enddate) ?></ead:todate><?php endif; ?>
                                        </ead:daterange>
                                    </ead:unitdatestructured>
                                <?php endforeach; ?>


                                <ead:physdescstructured physdescstructuredtype="materialtype" coverage="whole">
                                    <ead:quantity><?php echo $identifier[0]  ?></ead:quantity>
                                    <ead:unittype>Unidade(s) de instalación</ead:unittype>
                                </ead:physdescstructured>

                                <?php $objects = $item->getPhysicalObjects() ?>
                                <?php foreach ($objects as $object) : ?>
                                    <?php if (0 < strlen($location = $object->getLocation(array('cultureFallback' => true)))) : ?>
                                        <ead:physloc id="<?php echo 'physloc' . str_pad(++$$counterVar, 4, '0', STR_PAD_LEFT) ?>"><?php echo escape_dc(esc_specialchars($location)) ?></ead:physloc>
                                    <?php endif; ?>

                                <?php endforeach; ?>

                                <ead:repository>
                                    <ead:corpname>
                                        <ead:part><?php echo $item->getRepository(array('inherit' => true))->getAuthorizedFormOfName() ?></ead:part>
                                    </ead:corpname>
                                </ead:repository>

                            </ead:did>

                            <ead:processinfo>
                                <ead:chronlist>
                                    <ead:chronitem>
                                        <?php $dt = $item->createdAt ?>
                                        <?php $dt = preg_replace('/\s+/', '', $dt);
                                        $dt = preg_replace('/\-+/', '', $dt);
                                        $dt = preg_replace('/\T+/', '', $dt);
                                        $dt = preg_replace('/\:+/', '', $dt);
                                        $dt = preg_replace('/Z+/', '.', $dt);
                                        $dt = substr($dt, 0, -1);
                                        $dt = $dt . ".0" ?>
                                        <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                        <ead:event>creación</ead:event>
                                    </ead:chronitem>
                                    <ead:chronitem>
                                        <?php $dt = $item->updatedAt ?>
                                        <?php $dt = preg_replace('/\s+/', '', $dt);
                                        $dt = preg_replace('/\-+/', '', $dt);
                                        $dt = preg_replace('/\T+/', '', $dt);
                                        $dt = preg_replace('/\:+/', '', $dt);
                                        $dt = preg_replace('/Z+/', '.', $dt);
                                        $dt = substr($dt, 0, -1);
                                        $dt = $dt . ".0" ?>
                                        <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                        <ead:event>modificación</ead:event>
                                    </ead:chronitem>
                                </ead:chronlist>
                            </ead:processinfo>
                            <ead:processinfo>
                                <ead:p>
                                    <ead:name localtype="Creador de la descripción">
                                        <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                            <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                        <?php endif; ?>
                                    </ead:name>
                                </ead:p>
                                <ead:p>
                                    <ead:name localtype="Modificador de la descripción">
                                        <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                            <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                        <?php endif; ?>
                                    </ead:name>
                                </ead:p>
                            </ead:processinfo>


                        <?php endif; ?>


                        <?php if ($resource->levelOfDescription == $item->levelOfDescription) : ?>


                            <ead:did>

                                <ead:unitid label="Código de referencia" <?php if (isset($repository)) : ?><?php if ($countrycode = $repository->getCountryCode()) : ?><?php echo 'countrycode="' . $countrycode . '" ' ?><?php endif; ?><?php if ($repocode = $repository->getIdentifier()) : ?><?php echo 'repositorycode="' . escape_dc(esc_specialchars($repocode)) . '" ' ?><?php endif; ?><?php endif; ?>><?php echo $item['referenceCode'] ?></ead:unitid>

                                <ead:unitid label="Código de referencia" <?php if (isset($repository)) : ?><?php if ($countrycode = $repository->getCountryCode()) : ?><?php echo 'countrycode="' . $countrycode . '" ' ?><?php endif; ?><?php if ($repocode = $repository->getIdentifier()) : ?><?php echo 'repositorycode="' . escape_dc(esc_specialchars($repocode)) . '" ' ?><?php endif; ?><?php endif; ?>><?php echo esc_specialchars($resource->institutionResponsibleIdentifier) ?></ead:unitid>

                                <?php if (0 < strlen($value = $resource->getTitle(array('cultureFallback' => true)))) : ?>
                                    <ead:unittitle><?php echo esc_specialchars($value) ?></ead:unittitle>
                                <?php endif; ?>

                                <ead:langmaterial>
                                    <ead:languageset>
                                        <ead:language langcode="<?php echo strtolower($iso639convertor->getID2($exportLanguage)) ?>">Galician</ead:language>
                                        <ead:script scriptcode="Latn">Alfabeto latino</ead:script>
                                    </ead:languageset>
                                </ead:langmaterial>


                               <?php foreach ($$resourceVar->getDates() as $date) : ?>
                                    <ead:unitdatestructured datechar="Creación">
                                        <ead:daterange>
                                            <?php if ($startdate = $date->getStartDate()) : ?><ead:fromdate><?php echo Qubit::renderDate($startdate)  ?></ead:fromdate><?php endif; ?>
                                            <?php if (0 < strlen($enddate = $date->getEndDate())) : ?><ead:todate><?php echo Qubit::renderDate($enddate) ?></ead:todate><?php endif; ?>
                                        </ead:daterange>
                                    </ead:unitdatestructured>
                                <?php endforeach; ?>


                                <ead:physdescstructured physdescstructuredtype="materialtype" coverage="whole">
                                    <ead:quantity><?php echo $identifier[1]  ?></ead:quantity><?php if (0 < strlen($value = $$resourceVar->getExtentAndMedium(array('cultureFallback' => true)))) : ?>
                                        <ead:unittype>Unidade(s) de instalación</ead:unittype>

                                    <?php endif; ?>



                                </ead:physdescstructured>

                                <?php $objects = $$resourceVar->getPhysicalObjects() ?>
                                <?php foreach ($objects as $object) : ?>
                                    <?php if (0 < strlen($location = $object->getLocation(array('cultureFallback' => true)))) : ?>
                                        <ead:physloc label="<?php echo 'physloc' . str_pad(++$$counterVar, 4, '0', STR_PAD_LEFT) ?>"><?php echo escape_dc(esc_specialchars($location)) ?></ead:physloc><?php endif; ?>
                                <?php endforeach; ?>

                                <ead:repository>
                                    <ead:corpname>
                                        <ead:part><?php echo $resource->getRepository(array('inherit' => true))->getAuthorizedFormOfName() ?></ead:part>
                                    </ead:corpname>
                                </ead:repository>
                            </ead:did>

                            <?php if (0 < strlen($value = $resource->getScopeAndContent(array('cultureFallback' => true)))) : ?>
                                <ead:scopecontent>
                                     <ead:p><?php echo (str_replace(PHP_EOL, '\br',  $value)) ?></ead:p>				
                                </ead:scopecontent><?php endif; ?>

                            <ead:processinfo>
                                <ead:chronlist>
                                    <ead:chronitem>
                                        <?php $dt = $item->createdAt ?>
                                        <?php $dt = preg_replace('/\s+/', '', $dt);
                                        $dt = preg_replace('/\-+/', '', $dt);
                                        $dt = preg_replace('/\T+/', '', $dt);
                                        $dt = preg_replace('/\:+/', '', $dt);
                                        $dt = preg_replace('/Z+/', '.', $dt);
                                        $dt = substr($dt, 0, -1);
                                        $dt = $dt . ".0" ?>
                                        <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                        <ead:event>creación</ead:event>
                                    </ead:chronitem>
                                    <ead:chronitem>
                                        <?php $dt = $item->updatedAt ?>
                                        <?php $dt = preg_replace('/\s+/', '', $dt);
                                        $dt = preg_replace('/\-+/', '', $dt);
                                        $dt = preg_replace('/\T+/', '', $dt);
                                        $dt = preg_replace('/\:+/', '', $dt);
                                        $dt = preg_replace('/Z+/', '.', $dt);
                                        $dt = substr($dt, 0, -1);
                                        $dt = $dt . ".0" ?>
                                        <ead:datesingle><?php echo $dt ?></ead:datesingle>
                                        <ead:event>modificación</ead:event>
                                    </ead:chronitem>
                                </ead:chronlist>
                            </ead:processinfo>
                            <ead:processinfo>
                                <ead:p>
                                    <ead:name localtype="Creador de la descripción">
                                        <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                            <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                        <?php endif; ?>
                                    </ead:name>
                                </ead:p>
                                <ead:p>
                                    <ead:name localtype="Modificador de la descripción">
                                        <?php if ($value = $resource->getRepository(array('inherit' => true))) : ?>
                                            <ead:part><?php echo escape_dc(esc_specialchars($value->__toString())) ?></ead:part>
                                        <?php endif; ?>
                                    </ead:name>
                                </ead:p>
                            </ead:processinfo>

                            <?php for ($j = $i; $j >= 1; $j--) : ?>
                                <?php echo "</ead:c0$j>" ?>
                            <?php endfor; ?>
                        <?php endif; ?>


                    <?php endif; ?>


                <?php endforeach; ?>
            <?php endif; ?>
        </ead:dsc>
    </ead:archdesc>

</ead:ead>
