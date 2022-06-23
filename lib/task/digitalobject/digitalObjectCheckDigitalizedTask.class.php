<?php

/**
 * Regenerate nested set column values using repository_id as scope term
 *
 * @package    symfony
 * @subpackage task
 * @author     Edgar Rodriguez <edgar.rodriguez@xercode.es>
 */
class digitalObjectCheckDigitalizedTask extends sfBaseTask
{

  protected $conn;
  
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('update', null, sfCommandOption::PARAMETER_OPTIONAL, 'Check in digitalized table objects allready are digitalized', true),

    ));

    $this->namespace = 'digitalobject';
    $this->name = 'check-digitalized-objects';
    $this->briefDescription = 'Check digitalized objects stored in resourcespace';

    $this->detailedDescription = <<<EOF
Check digitalized objects stored in filenet and update the table digitalized.
EOF;
  }

  /**
   * @see sfTask
   */
   public function execute($arguments = array(), $options = array())
   {
    sfContext::createInstance($this->configuration);
    
    $timer = new QubitTimer();
    
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->conn = $databaseManager->getDatabase('propel')->getConnection();

    $no = $yes = 0;


    if ($options['update'])
    {
    // Comprueba los documentos que constaban como digitalizados

        $sql = "SELECT id FROM information_object ORDER BY id";


        foreach ($this->conn->query($sql, PDO::FETCH_ASSOC) as $item)
        {
          $io = QubitInformationObject::getById($item['id']);

          $io->digitalized();

          $this->logSection('id: ', $io->id);
        }

        $this->logSection('check-digitalized-objects', 'Done!');
        $this->logSection('heck-digitalized-objects', 'Elapsed time: '.$timer->elapsed() . ' s.');
    }
  }
}