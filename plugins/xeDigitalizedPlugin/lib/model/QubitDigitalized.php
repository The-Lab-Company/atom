<?php


/**
 * Skeleton subclass for representing a row from the 'digitalized' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    plugins.xeDigitalizedPlugin.lib.model
 */
class QubitDigitalized extends BaseDigitalized
{
  public static function checkRemoteDigitalized($informationObject)
  {
    $xeDamService = new XeDamService();
    $coverImages = $xeDamService->getCoverImages($informationObject->getInstitutionResponsibleIdentifier(array('cultureFallback' => true)));
    if ($coverImages != false )
    {
      $conn = Propel::getConnection();
      $statement = $conn->prepare('INSERT INTO digitalized (id) VALUES (?)');
      $statement->execute(array($informationObject->id));
      QubitSearch::getInstance()->update($informationObject);
      return true;
    }
    else
    {
      return false;
    }
  }

  public static function getThumbnail($institutionIdentifier)
  {
    $xeDamService = new XeDamService();
    $coverImage = $xeDamService->getCoverImage($institutionIdentifier);
    if ($coverImage != false)
    {
      return $coverImage;
    }
    else
    {
      return false;
    }
  }

  public static function removeDigitalizedIO($informationObjectId)
  {
    $conn = Propel::getConnection();
    $statement = $conn->prepare('DELETE FROM digitalized WHERE id = (?)');

    return $statement->execute(array($informationObjectId));
  }
} // QubitDigitalized
