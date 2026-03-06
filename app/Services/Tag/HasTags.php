<?php

namespace App\Services\Tag;

use App\User;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasTags
{
    use \Spatie\Tags\HasTags;

    public function tags(?User $currentUser = null)
    {
        $currentUser = $currentUser ?? auth()->user();

        $morph = $this
            ->morphToMany(self::getTagClassName(), $this->getTaggableMorphName(), $this->getTaggableTableName())
            ->using($this->getPivotModelClassName());

        if ($currentUser === null) {
            return $morph->ordered();
        }

        return $morph
            ->where('user_id', $currentUser->id)
            ->ordered();
    }

    public function isFavourited(): Attribute
    {
        return new Attribute(
            get: fn () => $this->hasTag(trans('global.tag.favourite.singular')),
        );
    }

    public function isHidden(): Attribute
    {
        return new Attribute(
            get: fn () => $this->hasTag(trans('global.tag.hidden.singular')),
        );
    }
}