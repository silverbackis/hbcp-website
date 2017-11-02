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
            $showBreadcrumbs = $filterCat->getParent()->getParent();
            // Check if category's parent is top level
            if ($showBreadcrumbs) {
                $filterCat = $filterCat->getParent();
            }
            if (!in_array($filterCat->getId(), $filterCats)) {
                $filterCats[] = $filterCat->getId();

                $selectOption = new SelectOption();
                $selectOption->setValue($filterCat->getParent()->getId());
                $selectOption->setLabel($showBreadcrumbs ? $filterCat->getBreadcrumbs() : $filterCat->getName());
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
