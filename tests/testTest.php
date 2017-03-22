<?php
// Filename must end with Test.php
namespace JAMKcomrade\Tests;

require __DIR__ . '/../src/vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class JokuTest extends TestCase
{
    // Must start with test
    public function testName()
    {
        $this->assertEquals('1', '1');
    }
}
