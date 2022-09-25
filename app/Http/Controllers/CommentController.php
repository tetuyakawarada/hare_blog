<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Post $post)
    {
        return view('comments.create', compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @param  \App\Models\Post  $post

     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, Post $post)
    {
        $comment = new Comment($request->all());
        $comment->user_id = $request->user()->id;

        //トランザクションの開始
        // DB::beginTransaction();
        try {
            // 登録
            // $comment->save();
            $post->comments()->save($comment);
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollback();
            return back()->withInput()->withErrors($th->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', 'コメントを登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, Comment $comment)

    {
        return view('comments.edit', compact('post', 'comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @param  \App\Models\Post  $post

     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Post $post, Comment $comment)
    {
        if ($request->user()->cannot('update', $comment)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分のコメント以外は更新できません');
        }

        $comment->fill($request->all());

        //トランザクションの開始
        // DB::beginTransaction();
        try {
            // 登録
            $comment->save();
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->withInput()->withErrors($th->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', 'コメントを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post, Comment $comment)
    {
        if ($request->user()->cannot('delete', $comment)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分のコメント以外は削除できません');
        }

        //トランザクションの開始
        // DB::beginTransaction();
        try {
            // 登録
            $comment->delete();
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            return back()->withInput()->withErrors($th->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', 'コメントを削除しました');
    }
}
