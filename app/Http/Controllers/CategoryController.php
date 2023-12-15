<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:admin'])->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $categories = Category::paginate(10);
        return CategoryResource::collection($categories);
    }

    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();

        if (Category::where('name', $data['name'])->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "name" => [
                        "name  already registered"
                    ]
                ]
            ], 400));
        }

        $data['slug'] = Str::of($data['name'])->slug('-');

        $category = new Category();
        $category->name = $data['name'];
        $category->slug = $data['slug'];

        if ($request->has('description')) {
            $category->description = $data['description'];
        }

        $category->save();

        return new CategoryResource($category);
    }


    public function show(string $id)
    {
        try {
            $category = Category::where('id', $id)->firstOrFail();
            return new CategoryResource($category);
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

    public function update(CategoryUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $category = Category::where('id', $id)->firstOrFail();

            if ($request->has('name') && $category->name !== $data['name']) {
                if (Category::where('name', $data['name'])->exists()) {
                    throw new HttpResponseException(response([
                        "errors" => [
                            "name" => [
                                "name  already registered"
                            ]
                        ]
                    ], 400));
                }
                $category->name = $data['name'];
                $category->slug = Str::of($data['name'])->slug('-');
            }

            if ($request->has('description')) {
                $category->description = $data['description'];
            }

            $category->save();

            return new CategoryResource($category);
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

    public function destroy(string $id)
    {
        try {
            $category = Category::where('id', $id)->firstOrFail();
            $category->delete();

            return response()->json([
                'data' => [
                    'message' => 'Category deleted successfully'
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
