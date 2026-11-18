<?php

use Antlion\SilverwindSite\Search\SiteSearchForm;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

/**
 * Template hooks the theme uses ($SiteScripts, $Locations) come from
 * antlion/silverwind-site's SiteControllerExtension.
 */
class PageController extends ContentController
{
    private static $allowed_actions = [
        'SearchForm',
    ];

    /**
     * Site search including the text of each page's blocks (overrides the
     * form FulltextSearchable adds, see app/_config.php).
     */
    public function SearchForm()
    {
        return SiteSearchForm::forController($this);
    }

    protected function init()
    {
        parent::init();
        Requirements::set_force_js_to_bottom(true);
        Requirements::block('silverstripe/userforms:client/dist/js/jquery.min.js');
        Requirements::block('innoweb/silverstripe-cookie-consent:client/dist/css/cookieconsent.css');
    }
}
