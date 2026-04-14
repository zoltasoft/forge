<?php

declare(strict_types=1);

namespace App\Console\ZoltaTest {
    class DummyCommand {}

    class DummyHandler
    {
        public function __invoke(): void {}
    }
}

namespace App\Console\AutoRefresh {
    class DummyCommand {}

    class DummyHandler
    {
        public function __invoke(): void {}
    }
}
