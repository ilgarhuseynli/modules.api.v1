<?php

namespace Modules\Blog\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;
use Modules\Blog\Models\Post;
use Modules\Blog\Requests\StorePostRequest;
use Modules\Blog\Requests\UpdatePostRequest;
use Modules\Blog\Resources\PostResource;
use Modules\Blog\Services\PostService;

class PostController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Post::$sortable);

        $posts = Post::with('translations', 'categories.translations', 'author', 'cover_image', 'images')
            ->filter($request->only(['title', 'status', 'is_featured', 'category_id', 'author_id', 'locale']))
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request, PostService $postService): mixed
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        $post = $postService->create($data);

        return response()->json(['id' => $post->id]);
    }

    public function show(Post $post): mixed
    {
        $post->load('translations', 'categories.translations', 'author', 'cover_image', 'images');

        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, Post $post, PostService $postService): mixed
    {
        $postService->update($post, $request->validated());

        return response()->json(['id' => $post->id]);
    }

    public function destroy(Post $post): mixed
    {
        $post->delete();

        return response()->noContent();
    }

    public function fileupload(Request $request, Post $post, FileService $fileService): mixed
    {
        $request->validate([
            'type' => 'required|in:cover_image,images',
            'file_id' => 'required|numeric',
        ]);

        $fileData = $fileService->storeTmpFile($post, $request->input('file_id'), $request->type);

        if (! $fileData) {
            return response()->json(['message' => 'File not saved. Please try again.'], 402);
        }

        if ($request->type === 'cover_image') {
            $post->update(['cover_image_id' => $fileData['id']]);
        }

        return response()->json($fileData);
    }

    public function filedelete(Request $request, Post $post, FileService $fileService): mixed
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        if ($post->cover_image_id == $request->file_id) {
            $fileService->deleteFile($post->cover_image);
        } else {
            $fileData = $post->images()->where('id', $request->file_id)->first();
            $fileService->deleteFile($fileData);
        }

        return response()->noContent();
    }
}
