<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Annotation;

use Attribute;
use Doctrine\Common\Annotations\NamedArgumentConstructorAnnotation;
use Ray\Di\Di\Qualifier;

/**
 * @Annotation
 * @Target("METHOD")
 * @Qualifier
 * @deprecated -- No one using?
 */
#[Attribute(Attribute::TARGET_METHOD), Qualifier]
final class Write implements NamedArgumentConstructorAnnotation
{
    /** @var string */
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
