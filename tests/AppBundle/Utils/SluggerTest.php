<?php

namespace tests\AppBundle\Utils\Slugger;

use AppBundle\Utils\Slugger;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class SluggerTest extends TestCase
{
    /**
     * Test the Slugger.
     */
    public function testSlugger()
    {
        $slugger = new Slugger();
        $slug = $slugger->slugify('a great title');

        $this->assertEquals('a-great-title', $slug);
        $this->assertEquals('a-great-title', $slugger->getSlug());
    }
}
