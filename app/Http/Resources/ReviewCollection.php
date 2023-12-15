<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReviewCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // add name to the response
        return [
            "data" => $this->collection->transform(function ($review) {
                return [
                    "id" => $review->id,
                    "title" => $review->title,
                    "content" => $review->content,
                    "rating" => $review->rating,
                    "user" => [
                        "id" => $review->user->id,
                        "name" => $review->user->name,
                    ],
                    "product" => [
                        "id" => $review->product->id,
                        "name" => $review->product->name,
                    ],
                ];
            }),
        ];
    }
}
