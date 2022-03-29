<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\PasswordResetRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Users\UserPasswordServiceInterface;

use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserPasswordService extends BaseService implements UserPasswordServiceInterface
{
    protected $passwordResetRepository;

    /**
     * UserPasswordService constructor.
     * @param UserRepositoryInterface $repository
     * @param PasswordResetRepositoryInterface $passwordResetRepository
     */
    public function __construct(
        UserRepositoryInterface $repository,
        PasswordResetRepositoryInterface $passwordResetRepository
    ) {
        $this->repository = $repository;
        $this->passwordResetRepository = $passwordResetRepository;
    }
    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function changePassword($params): array
    {
        $currentPassword = trim($params['current_password']);
        $newPassword = trim($params['new_password']);
        if (!(Hash::check($currentPassword, Auth::user()->password))) {
            throw new BadRequestException(trans('messages.auth.currentPasswordFailed'));
        }
        $this->update(['password' => Hash::make($newPassword)], Auth::user()->id);
        return [
            'message' => trans('messages.auth.changePasswordSuccessfully'),
        ];
    }
}
