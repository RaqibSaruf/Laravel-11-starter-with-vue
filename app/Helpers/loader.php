<?php

declare(strict_types=1);

foreach (glob(__DIR__ . '/*.php') as $filename) {
    require_once $filename;
}
