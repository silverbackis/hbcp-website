<?php

namespace AppBundle\Utils;

use AppBundle\Model\SelectOption;

class FilterUtils
{
    public function getCategoryFilterOptions(array $resources) {
        $options = [];
        $filterCats = [];
        /**
         * @var \AppBundle\Entity\Resource $resource
         */
        foreach ($resources as $resource)
        {
            $filterCat = $resource->getCategory();

            if (!in_array($filterCat->getId(), $filterCats)) {
                $selectOption = new SelectOption();
                $selectOption->setValue($filterCat->getId());
                $label = $filterCat->getBreadcrumbs(true, false);
                $selectOption->setLabel($label === '' ? 'General' : $label);
                $options[] = $selectOption;

                $filterCats[] = $filterCat->getId();
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
