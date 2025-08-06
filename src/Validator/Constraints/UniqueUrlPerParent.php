<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueUrlPerParent extends Constraint
{
    public string $message = 'The URL "{{ url }}" is already used under the same parent.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
