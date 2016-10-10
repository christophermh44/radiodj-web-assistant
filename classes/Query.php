<?php
class Query {
  private $base;
  private $host;
  private $port;
  private $user;
  private $pass;
  private $cset;

  private static $instance = null;

  public static function static_init() {
  }

  public function __construct($base, $host, $port, $user, $pass, $cset) {
    $this->base = $base;
    $this->host = $host;
    $this->port = $port;
    $this->user = $user;
    $this->pass = $pass;
    $this->cset = $cset;
  }

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new Query(
        Conf::get('db_base'),
        Conf::get('db_host'),
        Conf::get('db_port'),
        Conf::get('db_user'),
        Conf::get('db_pass'),
        Conf::get('db_cset')
      );
    }
    return self::$instance;
  }

  public function checkConnection() {
    $pdoInstance = $this->open();
    return ($pdoInstance != null);
  }

  protected function open() {
    try {
      return new \PDO('mysql:dbname='.$this->base.';host='.$this->host.';port='.$this->port, $this->user, $this->pass, [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \''.$this->cset.'\'']);
    } catch (\PDOException $e) {
      return null;
    }
  }

  protected function close() {
    unset($instance);
  }

  public function process($query, $vars = []) {
  	$instance = $this->open();
    if ($instance == null) {
      return null;
    }
    $statement = $instance->prepare($query);
    $statement->setFetchMode(\PDO::FETCH_OBJ);
    foreach ($vars as $key => $value) {
      $statement->bindValue(':'.$key, $value);
    }
    $data = null;
    if ($statement->execute()) {
      $data = $statement->fetchAll();
    }
    $this->close($instance);
    return $data;
  }
}
