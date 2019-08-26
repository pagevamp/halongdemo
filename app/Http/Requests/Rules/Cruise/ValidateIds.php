<?php

namespace App\Http\Requests\Rules\Cruise;

use Illuminate\Contracts\Validation\Rule;

class ValidateIds implements Rule
{
    private $model;

    public function __construct($model)
    {
        $this->model = new $model();
    }

    public function passes($attribute, $value)
    {
        return $this->model->whereIn('id', $value)->count() === count($value);
    }

    public function message()
    {
        return 'Your request looks suspicious with :attribute';
    }
}
