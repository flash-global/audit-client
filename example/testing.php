<?php

require __DIR__ . '/../vendor/autoload.php';

use Fei\Service\Audit\Entity\AuditEvent;
use Fei\Service\Audit\Client\Audit;

$start_time = microtime(true);

$audit = new Audit([Audit::OPTION_BASEURL =>'http://127.0.0.1:8084', Audit::OPTION_ENABLED => true]);
$audit->setTransport(new Fei\ApiClient\Transport\BasicTransport());

$auditEvent = new AuditEvent();
$auditEvent->setMessage('Hello World!');
$auditEvent->setLevel(AuditEvent::LVL_ERROR);
$auditEvent->setCategory(AuditEvent::PERFORMANCE);
$auditEvent->setContext([
   'type' => 8,
   'message' => 'Undefined variable: a',
   'file' => 'C:\WWW\index.php',
   'line' => 2
]);

/** @var \Fei\ApiClient\ResponseDescriptor $log */
$log = null;
$notify = function () use ($audit, $auditEvent, &$log) {
    $log = $audit->notify($auditEvent, ['context' => ['x' => 'y']]);
};

$notify();

$end_time = microtime(true);

if ($log instanceof \Fei\ApiClient\ResponseDescriptor) {
    print_r((string) $log->getBody());
    echo('Response '. $log->getCode(). PHP_EOL);
} else {
    echo "An error occurred.".PHP_EOL;
}

echo "time: ", bcsub($end_time, $start_time, 2), "\n";
