<?php

namespace Tests\Feature\Media;

use App\Repos\Models\Media;
use App\Services\Media\HasMediaInterface;

trait CreateMediaTrait
{
    public function getExpectedFileName($file)
    {
        $mediaNames = Media::all()->pluck('name')->toArray();
        $nameWithoutExtension = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fullImageName = str_slug($nameWithoutExtension).'.'.$file->getClientOriginalExtension();
        $counter = 1;
        while (\in_array($fullImageName, $mediaNames)) {
            $fullImageName = str_slug($nameWithoutExtension).'-'.$counter.'.'.$file->getClientOriginalExtension();
            ++$counter;
        }

        return $fullImageName;
    }

    public function getExpectedThumbnails($file, HasMediaInterface $entity)
    {
        $folder = config('storage.folder.media');
        $imageSizes = [];
        foreach ($entity->getSizes() as $key => $size) {
            $pathToSave = $folder.'/'.$key.'/'.$this->getExpectedFileName($file);

            $imageSizes[$key] = [
                'relative_path' => $pathToSave,
                'absolute_path' => $this->storage->url($pathToSave),
            ];
        }

        return $imageSizes;
    }
}
