<?php

namespace App\Tests;

use BlockPage;
use HomePage;
use Page;
use SilverStripe\Dev\FunctionalTest;

/**
 * The site's own page types render with the theme and silverwind-site
 * (features themselves are tested in their modules).
 */
class PageTypesTest extends FunctionalTest
{
    protected $usesDatabase = true;

    public function testPageTypesRender(): void
    {
        foreach ([HomePage::class => 'home', Page::class => 'about', BlockPage::class => 'blocks'] as $class => $segment) {
            $page = $class::create(['Title' => ucfirst($segment), 'URLSegment' => $segment]);
            $page->write();
            $page->publishRecursive();

            $response = $this->get('/' . $segment);

            $this->assertSame(200, $response->getStatusCode(), $class);
            $this->assertStringNotContainsString('[Emergency]', $response->getBody(), $class);
            $this->assertStringContainsString('data-consent-cookie', $response->getBody(), "{$class}: theme shell rendered");
        }
    }

    public function testSearchIncludesBlockText(): void
    {
        $this->assertInstanceOf(
            \Antlion\SilverwindSite\Search\SiteSearchForm::class,
            (new \PageController())->SearchForm()
        );
    }
}
