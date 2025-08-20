<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\FileService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UpdateUserRequest;

class ProfileController extends Controller
{

    public function index(){
        $user = Auth::user();

        $user->load('addresses');

        return response()->json(new UserResource($user));
    }

    public function update(UpdateProfileRequest $request,UserService $userService)
    {
        $user = Auth::user();

        $user->update($request->validated());

        return response()->json(['id' => $user->id]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['id' => $user->id]);
    }

    public function destroy()
    {
        $user = Auth::user();

        if ($user->avatar){
            //delete avatar from storage
        }

        $user->delete();

        return response()->noContent();
    }


    public function fileupload(Request $request,FileService $fileService){

        $user = Auth::user();

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

    public function filedelete(Request $request,FileService $fileService){

        $user = Auth::user();

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
