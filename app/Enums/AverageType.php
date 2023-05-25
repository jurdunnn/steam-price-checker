<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class AverageType extends Enum
{
    const REVIEW_POOR = 0;
    const REVIEW_POSITIVE = 1;
}
