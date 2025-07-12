<?php

namespace App\Http\Controllers\V1;

use App\Classes\Helpers;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserMinlistResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FileService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{

    private $userService;

    public function __construct()
    {
        Gate::authorize('user_access');
        $this->userService = new UserService();
    }

    public function index(Request $request)
    {
        Gate::authorize('user_show');

        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort,$request->sort_type,User::$sortable);

        $users = User::with('role')
//            ->where('type',UserType::EMPLOYEE)
            ->filter($request->only(['name', 'keyword', 'role_id','type','phone'])) // Apply filters scope
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

        $validUserFields = $this->userService->filterValidData($request);

        $user = User::create($validUserFields);

        foreach ($validUserFields['address_list'] as $address) {
            $user->addresses()->create($address); // Add new addresses
        }

        if ($request->input('avatar')){
            try {
                $fileData = $fileService->storeTmpFile($user,$request->input('avatar'),'avatar');
                $user->update(['avatar_id' => $fileData['id']]);
            }catch (\Exception $exception){
                //skip
            }
        }

        foreach ($request->input('files', []) as $file) {
            try {
                $fileService->storeTmpFile($user,$file,'files');
            }catch (\Exception $exception){
                //skip
            }
        }

        return response()->json(['id' => $user->id]);
    }

    public function show(User $user)
    {
        Gate::authorize('user_show',$user);

        $user->load('addresses');

        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validUserFields = $this->userService->filterValidData($request);

        $user->update($validUserFields);

        $user->addresses()->delete(); // Remove old addresses

        foreach ($validUserFields['address_list'] as $address) {
            if ($address['street']){
                $user->addresses()->create($address); // Add new addresses
            }
        }

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
            'file_id' => 'required|numeric',
        ]);

        $tmpFileId = $request->input('file_id');

        $fileData = $fileService->storeTmpFile($user,$tmpFileId,$request->type);

        if (!$fileData){
            return response()->json(['message' => 'File not saved. Please try again.'],402);
        }

        if($request->type === 'avatar'){
            $user->update(['avatar_id' => $fileData['id']]);
        }

        return response()->json($fileData);
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
