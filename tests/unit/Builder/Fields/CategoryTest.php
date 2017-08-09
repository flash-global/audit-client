<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Category;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class CategoryTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $category = new Category($builder);

        $category->build('fake-category');

        $this->assertEquals([
            'category' => 'fake-category'
        ], $builder->getParams());
    }
}