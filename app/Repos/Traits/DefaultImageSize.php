<?php

namespace App\Repos\Traits;

trait DefaultImageSize
{
    public function getSizes(): array
    {
        return [
            'large' => [1920, 1440],
            'medium' => [1200, 900],
            'small' => [800, 600],
            'extra_small' => [400, 300],
        ];
    }
}
