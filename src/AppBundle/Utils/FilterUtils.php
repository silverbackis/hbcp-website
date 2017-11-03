<?php

namespace AppBundle\Utils;

use AppBundle\Model\SelectOption;

class FilterUtils
{
    public function getCategoryFilterOptions(array $resources) {
        $filterCats = [];
        $options = [];
        /**
         * @var \AppBundle\Entity\Resource $resource
         */
        foreach ($resources as $resource)
        {
            $filterCat = $resource->getCategory();
            $isGeneral = $filterCat->getParent();
            $showBreadcrumbs = $isGeneral && $filterCat->getParent()->getParent();
            // Check if category's parent is top level
            if ($showBreadcrumbs && !$filterCat->getFixed()) {
                $filterCat = $filterCat->getParent();
            }
            if (!in_array($filterCat->getId(), $filterCats)) {
                $parent = $filterCat->getParent() ?: $filterCat;
                $filterCats[] = $filterCat->getId();

                $selectOption = new SelectOption();
                $selectOption->setValue($parent->getId());
                $label = $filterCat->getParent() ? $filterCat->getName() : 'General';

                $selectOption->setLabel($showBreadcrumbs ? $filterCat->getBreadcrumbs() : $label);
                $options[] = $selectOption;
            }
        }
        return $options;
    }

    public function getResourceTypeFilterOptions (array $resources)
    {
        $types = [];
        $options = [];
        /**
         * @var \AppBundle\Entity\Resource $resource
         */
        foreach ($resources as $resource)
        {
            if (strtolower($resource->getPathType()) == 'dropbox') {
                if ($resource->getResourceType()) {
                    if (!in_array($resource->getResourceType()->getId(), $types)) {
                        $types[] = $resource->getResourceType()->getId();

                        $selectOption = new SelectOption();
                        $selectOption->setValue($resource->getResourceType()->getId());
                        $selectOption->setLabel($resource->getResourceType()->getName());
                        $options[] = $selectOption;
                    }
                }
            } else {
                if (!in_array($resource->getPathType(), $types)) {
                    $types[] = $resource->getPathType();

                    $selectOption = new SelectOption();
                    $selectOption->setValue($resource->getPathType());
                    $selectOption->setLabel($resource->getPathType());
                    $options[] = $selectOption;
                }
            }
        }
        return $options;
    }
}
