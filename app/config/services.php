<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use flight\debug\tracy\TracyExtensionLoader;
use Tracy\Debugger;

/*********************************************
 *    BNGRC — Configuration des services     *
 *********************************************
 * Enregistrement des services et intégrations
 * pour l'application FlightPHP BNGRC.
 *
 * @var array  $config  Depuis config.php
 * @var Engine $app     Instance FlightPHP
 **********************************************/



/*********************************************
 *           Tracy Debugger Setup            *
 *********************************************
 * Tracy : gestionnaire d'erreurs et débogueur PHP.
 * Docs: https://tracy.nette.org/
 **********************************************/
Debugger::enable();
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log';
Debugger::$strictMode = true;
if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
	(new TracyExtensionLoader($app)); // Load FlightPHP Tracy extensions
}

/**********************************************
 *           Database Service Setup           *
 **********************************************/
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;
$app->register('db', $pdoClass, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);

/**********************************************
 *         Third-Party Integrations           *
 **********************************************/
// Ajouter vos intégrations tierces ici si nécessaire
