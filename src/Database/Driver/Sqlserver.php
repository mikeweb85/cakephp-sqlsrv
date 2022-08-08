<?php

namespace MikeWeb\CakeSqlsrv\Database\Driver;

use MikeWeb\CakeOdbc\Database\Driver\OdbcDriverTrait;
use MikeWeb\CakeOdbc\Database\Driver\Sqlserver as SqlserverDriver;
use Cake\Database\Driver\Sqlserver as BaseSqlserverDriver;
use InvalidArgumentException;
use PDO;

class Sqlserver extends SqlserverDriver {

    /** @inheritDoc */
    public function connect(): bool {
        if ($this->_connection) {
            return true;
        }

        $config = $this->_config;

        if (isset($config['persistent']) && $config['persistent']) {
            throw new InvalidArgumentException(
                'Config setting "persistent" cannot be set to true, '
                . 'as the Sqlserver PDO driver does not support PDO::ATTR_PERSISTENT'
            );
        }

        $config['flags'] += [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        if (!empty($config['encoding'])) {
            $config['flags'][PDO::SQLSRV_ATTR_ENCODING] = $config['encoding'];
        }

        $dsn = 'sqlsrv:' . $this->generateDsn($config);

        $connected = $this->_connect($dsn, $config);

        if ($connected) {
            $this->postConnectionExecution($config);
        }

        return true;
    }

    /** @inheritDoc */
    public function version(): string {
        return BaseSqlserverDriver::version();
    }

    /** @inheritDoc */
    public function enabled(): bool {
        return BaseSqlserverDriver::enabled();
    }
}
