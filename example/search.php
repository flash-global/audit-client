<?php

use Fei\Service\Audit\Client\Audit;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

require __DIR__ . '/../vendor/autoload.php';

$start_time = microtime(true);

$audit = new Audit([Audit::OPTION_BASEURL =>'http://127.0.0.1:8084', Audit::OPTION_ENABLED => true]);
$audit->setTransport(new Fei\ApiClient\Transport\BasicTransport());

$builder = new SearchBuilder();
$builder->message()->beginsWith('shaq');
$builder->context()->key('shaq')->like('2017-06-28T09:35:35+00:00');

/** @var \Fei\ApiClient\ResponseDescriptor $log */
$log = null;
$retrieve = function () use ($audit, $builder, &$log) {
    $log = $audit->retrieve($builder);
};

$retrieve();
$end_time = microtime(true);

if ($log instanceof \Fei\ApiClient\ResponseDescriptor) {
    print_r(json_decode((string) $log->getBody(), true));
    echo('Response '. $log->getCode(). PHP_EOL);
} else {
    echo "An error occurred.".PHP_EOL;
}

echo "time: ", bcsub($end_time, $start_time, 2), "\n";
