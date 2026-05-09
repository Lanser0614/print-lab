<?php

namespace Tests;

use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        URL::defaults(['locale' => 'ru']);
    }
}
