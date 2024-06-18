<?php

namespace App\Enums;

use MadWeb\Enum\Enum;

/**
 * @method static ProjectStatusEnum FOO()
 * @method static ProjectStatusEnum BAR()
 * @method static ProjectStatusEnum BAZ()
 */
final class ProjectStatusEnum extends Enum
{
    const default = 0;
    const PUBLISH = 1;
    const DRAFT = 0;
}
