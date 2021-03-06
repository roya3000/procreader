<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ApiPostsController extends Controller
{

    use GetAuthenticatedUser;
    use TEntityNotFound;

    private function returnPostWithLinkedEntity($post)
    {
        return Post::with('user', 'category')->where('id', $post->id)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @internal param int $limit
     */
    public function index(Request $request)
    {

        $posts = Post::with('user', 'category')
            ->limit($request->get('limit'))
            ->get();

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest|Request $request
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function store(PostRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $this->getAuthenticatedUser()['id'];
        $post = Post::create($input);

        return $this->returnPostWithLinkedEntity($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);

            return $post;
        } catch (ModelNotFoundException $e) {
            return $this->NotFoundResponse();
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest|Request $request
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function update(PostRequest $request, $id)
    {

        try {
            $input = $request->all();
            $post = Post::findOrfail($id);

            $input['user_id'] = $this->getAuthenticatedUser()['id'];

            $post->update($input);

            return $this->returnPostWithLinkedEntity($post);

        } catch (ModelNotFoundException $e) {
            return $this->NotFoundResponse();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();
        } catch (ModelNotFoundException $e) {
            return $this->NotFoundResponse();
        }
    }
}
