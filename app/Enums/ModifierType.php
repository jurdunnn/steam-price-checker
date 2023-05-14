<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class ModifierType extends Enum
{
    const PLATFORM = 0;
    const METACRITIC = 1;
}
