<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:admin'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Product::withCount('reviews')->withAvg('reviews', 'rating');

        if (request()->has('search')) {
            $searchName = request()->get('search');
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        if (request()->has('category')) {
            $categoryName = request()->get('category');
            $category = Category::where('slug', $categoryName)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        if (request()->has('popularity')) {
            $query->orderBy('reviews_count', 'desc')->orderBy('reviews_avg_rating', 'desc');
        }

        if (request()->has('price')) {
            $price = request()->get('price');
            if ($price === 'highest') {
                $query->orderBy('price', 'desc');
            } else if ($price === 'lowest') {
                $query->orderBy('price', 'asc');
            }
        }

        if (request()->has('newest')) {
            $query->orderBy('created_at', 'desc');
        }

        if (request()->has('oldest')) {
            $query->orderBy('created_at', 'asc');
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        if (Product::where('name', $data['name'])->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "name" => [
                        "name  already registered"
                    ]
                ]
            ], 400));
        }

        if (!Category::where('id', $data['category_id'])->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "category_id" => [
                        "category_id not found"
                    ]
                ]
            ], 400));
        }

        $image_path = $request->file('image')->store('image', 'public');

        $product = new Product();
        $product->name = $data['name'];
        $product->slug = Str::of($data['name'])->slug('-');
        $product->price = $data['price'];
        $product->category_id = $data['category_id'];
        $product->image = $image_path;
        $product->save();

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::withCount('reviews')->withAvg('reviews', 'rating')->where('id', $id)->firstOrFail();
            return new ProductResource($product);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $product = Product::where('id', $id)->firstOrFail();

            if ($request->has('name') && $product->name !== $data['name']) {
                if (Category::where('name', $data['name'])->exists()) {
                    throw new HttpResponseException(response([
                        "errors" => [
                            "name" => [
                                "name  already registered"
                            ]
                        ]
                    ], 400));
                }
                $product->name = $data['name'];
                $product->slug = Str::of($data['name'])->slug('-');
            }

            if ($request->has('price')) {
                $product->price = $data['price'];
            }

            if ($request->has('category_id')) {
                if (!Category::where('id', $data['category_id'])->exists()) {
                    throw new HttpResponseException(response([
                        "errors" => [
                            "category_id" => [
                                "category_id not found"
                            ]
                        ]
                    ], 400));
                }
                $product->category_id = $data['category_id'];
            }

            $product->save();

            return new ProductResource($product);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::where('id', $id)->firstOrFail();
            $product->delete();

            return response()->json([
                'data' => [
                    'message' => 'Product deleted successfully'
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
