<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get Categories
     * * Return Categories Data
     */
    public function index(Request $request)
    {
        $categories = Category::OrderBy("name", "ASC")->paginate();

        $response = [
            'message' => 'Get Categories Success',
            'status_code' => Response::HTTP_OK,
            'data' => $categories
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validationRules = [
            'name' => 'required|string|max:200',
            'slug' => 'required|string'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $randomStr = Str::random(10);

        $category = new Category();

        $category->name = $input['name'];
        $category->slug = $input['slug']."-".$randomStr;

        if ($category->save()) {
            $response = [
                'message' => 'Create Category Success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $category
            ];

            return response()->json($response, Response::HTTP_CREATED);
        }

        $response = [
            'message' => 'Create Category Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function show(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            $response = [
                'message' => 'Category Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        $response = [
            'message' => 'Show Category Success',
            'status_code' => Response::HTTP_OK,
            'data' => $category
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        
        $validationRules = [
            'name' => 'required|string|max:200',
            'slug' => 'required|string'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->errors()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $category = Category::find($id);

        if (!$category) {
            $response = [
                'message' => 'Category Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        $randomStr = Str::random(10);
        
        $category->name = $input['name'];
        $category->slug = $input['slug']."-".$randomStr;

        if ($category->save()) {
            $response = [
                'message' => 'Update Category Success',
                'status_code' => Response::HTTP_OK,
                'data' => $category
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'message' => 'Update Category Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function delete(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            $response = [
                'message' => 'Category Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        if ($category->delete()) {
            $response = [
                'message' => 'Delete Category Success',
                'status_code' => Response::HTTP_OK
            ];
    
            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'message' => 'Delete Category Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
