<?php

namespace App\Services\Tag;

use App\User;

trait HasTags
{
    use \Spatie\Tags\HasTags;

    public function tags(?User $currentUser = null)
    {
        $currentUser = $currentUser ?? auth()->user();

        return $this
            ->morphToMany(self::getTagClassName(), $this->getTaggableMorphName(), $this->getTaggableTableName())
            ->using($this->getPivotModelClassName())
            ->where('user_id', $currentUser->id)
            ->ordered();
    }
}