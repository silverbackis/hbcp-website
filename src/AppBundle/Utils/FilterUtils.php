<?php

namespace AppBundle\Utils;

use AppBundle\Model\SelectOption;

class FilterUtils
{
    public function getCategoryFilterOptions(array $resources): array
    {
        $options = [];
        $filterCats = [];
        /**
         * @var \AppBundle\Entity\Resource $resource
         */
        foreach ($resources as $resource) {
            $filterCat = $resource->getCategory();
            if (!\in_array($filterCat->getId(), $filterCats, true)) {
                $selectOption = new SelectOption();
                $selectOption->setValue($filterCat->getId());
                $label = $filterCat->getBreadcrumbs(true, false);
                if ($label !== '') {
                    $selectOption->setLabel($label);
                } else {
                    $selectOption->setLabel(strtolower($filterCat->getName()) === 'project' ? 'Project' : 'General');
                }
                $options[] = $selectOption;

                $filterCats[] = $filterCat->getId();
            }
        }
        return $options;
    }

    public function getResourceTypeFilterOptions(array $resources): array
    {
        $types = [];
        $options = [];
        /**
         * @var \AppBundle\Entity\Resource $resource
         */
        foreach ($resources as $resource) {
            $resourceType = $resource->getResourceType();
            if (
                $resourceType &&
                !\in_array($resourceType->getId(), $types, true)
            ) {
                $types[] = $resourceType->getId();

                $selectOption = new SelectOption();
                $selectOption->setValue($resourceType->getId());
                $selectOption->setLabel($resourceType->getName());
                $options[] = $selectOption;
            }
//            if (!\in_array($resource->getPathType(), $types, true)) {
//                $types[] = $resource->getPathType();
//
//                $selectOption = new SelectOption();
//                $selectOption->setValue($resource->getPathType());
//                $selectOption->setLabel($resource->getPathType());
//                $options[] = $selectOption;
//            }
        }
        return $options;
    }
}
