<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/21/19
 * Time: 3:22 PM
 */

namespace Epguides\Models;

define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ . DS . '..' . DS.'..'.DS. 'bootstrap'.DS.'app.php';

class Trigger {

    public function execute()
    {
        $episode = new EpisodesDb();
        session_start();
        $_SESSION['user'] = 1;
        $episode->updateDbData();
        session_destroy();
    }
}

$shellScript = new Trigger();
$shellScript->execute();