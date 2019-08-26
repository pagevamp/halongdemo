<?php

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

function createMany($class, array $attributeCollections, array $overwrites = [])
{
    $collections = collect();
    foreach ($attributeCollections as $attributes) {
        $collections->push(create($class, array_merge($attributes, $overwrites)));
    }

    return $collections;
}

function makeMany($class, array $attributeCollections, array $overwrites = [])
{
    $collections = collect();
    foreach ($attributeCollections as $attributes) {
        $collections->push(make($class, array_merge($attributes, $overwrites)));
    }

    return $collections;
}

function createUserWithRole(String $role, $attributes = [], $times = null)
{
    $attributes['role'] = $role;
    return create(\App\Repos\Models\User::class, $attributes, $times);
}

function makeUserWithRole(String $role, $attributes = [], $times = null)
{
    $attributes['role'] = $role;
    return make(\App\Repos\Models\User::class, $attributes, $times);
}

/**
 * This function is required for testing because
 * when you type cast a column to array in Eloquent model,
 * it will enclose any string values within ""
 * for e.g: "\"some example string\""
 *
 * @param $value
 * @return string
 */
function stringify($value)
{
    return json_encode($value);
}

function getPaginationKeys()
{
    return [
        'total',
        'per_page',
        'current_page',
        'last_page',
        'first_page_url',
        'last_page_url',
        'next_page_url',
        'prev_page_url',
        'path',
        'from',
        'to',
    ];
}

/**
 * Determine if two associative arrays are similar
 *
 * Both arrays must have the same indexes with identical values
 * without respect to key ordering
 *
 * @param array $a
 * @param array $b
 * @return bool
 */
function arraysAreSimilar($a, $b)
{
    // if the indexes don't match, return immediately
    if (count(array_diff_assoc($a, $b))) {
        return false;
    }
    // we know that the indexes, but maybe not values, match.
    // compare the values between the two arrays
    foreach ($a as $k => $v) {
        if ($v !== $b[$k]) {
            return false;
        }
    }
    // we have identical indexes, and no unequal values
    return true;
}
