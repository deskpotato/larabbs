<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request)
	{
		$topics = Topic::withOrder($request->order)->paginate(20);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	//创建帖子
	public function create(Topic $topic)
	{
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
		// $topic = Topic::create($request->all());
		// return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();
		return redirect()->route('topics.show', $topic->id)->with('success', '帖子创建成功');

	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}

	//上传图片
	public function uploadImage(Request $request)
	{
		$data = [
			'success'=>false,
			'msg'=>'上传失败!',
			'file_path'=>''
		];
		if($file = $request->upload_file){
			$result = ImageUploadHandler::save($request->upload_file,'topics',Auth::id(),1024);
			if ($result) {
				$data['file_path'] = $result['path'];
				$data['msg'] = "上传成功!";
				$data['success'] = true;
			}
		}
		return $data;

	}
}