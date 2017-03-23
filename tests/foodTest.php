<?php
// Filename must end with Test.php
namespace JAMKcomrade\Tests;

require __DIR__ . '/../src/vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class FoodTest extends TestCase
{
    // Must start with test
    public function testFoodStuff()
    {
        $this->assertEquals('1', '1');
    }
}
