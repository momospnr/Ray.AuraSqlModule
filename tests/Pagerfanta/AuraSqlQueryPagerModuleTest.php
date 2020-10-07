<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Pagerfanta;

use Iterator;
use Ray\Di\Injector;

use function assert;

class AuraSqlQueryPagerModuleTest extends AuraSqlQueryTestCase
{
    public function testNewInstance(): AuraSqlQueryPager
    {
        $factory = (new Injector(new AuraSqlPagerModule()))->getInstance(AuraSqlQueryPagerFactoryInterface::class);
        /** @var AuraSqlQueryPagerFactoryInterface $factory */
        $this->assertInstanceOf(AuraSqlQueryPagerFactory::class, $factory);
        $pager = $factory->newInstance($this->pdo, $this->select, 1, '/?page={page}&category=sports');
        $this->assertInstanceOf(AuraSqlQueryPager::class, $pager);

        return $pager;
    }

    public function testNewInstanceWithBinding(): AuraSqlQueryPager
    {
        $this->select->where('id = :id')->bindValue('id', 1);
        $factory = (new Injector(new AuraSqlPagerModule()))->getInstance(AuraSqlQueryPagerFactoryInterface::class);
        /** @var AuraSqlQueryPagerFactoryInterface $factory */
        $this->assertInstanceOf(AuraSqlQueryPagerFactory::class, $factory);
        $pager = $factory->newInstance($this->pdo, $this->select, 1, '/?page={page}&category=sports');
        $this->assertInstanceOf(AuraSqlQueryPager::class, $pager);

        return $pager;
    }

    /**
     * @depends testNewInstance
     */
    public function testArrayAccess(AuraSqlQueryPager $pager): void
    {
        $page = $pager[2];
        assert($page instanceof Page);
        $this->assertTrue($page->hasNext);
        $this->assertTrue($page->hasPrevious);
        $expected = [
            [
                'id' => '2',
                'username' => 'Jon Doe',
                'post_content' => 'Post #2',
            ],
        ];
        $this->assertSame($expected, $page->data);
        $expected = '<nav><a href="/?page=1&category=sports" rel="prev">Previous</a><a href="/?page=1&category=sports">1</a><span class="current">2</span><a href="/?page=3&category=sports">3</a><a href="/?page=4&category=sports">4</a><a href="/?page=5&category=sports">5</a><span class="dots">...</span><a href="/?page=50&category=sports">50</a><a href="/?page=3&category=sports" rel="next">Next</a></nav>';
        $this->assertSame($expected, (string) $page);
        $this->assertSame(50, $page->total);
    }

    /**
     * @depends testNewInstance
     */
    public function testArrayAccessWithMaxPage(AuraSqlQueryPager $pager): void
    {
        $page = $pager[50];
        assert($page instanceof Page);
        $this->assertFalse($page->hasNext);
        $this->assertTrue($page->hasPrevious);
        $expected = [
            [
                'id' => '50',
                'username' => 'Jon Doe',
                'post_content' => 'Post #50',
            ],
        ];
        $this->assertSame($expected, $page->data);
        $expected = '<nav><a href="/?page=49&category=sports" rel="prev">Previous</a><a href="/?page=1&category=sports">1</a><span class="dots">...</span><a href="/?page=46&category=sports">46</a><a href="/?page=47&category=sports">47</a><a href="/?page=48&category=sports">48</a><a href="/?page=49&category=sports">49</a><span class="current">50</span><span class="disabled">Next</span></nav>';
        $this->assertSame($expected, (string) $page);
        $this->assertSame(50, $page->total);
    }

    /**
     * @depends testNewInstance
     */
    public function testIterator(AuraSqlQueryPager $pager): void
    {
        $page = $pager[1];
        $itelator = $page->getIterator();
        $this->assertInstanceOf(Iterator::class, $itelator);
    }

    /**
     * @depends testNewInstanceWithBinding
     */
    public function testArrayAccessWithBinding(AuraSqlQueryPager $pager): void
    {
        $page = $pager[1];
        assert($page instanceof Page);
        $this->assertFalse($page->hasNext);
        $this->assertFalse($page->hasPrevious);
        $expected = [
            [
                'id' => '1',
                'username' => 'Jon Doe',
                'post_content' => 'Post #1',
            ],
        ];
        $this->assertSame($expected, $page->data);
        $expected = '<nav><span class="disabled">Previous</span><span class="current">1</span><span class="disabled">Next</span></nav>';
        $this->assertSame($expected, (string) $page);
        $this->assertSame(1, $page->total);
    }
}
