<?php

namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Tag extends \Spatie\Tags\Tag
{
    protected static function booted(): void
    {
        static::created(static function (Tag $tag) {
            if ($tag->translation ?? null || empty($tag->getTranslations('name'))) {
                return;
            }

            $tag->translation = $tag->getTranslations('name')[$tag->getLocale()];
        });

        static::retrieved(static function (Tag $tag) {
            if ($tag->translation ?? null || empty($tag->getTranslations('name'))) {
                return;
            }

            $tag->translation = $tag->getTranslations('name')[$tag->getLocale()];
        });
    }

    public static function getTypesWithGlobal(): SupportCollection
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return static::groupBy(['type'])->orderBy('type')->pluck('type');
    }

    public static function getTypes(): SupportCollection
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        return static::groupBy(['type'])->whereNotNull('type')->orderBy('type')->pluck('type');
    }

    public static function getTranslatedTypes(): SupportCollection
    {
        return self::getTypes()->map(static function (?string $type) {
            return trans('global.tag.types.' . $type);
        });
    }

    public function scopeContaining(Builder $query, string $name, $locale = null, ?User $currentUser = null): Builder
    {
        $locale      = $locale ?? static::getLocale();
        $currentUser = $currentUser ?? auth()->user();

        return $query->where('user_id', '=', $currentUser->id)
            ->whereRaw(
                'lower(' . $this->getQuery()->getGrammar()->wrap('name->' . $locale) . ') like ?',
                ['%' . mb_strtolower($name) . '%']
            );
    }

    /**
     * @return array{count: int, result: Collection}
     */
    public function fromParamForSelect2(
        string $name,
        string $type,
        array $get = ['id', 'text'],
        bool $withGlobalType = true,
    ): array {
        $locale      = static::getLocale();

        return self::withoutEvents(
            function () use ($locale, $name, $type, $get, $withGlobalType) {
                $builder = $this->scopeContaining(
                    static::select(['id', "name->{$locale} as text"]),
                    $name,
                    $locale
                )->where(function ($builder) use($withGlobalType, $type) {
                    $builder->where('type', $type);

                    if ($withGlobalType) {
                        $builder->orWhereNull('type');
                    }
                });

                return [
                    'count'  => $builder->count(),
                    'result' => $builder->get($get)
                ];
            }
        );
    }

    /**
     * @return array{count: int, result: Collection}
     */
    public function fromIdListForSelect2(array $selected, ?string $locale = null, array $get = ['id', 'title']): array
    {
        $locale = $locale ?? static::getLocale();

        return self::withoutEvents(
            function () use ($locale, $selected, $get) {
                $builder = $this
                    ->select(['id', "name->{$locale} as title"])
                    ->whereIn('id', $selected);

                return [
                    'count'  => $builder->count(),
                    'result' => $builder->get($get)
                ];
            }
        );
    }

    public static function findOrCreateFromString(
        string $name,
        ?string $type = null,
        ?string $locale = null,
        ?User $currentUser = null
    ) {
        $currentUser = $currentUser ?? auth()->user();
        $locale      = $locale ?? static::getLocale();

        $tag = static::findFromString($name, $type, $locale);

        if (!$tag) {
            $tag = static::create([
                'name'    => [$locale => $name],
                'type'    => $type,
                'user_id' => $currentUser->id,
            ]);
        }

        return $tag;
    }

    public static function findFromString(
        string $name,
        ?string $type = null,
        ?string $locale = null,
        ?User $currentUser = null
    ): Tag|null {
        $currentUser = $currentUser ?? auth()->user();
        $locale      = $locale ?? static::getLocale();

        return static::query()
            ->where('type', $type)
            ->where('user_id', '=', $currentUser->id)
            ->where(function ($query) use ($name, $locale) {
                $query->where("name->{$locale}", $name)
                    ->orWhere("slug->{$locale}", $name);
            })
            ->first();
    }
}
