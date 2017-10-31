<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Resource extends Constraint
{
    public $invalid_url_message = 'The URL "{{ url }}" returns status code {{ status_code }}';
    public $unresolved_url_message = 'The URL "{{ url }}" cannot be resolved';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
