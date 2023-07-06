<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function getPosts(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();

            $posts = Post::where("sender", $login_user["UserName"])->get();

            foreach($posts as $post) {
                $temp_img_array = array();
                $img_array = explode(",", $post["images"]);
                foreach($img_array as $image) {
                    $image = env('APP_URL').Storage::url("upload/posts/").$image;
                    array_push($temp_img_array, $image);
                }
                $post["images"] = $temp_img_array;
            }

            $response['data'] = $posts;
            $response['message'] = 'Post Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
    public function getPost(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "id" => "required|numeric",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $id = $request_data["id"];

            $post = Post::find($id);

            $temp_img_array = array();

            if ($post["images"] != "") {

                $img_array = explode(",", $post["images"]);

                foreach($img_array as $image) {
                    $image = env('APP_URL').Storage::url("upload/posts/").$image;
                    array_push($temp_img_array, $image);
                }
                
                $post["images"] = $temp_img_array;
                
            }

            $response['data'] = $post;
            $response['message'] = 'Post Data fetched successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
    public function savePost(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                "title" => "required|string",
                "content" => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $login_user = $request->user();

            $title = $request_data["title"];
            $content = $request_data["content"];
            $img_list = $request_data["img_list"] ?? "";

            $img_array = array();

            foreach($img_list as $item) {

                $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $item["img"]));

                file_put_contents(storage_path('app/public/upload/posts/' . $item["fileName"]), $decodedData);

                array_push($img_array, $item["fileName"]);

            }

            $date = Carbon::now("Asia/Hong_Kong")->format("Y-m-d H:i:s");

            $post = new Post;

            $post->sender = $login_user["UserName"];
            $post->title = $title;
            $post->content = $content;
            $post->images = implode(",", $img_array);
            $post->status = 0;
            $post->created_at = $date;
            
            $post->save();

            $response['message'] = 'Post Data added successfully!';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
