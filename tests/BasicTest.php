<?php
namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__, '.env.testing');
        $dotenv->load();
    }
}