<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['store', 'update']);
        $this->middleware(['auth:sanctum', 'abilities:admin'])->only(['destroy']);
    }

    /**
     * Display the specified resource.
     */
    public function index(string $id)
    {
        try {
            $reviews = Review::where('product_id', $id)->paginate(10);
            return new ReviewCollection($reviews);
        } catch (Exception $e) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 400));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewStoreRequest $request, string $product_id)
    {
        $data = $request->validated();

        if (Review::where('user_id', $request->user()->id)->where('product_id', $product_id)->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "product_id" => [
                        "You already reviewed this product"
                    ]
                ]
            ], 400));
        }
        $review = new Review();
        $review->user_id = $request->user()->id;
        $review->product_id = $product_id;
        $review->title = $data['title'];
        $review->content = $data['content'];
        $review->rating = $data['rating'];
        $review->save();

        return new ReviewResource($review);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $product_id)
    {
        try {
            $review = Review::where('id', $product_id)->firstOrFail();
            return new ReviewResource($review);
        } catch (Exception $e) {
            throw new HttpResponseException(response([
                "errors" => [
                    "product_id" => [
                        "product_id not found"
                    ]
                ]
            ], 400));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewUpdateRequest $request, string $productId, $reviewId): ReviewResource
    {
        $data = $request->validated();

        try {
            $review = Review::where('id', $reviewId)->firstOrFail();

            if ($review->user_id != $request->user()->id) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "product_id" => [
                            "You are not allowed to update this review"
                        ]
                    ]
                ], 400));
            }

            if ($request->has('title')) {
                $review->title = $data['title'];
            }

            if ($request->has('content')) {
                $review->content = $data['content'];
            }

            if ($request->has('rating')) {
                $review->rating = $data['rating'];
            }

            $review->save();

            return new ReviewResource($review);
        } catch (Exception $e) {
            throw new HttpResponseException(response([
                "errors" => [
                    "product_id" => [
                        "product_id not found"
                    ]
                ]
            ], 400));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $productId, $reviewId)
    {
        try {
            $review = Review::where('id', $reviewId)->firstOrFail();
            $review->delete();

            return response()->json([
                'data' => [
                    'message' => 'Review deleted successfully'
                ]
            ], 200);
        } catch (Exception $e) {
            throw new HttpResponseException(response([
                "errors" => [
                    "id" => [
                        "id not found"
                    ]
                ]
            ], 400));
        }
    }
}
