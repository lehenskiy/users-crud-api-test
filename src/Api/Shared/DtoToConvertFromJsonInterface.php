<?php

declare(strict_types=1);

namespace App\Api\Shared;

interface DtoToConvertFromJsonInterface
{
    public function __construct(array $data);
}
