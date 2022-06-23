<?php

class XeDamService
{
  protected $xeDamApiUrl;
  protected $xeDamUser;
  protected $xeDamPrivateKey;

  function __construct()
  {
    $this->xeDamApiUrl = QubitSetting::getByName('xe_dam_api_url');
    $this->xeDamUser = QubitSetting::getByName('xe_dam_user');
    $this->xeDamPrivateKey = QubitSetting::getByName('xe_dam_private_key');
  }

  public function getCoverImage($identifier)
  {
    $coverImage = false;

    $query = "user=" . $this->xeDamUser . "&function=get_preview_cover&theme=$identifier";

    $sign = hash("sha256", $this->xeDamPrivateKey . $query);

    $results = file_get_contents($this->xeDamApiUrl . $query . "&sign=" . $sign);

    $data = json_decode($results, TRUE);

    if (isset($data[0]['url_cover']))
    {
      $coverImage = $data[0]['url_cover'];
    }

    return $coverImage;
  }

  public function getCoverImages($identifier)
  {
    $coverImages = false;

    $query = "user=" . $this->xeDamUser . "&function=get_preview_digital_objects&theme=$identifier";

    $sign = hash("sha256", $this->xeDamPrivateKey . $query);

    $results = file_get_contents($this->xeDamApiUrl . $query . "&sign=" . $sign);

    $data = json_decode($results, TRUE);

    if (0 < count($data))
    {
      $coverImages = $data;
    }

    return $coverImages;
  }

  public function getPdfLink($identifier)
  {
    $pdfLink = false;

    $query = "user=" . $this->xeDamUser . "&function=get_resource_pdf&theme=$identifier";

    $sign = hash("sha256", $this->xeDamPrivateKey . $query);

    $results = file_get_contents($this->xeDamApiUrl . $query . "&sign=" . $sign);

    $data = json_decode($results, TRUE);

    if (0 < count($data))
    {
      $pdfLink = $data;
    }

    return $pdfLink;
  }
}
