<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoListResource;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $todos = Todo::all();

        return response()->json([
            'status' => 'success',
            'count' => count($todos),
            'todos' => TodoListResource::collection($todos),
        ]);
    }

    public function index_paging(Request $request)
    {
       
        $query = Todo::query();
        if ($request->name) {
            $query = $query->where('title', $request->name);
        }
        if ($request->search) {
            $query = $query->where('title', 'LIKE', '%' . $request->search . '%')->orWhere('description', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->order_by == 'desc') {
            $query = $query->orderBy("title", "desc");
        }else{
            $query = $query->orderBy("title", "acs"); 
        }
        if ($request->number) {
            $query = $query->paginate($request->number);
        }
        else{
            $query = $query->paginate(10);
        }
        
        $results = $query;

        return response()->json([
            'todos' => $results
        ]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'user_id' => 'required|string'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 404);
        }

        list($type, $data) = explode(';', $request->url);
        list($extra, $pictype) = explode('/', $type);
        list(, $data)  = explode(',', $data);
        $data = base64_decode($data);
        $url = '..\app\upload\api' . mt_rand() . time() . '.' . $pictype;
        file_put_contents($url, $data);

        $user = Auth::user();

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
            'url' => $url
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo created successfully',
            'todo' => $todo,
        ]);
    }

    public function show($id)
    {
        $todo = Todo::find($id);
        return response()->json([
            'status' => 'success',
            'todo' => $todo,
        ]);
    }

    public function todo_by_user_id(Request $request)
    {
        $todo = Todo::where('user_id',$request->user_id)->get();
        return response()->json([
            'status' => 'success',
            'todo' => $todo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 404);
        }

        list($type, $data) = explode(';', $request->url);
        list($extra, $pictype) = explode('/', $type);
        list(, $data)  = explode(',', $data);
        $data = base64_decode($data);
        $url = '..\app\upload\api' . mt_rand() . time() . '.' . $pictype;
        file_put_contents($url, $data);

        $todo = Todo::find($id);

        $user = Auth::user();

        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->url = $url;
        $todo->user_id = $user->id;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
