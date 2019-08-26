<?php

Route::group(['namespace' => 'Cruise'], function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('cruises', 'CruiseController@store')->name('cruises.create');
        Route::patch('cruises/{id}', 'CruiseController@update')->name('cruises.update');
        Route::delete('cruises/{id}', 'CruiseController@delete')->name('cruises.delete');
    });

    Route::get('cruises', 'CruiseController@index')->name('cruises.index');
    Route::get('cruises/{slug}', 'CruiseController@show')->name('cruises.show');
});
