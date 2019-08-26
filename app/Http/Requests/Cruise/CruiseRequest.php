<?php

namespace App\Http\Requests\Cruise;

use App\Http\Requests\Rules\Cruise\ValidateIds;
use App\Repos\Models\Activity;
use App\Repos\Models\Category;
use App\Repos\Models\Cruise;
use App\Repos\Models\Facility;
use Illuminate\Foundation\Http\FormRequest;

class CruiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $required = 'required';

    public function authorize()
    {
        switch (true) {
            case $this->isMethod('post'):
                return $this->user()->can('create', Cruise::class);
            case $this->isMethod('patch'):
                $this->required = 'sometimes';

                return $this->user()->can('update', Cruise::findOrFail($this->route('id')));
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id');
        $id = $id ? "$id,id" : '';

        return [
            'name' => [$this->required, 'string', 'max:'.config('database.string.name', 120)],
            'slug' => [$this->required, 'alpha_dash', "unique:cruises,slug,$id"],
            'short_description' => [$this->required, 'max:450'],
            'long_description' => [$this->required],
            'route_id' => [$this->required, 'integer'],
            'price' => [$this->required, 'numeric'],
            'published' => ['sometimes', 'boolean'],
            'star' => ['sometimes', 'numeric', 'min:1', 'max:5'],
            'floors' => ['sometimes', 'integer'],
            'seo_title' => ['sometimes', 'required', 'string'],
            'seo_description' => ['sometimes', 'required', 'string'],
            'og_image' => ['sometimes', 'string'],
            'booking_policy' => ['sometimes', 'required', 'string'],
            'why_book_this_cruise' => ['sometimes', 'required', 'string'],
            'seo_keywords' => ['sometimes', 'required', 'string'],
            'cabins' => ['sometimes', 'integer'],
            'guest_capacity' => ['sometimes', 'integer'],
            'built_year' => ['sometimes', 'integer', 'min:1000', 'max:3000'],
            'width' => ['sometimes', 'numeric'],
            'length' => ['sometimes', 'numeric'],
            'height' => ['sometimes', 'numeric'],
            'category_ids' => ['sometimes', 'array', new ValidateIds(Category::class)],
            'activity_ids' => ['sometimes', 'array', new ValidateIds(Activity::class)],
            'facility_ids' => ['sometimes', 'array', new ValidateIds(Facility::class)],
            'boarding_time' => ['sometimes', 'date_format:H:i'],
            'disembarking_time' => ['sometimes', 'date_format:H:i'],
            'itinerary.included' => ['sometimes', 'string'],
            'itinerary.excluded' => ['sometimes', 'string'],
            'itinerary.days.*.day' => ['sometimes', 'integer'],
            'itinerary.days.*.title' => ['sometimes', 'string'],
            'itinerary.days.*.details' => ['sometimes', 'string'],
            'video' => ['nullable'],
            'featured_index' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ];
    }
}
