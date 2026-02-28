<?php

declare(strict_types=1);

use Phel\Config\PhelConfig;
use Phel\Config\PhelExportConfig;
use Phel\Config\PhelBuildConfig;

return (new PhelConfig())
    ->setSrcDirs(['src'])
    ->setTestDirs(['tests'])
    ->setVendorDir('vendor')
    ->setErrorLogFile('data/error.log')
    ->setIgnoreWhenBuilding(['src/main.phel'])
    ->setKeepGeneratedTempFiles(false);
