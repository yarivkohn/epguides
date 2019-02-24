<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/21/19
 * Time: 3:22 PM
 */

namespace Epguides\Cron;

use Epguides\Models\EpisodesDb;

define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ . DS . '..' . DS.'..'.DS. 'bootstrap'.DS.'app.php';

class CronUpdate {

    public function execute()
    {
        $episode = new EpisodesDb();
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['user'] = 1;
        $episode->updateDbData();
        session_destroy();
    }
}

$shellScript = new CronUpdate();
$shellScript->execute();