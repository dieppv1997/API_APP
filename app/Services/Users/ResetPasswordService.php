<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Enums\Users\UserStatusEnum;
use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\PasswordResetRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Users\ResetPasswordServiceInterface;
use App\Mail\ResetPasswordEmail;
use App\Services\BaseService;
use App\Traits\HandleExceptionTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class ResetPasswordService extends BaseService implements ResetPasswordServiceInterface
{
    use HandleExceptionTrait;

    public $userRepository;

    /**
     * @param PasswordResetRepositoryInterface $repository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        PasswordResetRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function requestResetPassword($params): array
    {
        $user = $this->userRepository->findWhere(['email' => $params['email']])->first();
        if (!$user || ($user->activated != UserStatusEnum::ACTIVE)) {
            return [
                'message' => trans('messages.user.sentResetPasswordMail')
            ];
        }
        $this->deleteWhere(['email' => $params['email']]);
        $token = $this->generateResetPasswordToken();
        $this->create([
            'email' => $user->email,
            'token' => Hash::make($token),
        ]);

        $webUrl = config('settings.web_url');
        $emailEncode = urlencode($user->email);
        $urlEncode = "{$webUrl}/launch-app?type=reset-password&token={$token}&email={$emailEncode}";
        $urlPlaintext = "{$webUrl}/launch-app?type=reset-password&token={$token}&email={$user->email}";

        $mailData = array(
            'subject' => '【ハナノヒBe】パスワード再設定',
            'nickname' => $user->nickname,
            'urlEncode' => $urlEncode,
            'urlPlaintext' => $urlPlaintext,
        );
        try {
            Mail::to($user)->send(new ResetPasswordEmail($mailData));
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        return [
            'message' => trans('messages.user.sentResetPasswordMail')
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws BadRequestException
     */
    public function resetPassword($params): array
    {
        $user = $this->userRepository->findWhere(['email' => $params['email']])->first();
        if (!$user) {
            throw new BadRequestException(trans('messages.user.emailDoesNotExist'));
        }
        $record = $this->findWhere([
            'email' => $params['email']
        ])->first();
        if (!$record) {
            throw new BadRequestException(trans('messages.user.tokenInvalid'));
        }
        if (!Hash::check($params['token'], $record->token)) {
            throw new BadRequestException(trans('messages.user.tokenInvalid'));
        }
        $timeExpired = config('auth.passwords.timeExpiredToken');
        if (Carbon::parse($record->created_at)->addMinutes($timeExpired)->isPast()) {
            throw new BadRequestException(trans('messages.user.tokenInvalid'));
        }
        $this->userRepository->update([
            'password' => Hash::make($params['password'])
        ], $user->id);
        $this->deleteWhere(['email' => $params['email']]);
        return [
            'message' => trans('messages.user.passwordResetSuccessful')
        ];
    }

    public function generateResetPasswordToken()
    {
        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return  hash_hmac('sha256', Str::random(40), $key);
    }
}
