<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Score;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScoreController extends Controller
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
        $scores = Score::OrderBy('id')->paginate();

        foreach ($scores as $key => $score) {
           $score->student;
        }

        $response = [
            'message' => 'Get Scores Success',
            'status_code' => Response::HTTP_OK,
            'data' => $scores
        ];

        return response()->json($response, $response['status_code']);
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $student = Student::where('id', $input['student_id']);

        if (!$student) {
            $response = [
                'message' => 'Create Score Failed, Student Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $score = new Score();
        $score->student_id = $input['student_id'];
        $score->task_score = $input['task_score'];
        $score->midterm_score = $input['midterm_score'];
        $score->finals_score = $input['finals_score'];

        if ($score->save()) {
            $response = [
                'message' => 'Create Score Success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $score
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Create Score Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }

    public function show($id)
    {
        $score = Score::find($id);

        if (!$score) {
            $response = [
                'message' => 'Score Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $score->student;

        $response = [
            'message' => 'Show Score Success',
            'status_code' => Response::HTTP_CREATED,
            'data' => $score
        ];

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $score = Score::find($id);

        if (!$score) {
            $response = [
                'message' => 'Update Score Failed, Score Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $input = $request->all();
        $student = Student::where('id', $input['student_id']);

        if (!$student) {
            $response = [
                'message' => 'Update Score Failed, Student Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        $score->student_id = $input['student_id'];
        $score->task_score = $input['task_score'];
        $score->midterm_score = $input['midterm_score'];
        $score->finals_score = $input['finals_score'];

        if ($score->save()) {
            $score->student;
            $response = [
                'message' => 'Update Score Success',
                'status_code' => Response::HTTP_OK,
                'data' => $score
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Update Score Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }

    public function delete($id)
    {
        $score = Score::find($id);

        if (!$score) {
            $response = [
                'message' => 'Delete Score Failed, Score Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, $response['status_code']);
        }

        if ($score->delete()) {
            $response = [
                'message' => 'Delete Score Success',
                'status_code' => Response::HTTP_OK
            ];

            return response()->json($response, $response['status_code']);
        }

        $response = [
            'message' => 'Delete Score Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, $response['status_code']);
    }
}
