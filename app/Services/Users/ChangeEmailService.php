<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Users;

use App\Exceptions\BadRequestException;
use App\Interfaces\Repositories\ChangeEmailRequestRepositoryInterface;
use App\Interfaces\Repositories\VerifyUserRepositoryInterface;
use App\Interfaces\Services\Users\ChangeEmailServiceInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;

use App\Mail\ChangeEmailEmail;
use App\Services\BaseService;
use App\Traits\HandleExceptionTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ChangeEmailService extends BaseService implements ChangeEmailServiceInterface
{
    use HandleExceptionTrait;

    public $changeEmailRequestRepository;
    public $verifyUserRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $repository
     * @param ChangeEmailRequestRepositoryInterface $changeEmailRequestRepository
     */
    public function __construct(
        UserRepositoryInterface       $repository,
        ChangeEmailRequestRepositoryInterface $changeEmailRequestRepository,
        VerifyUserRepositoryInterface $verifyUserRepository
    ) {
        $this->repository = $repository;
        $this->changeEmailRequestRepository = $changeEmailRequestRepository;
        $this->verifyUserRepository = $verifyUserRepository;
    }



    public function requestChangeEmail($params)
    {
        $newEmail = $params['email'];
        $checkEmailInUsed = $this->findWhere([
            'email' => $newEmail,
            ['email_verified_at', '!=', null]
        ])->first();

        if ($checkEmailInUsed) {
            throw new BadRequestException(trans('messages.auth.emailInUsed'));
        }

        $rand = random_int(0, 999999);
        $verifyCode = str_pad($rand, 6, "0", STR_PAD_LEFT);
        $this->changeEmailRequestRepository->deleteWhere([
            'user_id' => Auth::id()
        ]);

        $this->changeEmailRequestRepository->create([
            'user_id' => Auth::id(),
            'verify_code' => Hash::make($verifyCode),
            'email' => $newEmail,
        ]);
        $mailData = array(
            'subject' => '【ハナノヒ Be】メールアドレス変更（ご本人様確認）',
            'verify_code' => $verifyCode
        );
        try {
            Mail::to($newEmail)->send(new ChangeEmailEmail($mailData));
        } catch (\Exception $e) {
            $this->handleException($e);
        }
        return [
            'message' => trans('messages.user.sentChangeMailSuccess')
        ];
    }

    public function verifyChangeEmail($params)
    {
        $verifyChangeEmail = $this->changeEmailRequestRepository->findWhere([
            'user_id' => Auth::id(),
            'email' => $params['email']
        ])->first();

        $existUser = $this->findWhere(['email' => $params['email']])->first();
        if (!empty($existUser)) {
            if (!empty($existUser->email_verified_at)) {
                throw new BadRequestException(trans('messages.auth.emailInUsed'));
            } else {
                $existUser->forceDelete();
                $this->verifyUserRepository->deleteWhere(['user_id' => $existUser->id]);
            }
        }
        if (empty($verifyChangeEmail) || !Hash::check($params['verify_code'], $verifyChangeEmail->verify_code)) {
            throw new BadRequestException(trans('messages.auth.invalidVerifyCode'));
        }
        $timeExpired = config('auth.verify.token.expire');

        if (Carbon::parse($verifyChangeEmail->created_at)->addMinutes($timeExpired)->isPast()) {
            throw new BadRequestException(trans('messages.auth.expireVerifyCode'));
        }

        $this->repository->update([
            'email' => $params['email']
        ], Auth::id());

        $this->changeEmailRequestRepository->deleteWhere([
            'user_id' => Auth::id(),
            'email' => $params['email']
        ]);
        return [
            'message' => trans('messages.user.changeMailSuccessfully')
        ];
    }
}
