<?php

$xmlPath = __DIR__ . '/../vendor/start-kit-symfony/start-bundle/phpunit.xml.dist';
$xmlString = file_get_contents($xmlPath);
$xmlString = str_replace('<server name="KERNEL_CLASS" value="AppKernel" />', '<env name="KERNEL_CLASS" value="App\Kernel" />', $xmlString);
file_put_contents($xmlPath, $xmlString);