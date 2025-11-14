<?php

namespace App\Services\Tag;

use App\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use stdClass;

class TagService
{
    public function getEntriesForSelect2ByModel(
        array $selected,
        string $term = '',
        int $page = 1,
        Tag $model = new Tag(),
    ): JsonResponse {
        if (!empty($selected)) {
            $entries = $model->fromIdListForSelect2($selected);
        } else {
            $entries = $model->fromParamForSelect2($term);
        }

        $entries["result"]->map(
            function (Model $item) {
                // Because we selected only a specific value (name) of the json, the cast type need to be changed from array to string.
                $item->mergeCasts([
                    'text' => 'string',
                ]);
            }
        );

        if (!empty($selected)) {
            return response()->json($entries["result"]);
        }

        $resultCount = 25;
        $offset      = ($page - 1) * $resultCount;

        $endCount  = $offset + $resultCount;
        $morePages = $entries["count"] > $endCount;

        $results = array(
            "results"    => $entries["result"],
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
