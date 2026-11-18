<?php

namespace {
    use Antlion\SilverwindBlocks\Elements\ElementAccordion;
    use Antlion\SilverwindBlocks\Elements\ElementMenu;
    use DNADesign\Elemental\Forms\ElementalAreaField;
    use DNADesign\Elemental\Models\ElementalArea;
    use DNADesign\Elemental\Models\ElementContent;
    use DNADesign\ElementalVirtual\Model\ElementVirtual;
    use SilverStripe\Forms\DropdownField;

    class BlockPage extends Page
    {
        private static $db = [
            'SidebarPosition' => 'Enum("none,left,right", "none")',
        ];

        private static $cms_icon_class = 'font-icon-block-layout';

        private static $has_one = [
            'SidebarElements' => ElementalArea::class,
        ];

        private static $owns = [
            'SidebarElements',
        ];

        private static $cascade_deletes = [
            'SidebarElements',
        ];

        private static $cascade_duplicates = [
            'SidebarElements',
        ];

        /**
         * Block types offered in the sidebar area. Deliberately short - the main
         * content area still allows everything. Tune via YAML if needed.
         */
        private static $sidebar_element_types = [
            ElementMenu::class,
            ElementAccordion::class,
            ElementContent::class,
            ElementVirtual::class,
        ];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields();

              $fields->removeByName(['SidebarPosition']);

            $fields->addFieldToTab('Root.Sidebar', DropdownField::create('SidebarPosition', 'Sidebar position', [
                'none'  => 'None (no sidebar)',
                'left'  => 'Left',
                'right' => 'Right',
            ])->setDescription('Where the sidebar sits relative to the main content. "None" hides it on the front end.'));

            // ElementalAreasExtension scaffolds an editor for every ElementalArea
            // has_one onto Root.Main. Move the sidebar one onto its own tab and
            // restrict which blocks can be added there.
            $fields->removeByName('SidebarElements');

            if ($this->isInDB()) {
                $sidebarTypes = [];
                foreach ((array) $this->config()->get('sidebar_element_types') as $class) {
                    if ($class && class_exists($class)) {
                        $sidebarTypes[$class] = singleton($class)->getType();
                    }
                }

                $fields->addFieldToTab('Root.Sidebar', ElementalAreaField::create(
                    'SidebarElements',
                    $this->SidebarElements(),
                    $sidebarTypes
                ));
            }

            return $fields;
        }

        /**
         * True when a sidebar should actually render: a position is set and the
         * sidebar area has at least one block.
         */
        public function HasSidebar(): bool
        {
            if (!$this->SidebarPosition || $this->SidebarPosition === 'none') {
                return false;
            }

            $area = $this->SidebarElements();

            return $area && $area->exists() && $area->Elements()->exists();
        }
    }
}
