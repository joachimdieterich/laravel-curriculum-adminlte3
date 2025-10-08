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
            if ($tag->translation ?? null) {
                return;
            }

            $tag->translation = $tag->getTranslations('name')[$tag->getLocale()];
        });

        static::retrieved(static function (Tag $tag) {
            if ($tag->translation ?? null) {
                return;
            }

            $tag->translation = $tag->getTranslations('name')[$tag->getLocale()];
        });
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

    /**
     * @return array{count: int, result: Collection}
     */
    public function fromParamForSelect2(
        string $name,
        string $type,
        ?string $locale = null,
        array $get = ['id', 'text'],
        bool $withGlobalType = true
    ): array {
        $locale = $locale ?? static::getLocale();

        return self::withoutEvents(
            function () use ($locale, $name, $type, $get, $withGlobalType) {
                $builder = $this->scopeContaining(
                    static::select('id', "name->{$locale} as text"),
                    $name,
                    $locale
                )->where('type', $type);

                if ($withGlobalType) {
                    $builder->orWhereNull('type');
                }

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
                    ->select('id', "name->{$locale} as title")
                    ->whereIn('id', $selected);

                return [
                    'count'  => $builder->count(),
                    'result' => $builder->get($get)
                ];
            }
        );
    }
}
