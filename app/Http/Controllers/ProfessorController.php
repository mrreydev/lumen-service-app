<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfessorController extends Controller
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

    public function index()
    {
        $professors = Professor::OrderBy('id', 'ASC')->paginate();

        $response = [
            'message' => 'Get Professors Success',
            'status_code' => Response::HTTP_OK,
            'data' => $professors
        ];

        return response()->json($response, $response['status_code']);
    }

    public function create(Request $request)
    {
        //! Create NIP -> app/helpers.php
        $nip = generateParentNum();

        $professor = new Professor();

        $professor->nip = $nip;
        $professor->name = $request->name;

        $response = [
            'message' => 'Create Professor Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        if ($professor->save()) {
            $response['message'] = 'Create Professor Success';
            $response['data'] = $professor;
            $response['status_code'] = Response::HTTP_OK;
        }

        return response()->json($response, $response['status_code']);
    }

    public function show($id)
    {
        $professor = Professor::where('id', $id)
                                ->orWhere('nip', $id)
                                ->first();

        if (!$professor) {
            $response = [
                'message' => 'Data Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Show Professor Success',
            'status_code' => Response::HTTP_OK,
            'data' => $professor
        ];

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $professor = Professor::where('id', $id)
                                ->orWhere('nip', $id)
                                ->first();

        if (!$professor) {
            $response = [
                'message' => 'Update Professor Failed, Data Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $professor->name = $request->name;

        if ($professor->save()) {
            $response = [
                'message' => 'Show Professor Success',
                'status_code' => Response::HTTP_OK,
                'data' => $professor
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Update Professor Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }

    public function delete($id) {
        $professor = Professor::where('id', $id)
                                ->orWhere('nip', $id)
                                ->first();

        $response = [
            'message' => 'Delete Professor Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        if (isset($professor) && $professor->delete()) {
            $response['message'] = 'Delete Professor Success';
            $response['status_code'] = Response::HTTP_OK;
        }

        return response()->json($response, $response['status_code']);
    }
}
