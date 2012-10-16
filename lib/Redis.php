<?php
/**
 * @file lib/Redis.php
 */

/**
 * Common code and client singleton, for all Redis clients.
 *
 * Copied from Drupal redis module.
 */
class VcRedis {
  /**
   * Redis default host.
   */
  const REDIS_DEFAULT_HOST = "127.0.0.1";

  /**
   * Redis default port.
   */
  const REDIS_DEFAULT_PORT = 6379;

  /**
   * Redis default database: will select none (Database 0).
   */
  const REDIS_DEFAULT_BASE = NULL;

  /**
   * Redis default password: will not authenticate.
   */
  const REDIS_DEFAULT_PASSWORD = NULL;

  /**
   * Cache implementation namespace.
   */
  const REDIS_IMPL_CACHE = 'Redis_Cache_';

  /**
   * Lock implementation namespace.
   */
  const REDIS_IMPL_LOCK = 'Redis_Lock_Backend_';

  /**
   * Session implementation namespace.
   */
  const REDIS_IMPL_SESSION = 'Redis_Session_Backend_';

  /**
   * Session implementation namespace.
   */
  const REDIS_IMPL_CLIENT = 'Redis_Client_';

  /**
   * @var Redis_Client_Interface
   */
  protected static $_clientInterface;

  /**
   * @var mixed
   */
  protected static $_client;

  public static function hasClient() {
    return isset(self::$_client);
  }

  /**
   * Get underlaying library name.
   *
   * @return string
   */
  public static function getClientName() {
    return 'PhpRedis';
  }

  /**
   * Get client singleton.
   *
   * Always prefer socket connection.
   *
   * @return Redis
   */
  public static function getClient() {
    if (!isset(self::$_client)) {
      global $conf;

      $host = isset($conf['redis_client_host']) ? $conf['redis_client_host'] : self::REDIS_DEFAULT_HOST;
      $port = isset($conf['redis_client_port']) ? $conf['redis_client_port'] : self::REDIS_DEFAULT_PORT;
      $base = isset($conf['redis_client_base']) ? $conf['redis_client_base'] : self::REDIS_DEFAULT_BASE;
      $password = isset($conf['redis_client_password']) ? $conf['redis_client_password'] : self::REDIS_DEFAULT_PASSWORD;

      self::$_client = new Redis;
      self::$_client->connect($host, $port);

      if (!empty($password)) {
        self::$_client->auth($password);
      }

      if (!empty($base)) {
        self::$_client->select($base);
      }

      // Do not allow PhpRedis serialize itself data, we are going to do it
      // ourself. This will ensure less memory footprint on Redis size when
      // we will attempt to store small values.
      self::$_client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
    }

    return self::$_client;
  }

  /**
   * Get specific class implementing the current client usage for the specific
   * asked core subsystem.
   *
   * @param string $system
   *   One of the Redis_Client::IMPL_* constant.
   * @param string $clientName
   *   Client name, if fixed.
   *
   * @return string
   *   Class name, if found.
   *
   * @throws Exception
   *   If not found.
   */
  public static function getClass($system, $clientName = NULL) {
    $className = $system . (isset($clientName) ? $clientName : self::getClientName());

    if (!class_exists($className)) {
      throw new Exception($className . " does not exists");
    }

    return $className;
  }
}
