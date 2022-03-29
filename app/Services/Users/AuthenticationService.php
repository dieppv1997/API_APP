<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Enums\Users\RegisterTypeEnum;
use App\Enums\Users\UserBannedEnum;
use App\Enums\Users\UserStatusEnum;
use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\FCMTokenRepositoryInterface;
use App\Interfaces\Services\Users\AuthenticationServiceInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Repositories\VerifyUserRepositoryInterface;

use App\Mail\RegisterEmail;
use App\Services\BaseService;
use App\Traits\HandleExceptionTrait;
use App\Traits\UserTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthenticationService extends BaseService implements AuthenticationServiceInterface
{
    use HandleExceptionTrait, UserTrait;

    public $verifyUserRepository;
    public $FCMTokenRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $repository
     * @param VerifyUserRepositoryInterface $verifyUserRepository
     * @param FCMTokenRepositoryInterface $FCMTokenRepository
     */
    public function __construct(
        UserRepositoryInterface       $repository,
        VerifyUserRepositoryInterface $verifyUserRepository,
        FCMTokenRepositoryInterface $FCMTokenRepository
    ) {
        $this->repository = $repository;
        $this->verifyUserRepository = $verifyUserRepository;
        $this->FCMTokenRepository = $FCMTokenRepository;
    }

    public function registerByNickname($params): array
    {
        $user = $this->repository->create([
            'nickname' => $params['nickname'],
            'province_id' => $params['province_id'],
            'device_id' => $params['device_id'],
            'register_type' => RegisterTypeEnum::REGISTER_NICKNAME,
            'accept_rule_at' =>  Carbon::now(),
        ]);

        $this->generateUserSetting($user->id);

        return [
            'message' => trans('messages.auth.registerByNicknameSuccess'),
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'data' => $user,
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function login($params): array
    {
        $credentials = [
            'email'=> trim($params['email']),
            'password'=> trim($params['password']),
        ];
        if (!Auth::attempt($credentials)) {
            throw new BadRequestException(trans('messages.auth.loginFailed'));
        }
        $user = $this->findWhere(['email' => trim($params['email'])])->firstOrFail();

        if ($user->activated == UserStatusEnum::INACTIVE) {
            throw new BadRequestException(trans('messages.auth.accountInactive'));
        }
        if ($user->email_verified_at == null) {
            throw new BadRequestException(trans('messages.auth.accountNotVerify'));
        }
        return [
            'message' => trans('messages.auth.loginSuccess'),
            'access_token' => $user->createToken('authToken')->plainTextToken,
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function logout($params): array
    {
        auth()->user()->currentAccessToken()->delete();
        if (!empty($params['fcm_token'])) {
            $currentUserId = Auth::id();
            $this->FCMTokenRepository->deleteWhere([
                'user_id' => $currentUserId,
                'fcm_token' => $params['fcm_token']
            ]);
        }
        return [
            'message' => trans('messages.auth.loggedOut')
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function registerEmail($params): array
    {
        $existUser = $this->findWhere(['email' => $params['email']])->first();
        if (!empty($existUser)) {
            if (!empty($existUser->email_verified_at)) {
                throw new BadRequestException(trans('messages.auth.emailInUsed'));
            } else {
                $existUser->forceDelete();
                $this->verifyUserRepository->deleteWhere(['user_id' => $existUser->id]);
            }
        }

        $newUser = $this->create([
            'email' => $params['email'],
            'password' => bcrypt($params['password']),
            'nickname' => $params['nickname'],
            'province_id' => $params['province_id'],
            'register_type' => RegisterTypeEnum::REGISTER_EMAIL,
            'accept_rule_at' =>  Carbon::now(),
        ]);

        $rand = random_int(0, 999999);
        $verifyCode = str_pad($rand, 6, "0", STR_PAD_LEFT);
        $this->verifyUserRepository->create([
            'user_id' => $newUser->id,
            'verify_code' => $verifyCode
        ]);
        $mailData = array(
            'subject' => '【ハナノヒBe】新規登録（ご本人様確認）',
            'verify_code' => $verifyCode
        );
        try {
            Mail::to($newUser)->send(new RegisterEmail($mailData));
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        return [
            'message' => trans('messages.auth.registerEmailSuccessful'),
            'user_id' => $newUser->id,
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     * @throws Exception
     */
    public function registerEmailNickname($params): array
    {
        $currentUser = Auth::user();
        if (!empty($currentUser->register_type != RegisterTypeEnum::REGISTER_NICKNAME)) {
            throw new BadRequestException(trans('messages.auth.invalidRegisterType'));
        }
        if (!empty($currentUser->email_verified_at)) {
            throw new BadRequestException(trans('messages.auth.userAlreadyVerified'));
        }

        $existUser = $this->findWhere(['email' => $params['email']])->first();
        if (!empty($existUser)) {
            if (!empty($existUser->email_verified_at)) {
                throw new BadRequestException(trans('messages.auth.emailInUsed'));
            } else {
                $existUser->forceDelete();
                $this->verifyUserRepository->deleteWhere(['user_id' => $existUser->id]);
            }
        }

        $this->verifyUserRepository->deleteWhere(['user_id' => $currentUser->id]);
        $rand = random_int(0, 999999);
        $verifyCode = str_pad($rand, 6, "0", STR_PAD_LEFT);
        $verifyUser = $this->verifyUserRepository->create([
            'user_id' => $currentUser->id,
            'verify_code' => $verifyCode,
            'email' => $params['email'],
        ]);

        $updateData = [
            'password' => bcrypt($params['password'])
        ];
        $this->update($updateData, $currentUser->id);

        $mailData = array(
            'subject' => '【ハナノヒBe】新規登録（ご本人様確認）',
            'verify_code' => $verifyCode
        );
        try {
            Mail::to($verifyUser)->send(new RegisterEmail($mailData));
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        return [
            'message' => trans('messages.auth.registerEmailSuccessful')
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function verifyEmail($params): array
    {
        $verifyUser = $this->verifyUserRepository->findWhere([
            'user_id' => $params['user_id'],
            'verify_code' => $params['verify_code']
        ])->first();
        if (empty($verifyUser)) {
            throw new BadRequestException(trans('messages.auth.invalidVerifyCode'));
        }

        $user = $verifyUser->user;
        $currentTime = Carbon::now();
        if ($currentTime > $verifyUser->created_at->addMinutes(config('auth.verify.token.expire'))) {
            $this->verifyUserRepository->delete($verifyUser->id);
            if (!$user->email_verified_at) {
                $user->forceDelete();
            }
            if (DB::transactionLevel()) {
                DB::commit();
            }
            throw new BadRequestException(trans('messages.auth.invalidVerifyCode'));
        } else {
            if (!$user->email_verified_at) {
                $updateData = [
                    'email_verified_at' => $currentTime,
                    'activated' => UserStatusEnum::ACTIVE
                ];
                $this->update($updateData, $user->id);

                $this->generateUserSetting($user->id);

                $this->verifyUserRepository->deleteWhere(['verify_code' => $params['verify_code']]);
                return [
                    'message' => trans('messages.auth.verifySuccessful'),
                    'access_token' => $user->createToken('auth_token')->plainTextToken,
                    'token_type' => 'Bearer',
                    'data' => $user,
                ];
            } else {
                throw new BadRequestException(trans('messages.auth.userAlreadyVerified'));
            }
        }
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function verifyEmailNickname($params): array
    {
        $currentUser = Auth::user();
        if ($currentUser->register_type != RegisterTypeEnum::REGISTER_NICKNAME) {
            throw new BadRequestException();
        }
        $verifyUser = $this->verifyUserRepository->findWhere([
            'user_id' => $currentUser->id,
            'verify_code' => $params['verify_code']
        ])->first();
        if (empty($verifyUser)) {
            throw new BadRequestException(trans('messages.auth.invalidVerifyCode'));
        }

        $currentTime = Carbon::now();
        if ($currentTime > $verifyUser->created_at->addMinutes(config('auth.verify.token.expire'))) {
            $this->verifyUserRepository->delete($verifyUser->id);
            if (DB::transactionLevel()) {
                DB::commit();
            }
            throw new BadRequestException(trans('messages.auth.expireVerifyCode'));
        } else {
            if (!$currentUser->email_verified_at) {
                $updateData = [
                    'email' => $verifyUser->email,
                    'email_verified_at' => $currentTime,
                    'activated' => UserStatusEnum::ACTIVE
                ];
                $this->update($updateData, $currentUser->id);
                $this->verifyUserRepository->deleteWhere(['verify_code' => $params['verify_code']]);
                return [
                    'message' => trans('messages.auth.verifySuccessful'),
                ];
            } else {
                throw new BadRequestException(trans('messages.auth.userAlreadyVerified'));
            }
        }
    }
}
