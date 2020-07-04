<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;  //追加

class TasksController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     // get　でtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {   
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
        // 認証済みユーザを取得
        $user = \Auth::user();
        // タスク一覧を取得
        $tasks = \Auth::user()->tasks()->orderBy('created_at' , 'desc')->paginate(10);
        
        $data = [
            'user_id' => $user->id,
            'tasks' => $tasks,
            ];
        }
        // タスク一覧ビューでそれを表示
        return view('welcome', $data 
            );
            
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        if (\Auth::check()) { // 認証済みの場合
        
        $task = new Task;
        $task->user_id = \Auth::id();
        // タスク作成ビューを表示
        return view('tasks.create');
    }
    
    return redirect('/');
    
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10', 
            'content' => 'required|max:255',
            ]);
            
        
        // タスクを作成
            //public function show($id)

        $task = new Task;
        $task->user_id = \Auth::id();
        $task->status = $request->status; // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        if(\Auth::id() == $task->user_id){
        //$this->authorize('view', $task); // policy追加

    // if (\Auth::check()) { // 認証済みの場合
    
        
        // タスク詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
        
        }    
            
    return redirect('/');
    
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        // idの値でタスクを検索して取得
         $task = Task::findOrFail($id);

        //if (\Auth::check()) { // 認証済みの場合
        if(\Auth::id() == $task->user_id){

        // タスク編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }else{
    
    return redirect('/');
    
    }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        // バリデーション 
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
            ]);
            
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスクを更新
        $task->user_id =\Auth::id();
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでtask/idにアクセスされた場合の「削除処理」 
    public function destroy($id)
    {
       // idの値で投稿を検索して取得
        $task = \App\Task::findOrFail($id);
        if (\Auth::id() === $task->user_id) {
        // タスクを削除
        $task->delete();
        }
        // トップページへリダイレクトさせる
        return redirect('/'); 
    
}
}