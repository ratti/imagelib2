<?php
/**
 * Class MysqlHelper
 *
 */

namespace App\Helper;

use App\Manager\ThingsManager;

class MysqlHelper
{
    /* @var \mysqli $mysqli */
    public $mysqli;

    /* @var ThingsManager $thingsManager */
    public $thingsManager;

    public function init(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
        $config = $this->thingsManager->getConfigHelper();
        $this->connect(
            $config->mysqlHost,
            $config->mysqlUser,
            $config->mysqlPassword,
            $config->mysqlDatabase
        );

        # allow 0 as id.
        $this->query("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
    }

    public function connect($host, $user, $password, $database)
    {
        if (!$this->mysqli) {
            $this->mysqli = new \mysqli($host, $user, $password, $database);
            if ($this->mysqli->connect_errno) {
                die("Failed to connect to MySQL: " . $this->mysqli->connect_error);
            }
        }
    }

    public function getAllAssocArray($sql): array
    {
        $ret = [];
        $result = $this->mysqli->query($sql);
        if ($result === false) return [];
        while ($row = $result->fetch_assoc()) {
            $ret[$row['id']] = $row;
        }
        $result->free_result();
        return $ret;
    }

    public function getOneAssocArray($sql): array
    {
        $result = $this->query($sql);
        if ($result === false) return [];
        $ret = $result->fetch_array(MYSQLI_ASSOC);
        $result->free_result();
        if (is_null($ret)) $ret = [];
        return $ret;
    }

    public function query($sql)
    {
        return $this->mysqli->query($sql);
    }

    public function quote($val)
    {
        return $this->mysqli->real_escape_string($val);
    }
}
