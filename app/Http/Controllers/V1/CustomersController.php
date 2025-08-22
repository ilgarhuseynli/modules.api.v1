<?php

namespace App\Http\Controllers\V1;

use App\Classes\Helpers;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\UserMinlistResource;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomersController extends Controller
{

    public function index(Request $request)
    {
        Gate::authorize('customer_show');

        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort,$request->sort_type,User::$sortable);

        $users = User::with('role')
            ->where('type',UserType::CUSTOMER)
            ->filter($request->only(['name', 'keyword', 'role_id','type','phone'])) // Apply filters scope
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return CustomerResource::collection($users);
    }

    public function minlist(Request $request)
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort,$request->sort_type,User::$sortable);

        $users = User::query()
            ->where('type',UserType::CUSTOMER)
            ->filter($request->only(['name', 'keyword', 'role'])) // Apply filters scope
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return UserMinlistResource::collection($users);
    }

    public function store(StoreCustomerRequest $request,FileService $fileService,CustomerService $customerService)
    {

        $validUserFields = $customerService->filterValidData($request);

        $customer = User::create($validUserFields);

        foreach ($validUserFields['address_list'] as $address) {
            $customer->addresses()->create($address); // Add new addresses
        }

        if ($request->input('avatar')){
            try {
                $fileData = $fileService->storeTmpFile($customer,$request->input('avatar'),'avatar');
                $customer->update(['avatar_id' => $fileData['id']]);
            }catch (\Exception $exception){
                //skip
            }
        }

        foreach ($request->input('files', []) as $file) {
            try {
                $fileService->storeTmpFile($customer,$file,'files');
            }catch (\Exception $exception){
                //skip
            }
        }

        return response()->json(['id' => $customer->id]);
    }

    public function show(User $customer)
    {
        Gate::authorize('customer_show',$customer);

        $customer->load('addresses');

        return response()->json(new CustomerResource($customer));
    }

    public function update(UpdateCustomerRequest $request, User $customer,CustomerService $customerService)
    {
        $validUserFields = $customerService->filterValidData($request);

        $customer->update($validUserFields);

        $customer->addresses()->delete(); // Remove old addresses

        foreach ($validUserFields['address_list'] as $address) {
            if ($address['street']){
                $customer->addresses()->create($address); // Add new addresses
            }
        }

        return response()->json(['id' => $customer->id]);
    }

    public function updatePassword(Request $request,User $customer)
    {
        Gate::authorize('customer_edit',$customer);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $customer->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['id' => $customer->id]);
    }

    public function destroy(User $customer)
    {
        Gate::authorize('customer_delete',$customer);

        if ($customer->avatar){
            //delete avatar from storage
        }

        $customer->delete();

        return response()->noContent();
    }


    public function fileupload(Request $request,User $customer,FileService $fileService){

        Gate::authorize('customer_edit',$customer);

        $request->validate([
            'type' => 'required|in:avatar,files',
            'file_id' => 'required|numeric',
        ]);

        $tmpFileId = $request->input('file_id');

        $fileData = $fileService->storeTmpFile($customer,$tmpFileId,$request->type);

        if (!$fileData){
            return response()->json(['message' => 'File not saved. Please try again.'],402);
        }

        if($request->type === 'avatar'){
            $customer->update(['avatar_id' => $fileData['id']]);
        }

        return response()->json($fileData);
    }

    public function filedelete(Request $request,User $customer,FileService $fileService){
        Gate::authorize('customer_edit',$customer);

        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);


        if ($customer->avatar_id == $request->file_id){
            $fileService->deleteFile($customer->avatar);

        }else{
            $fileData = $customer->files()->where('id',$request->file_id)->first();

            $fileService->deleteFile($fileData);
        }

        return response()->noContent();
    }

}
