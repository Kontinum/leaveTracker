<?php

namespace App\Http\Controllers;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use App\Exceptions\ApiResponseException;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\LoginUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Services\UserTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    use ApiResponse;
    public function __construct(
        private readonly UsersRepository $usersRepository
    )
    {
    }

    /**
     * @param LoginUserRequest $loginUserRequest
     * @param UserTokenService $userTokenService
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function login(LoginUserRequest $loginUserRequest, UserTokenService $userTokenService): JsonResponse
    {
        try {
            $loginData = $loginUserRequest->validated();
            $user = $this->usersRepository->get(['email' => $loginData['email']]);
            if (!$user) {
                return $this->sendResponse('User does not exist.', HttpStatus::NOT_FOUND->value, 'error');
            }
            if (!Hash::check($loginData['password'],  $user->password)) {
                return $this->sendResponse('Password does not match.', HttpStatus::UNAUTHORIZED->value, 'error');
            }
            $token = $userTokenService->createToken($user);

            return $this->sendResponse($token);
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @param CreateUserRequest $createUserRequest
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function store(CreateUserRequest $createUserRequest): JsonResponse
    {
        try {
            $createData = $createUserRequest->validated();
            $createData['password'] = Hash::make($createData['password']);

            $newUser = $this->usersRepository->create($createData);

            return $this->sendResponse(new UserResource($newUser));
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @param UpdateUserRequest $updateUserRequest
     * @param User $user
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function update(UpdateUserRequest $updateUserRequest, User $user): JsonResponse
    {
        try {
            $updateData = $updateUserRequest->validated();
            if (isset($updateData['password'])) {
                $updateData['password'] = Hash::make($updateData['password']);
            }

            $updatedUser = $this->usersRepository->update($user, $updateData);

            return $this->sendResponse(new UserResource($updatedUser));

        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            return $this->sendResponse(data: 'User has been deleted', wrapKey: 'message');
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }
}
