<?php

namespace App\Repos\Traits;

trait HasMetas
{
    public function deleteMetas(array $metaNames)
    {
        if (empty($metaNames)) {
            return $this;
        }
        $this->metas()->whereIn('name', $metaNames)->delete();

        return $this;
    }

    public function updateMetas(array $metas)
    {
        if (empty($metas)) {
            return $this;
        }

        return $this->deleteMetas(array_keys($metas))->addMetas($metas);
    }

    public function getMeta($name, $defaultValue = null)
    {
        if (!$this->relationLoaded('metas')) {
            return $defaultValue;
        }
        return $this->metas->where('name', $name)->first()->value ?? $defaultValue;
    }

    public function hasMeta($name)
    {
        return (bool)$this->metas->where('name', $name)->first();
    }

    public function addMetas(array $metas)
    {
        if (empty($metas)) {
            return $this;
        }
        $formattedMetas = [];
        foreach ($metas as $name => $value) {
            $formattedMetas[] = compact('name', 'value');
        }
        $this->metas()->createMany($formattedMetas);

        return $this;
    }

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
