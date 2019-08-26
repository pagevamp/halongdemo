<?php

namespace App\Repos\Traits;

use App\Http\Resources\MediaResource;
use App\Repos\Models\Media;

/**
 * When using this trait, please add your Eloquent model to moprhMap array in AppServiceProvider.php.
 *
 * Trait HasMedia
 */
trait HasMedia
{
    public function media()
    {
        return $this->morphMany(Media::class, 'subject');
    }

    public function getCategorisedMedia()
    {
        $transformedMedia = [];
        if ($this->media->count() > 0) {
            $categorisedMedia = $this->media->sortByDesc('created_at')->groupBy('category');
            foreach ($categorisedMedia as $category => $media) {
                if (\in_array($category, $this->getSingleImageCategories())) {
                    $transformedMedia[$category] = new MediaResource($media->first());
                    continue;
                }

                foreach ($media as $medium) {
                    $transformedMedia[$category][] = new MediaResource($medium);
                }
            }
        }

        return $transformedMedia;
    }

    public function getSingleImageCategories(): array
    {
        return [];
    }
}
