<?php

namespace App\Http\Controllers\V1;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserMinlistResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{

    public function __construct()
    {
        Gate::authorize('user_access');
    }

    public function index(Request $request)
    {
        Gate::authorize('user_show');

        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort,$request->sort_type,User::$sortable);

        $users = User::query()
            ->filter($request->only(['name', 'keyword', 'role'])) // Apply filters scope
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return UserResource::collection($users);
    }

    public function minlist(Request $request)
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort,$request->sort_type,User::$sortable);

        $users = User::query()
            ->filter($request->only(['name', 'keyword', 'role'])) // Apply filters scope
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return UserMinlistResource::collection($users);
    }

    public function store(StoreUserRequest $request,FileService $fileService)
    {
        $validUserFields = $request->validated();


        //TODO
        //when store phones pick the selected and store in phone field.
        //pick address from addresses (marked default);

        $user = User::create($validUserFields);

        if ($request->input('avatar')){
            $fileService->storeTmpFile($user,$request->input('cover'),'avatar');
        }

        foreach ($request->input('files', []) as $file) {
            $fileService->storeTmpFile($user,$file,'files');
        }

        return response()->json(['id' => $user->id]);
    }

    public function show(User $user)
    {
        Gate::authorize('user_show',$user);

        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validUserFields = $request->validated();

        $user->update($validUserFields);

        return response()->json(['id' => $user->id]);
    }

    public function updatePassword(Request $request,User $user)
    {
        Gate::authorize('user_edit',$user);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['id' => $user->id]);
    }

    public function destroy(User $user)
    {
        Gate::authorize('user_delete',$user);

        if ($user->avatar){
            //delete avatar from storage
        }

        $user->delete();

        return response()->noContent();
    }


    public function fileupload(Request $request,User $user,FileService $fileService){

        Gate::authorize('user_edit',$user);

        $request->validate([
            'type' => 'required|in:avatar,files',
            'file' => 'required|image|max:5048',
        ]);

        $file = $request->file('file');

        $fileData = $fileService->storeFile($user,$file,$request->type);

        if (!$fileData){
            return response()->json(['message' => 'File not saved. Please try again.'],402);
        }

        if($request->type === 'avatar'){
            $user->update(['avatar' => $fileData['id']]);
        }

        return response()->json([
            'id' => $fileData['id'],
            'url' => $fileData['url'],
        ]);
    }

    public function filedelete(Request $request,User $user,FileService $fileService){
        Gate::authorize('user_edit',$user);

        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);


        if ($user->avatar_id == $request->file_id){
            $fileService->deleteFile($user->avatar);

        }else{
            $fileData = $user->files()->where('id',$request->file_id)->first();

            $fileService->deleteFile($fileData);
        }

        return response()->noContent();
    }

}
