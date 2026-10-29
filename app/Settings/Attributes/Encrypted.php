<?php

declare(strict_types=1);

namespace App\Settings\Attributes;

use Attribute;

/**
 * Stores a setting encrypted with the application key, for secrets such as API keys.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Encrypted {}
