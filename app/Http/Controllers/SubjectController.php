<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubjectController extends Controller
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
        $subjects = Subject::OrderBy('id')->paginate();

        $response = [
            'message' => 'Get Subjects Success',
            'status_code' => Response::HTTP_OK,
            'data' => $subjects
        ];

        return response()->json($response, $response['status_code']);
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $professor = Professor::where('id', $input['professor_id'])
                                    ->orWhere('nip', $input['professor_id']);

        if (!isset($professor)) {
            $response = [
                'message' => 'Create Subject Failed, Professor ID Not Valid',
                'status_code' => Response::HTTP_BAD_REQUEST
            ];

            return response()->json($response, $response['status_code']);
        }

        $subject = new Subject();
        $subject->name = $input['name'];
        $subject->professor_id = $input['professor_id'];

        if ($subject->save()) {
            $subject->professor;
            $response = [
                'message' => 'Create Subject Success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $subject
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Create Subject Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }

    public function show($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            $response = [
                'message' => 'Subject Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $subject->professor;

        $response = [
            'message' => 'Show Subject Success',
            'status_code' => Response::HTTP_OK,
            'data' => $subject
        ];

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $professor = Professor::where('id', $input['professor_id'])
                                    ->orWhere('nip', $input['professor_id']);

        if (!isset($professor)) {
            $response = [
                'message' => 'Update Subject Failed, Professor ID Not Valid',
                'status_code' => Response::HTTP_BAD_REQUEST
            ];

        }

        $subject = Subject::find($id);

        if (!$subject) {
            $response = [
                'message' => 'Subject Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $subject->name = $input['name'];
        $subject->professor_id = $input['professor_id'];

        if ($subject->save()) {
            $response = [
                'message' => 'Update Subject Success',
                'status_code' => Response::HTTP_OK,
                'data' => $subject
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Update Subject Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }

    public function delete($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            $response = [
                'message' => 'Subject Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        if ($subject->delete()) {
            $response = [
                'message' => 'Delete Subject Success',
                'status_code' => Response::HTTP_OK,
                'data' => $subject
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Delete Subject Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }
}
