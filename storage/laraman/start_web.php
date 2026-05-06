#!/usr/bin/env php
<?php
/*
 * Please don't edit this file,this is auto generate by laraman
 */

use Illuminate\Container\Container;

require_once 'C:\Work_Zample\server/vendor/itinysun/laraman/fixes/WorkmanFunctions.php';

require 'C:\Work_Zample\server/vendor/autoload.php';

//准备workerman的运行环境
\Itinysun\Laraman\Server\LaramanWorker::prepare();

\Itinysun\Laraman\Command\Configs::setBasePath('C:\Work_Zample\server');

$status = \Itinysun\Laraman\Command\Process::run('web');
exit($status);