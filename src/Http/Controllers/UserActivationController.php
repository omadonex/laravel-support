<?php

namespace Omadonex\LaravelSupport\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsApp;
use Omadonex\LaravelSupport\Classes\Utils\UtilsResponseJson;
use Omadonex\LaravelSupport\Events\UserActivationResendEvent;
use Omadonex\LaravelSupport\Http\Requests\UserActivateRequest;
use Omadonex\LaravelSupport\Models\UserActivation;

class UserActivationController extends Controller
{
    public function activation($token)
    {
        $userActivation = UserActivation::where('token', $token)->first();

        if (!$userActivation) {
            abort(404);
        }

        $user = $userActivation->user;
        if (!$user->isRandom()) {
            $user->activate($userActivation);

            if (!auth()->check()) {
                Auth::login($user);
            }

            UtilsApp::addLiveNotify(trans('support::auth.activated'));

            return redirect('/');
        }

        $data = [
            ConstantsCustom::MAIN_DATA_PAGE => [
                'token' => $token,
                'email' => $userActivation->user->email,
            ],
        ];

        return view('layouts.pages', $data);
    }

    public function activate(UserActivateRequest $request)
    {
        $userActivation = UserActivation::where('token', $request->token)->first();
        if ($userActivation) {
            $user = $userActivation->user;
            $activationData = $request->all();
            $activationData['password'] = bcrypt($activationData['password']);

            $user->activate($userActivation, $activationData);

            if (!auth()->check()) {
                Auth::login($user);
            }

            UtilsApp::addLiveNotify(trans('support::auth.activated'));

            return UtilsResponseJson::okResponse([
                ConstantsCustom::REDIRECT_URL => route('content.lesson.index'),
            ], true);
        }

        return UtilsResponseJson::validationResponse([
            'activationToken' => [
                trans('support::auth.activationToken'),
            ],
        ]);
    }

    public function resendActivation()
    {
        $user = auth()->user();
        $userActivation = auth()->check() ? $user->userActivation : null;
        if (!auth()->check() || $user->isActivated() || !$userActivation) {
            return UtilsResponseJson::errorResponse([
                ConstantsCustom::ERROR_MESSAGE => trans('support::auth.activationResendError'),
            ]);
        }

        $now = Carbon::now();
        if ($now->diffInMinutes($userActivation->sent_at) < ConstantsCustom::ACTIVATION_EMAIL_REPEAT_MINUTES) {
            $seconds = ConstantsCustom::ACTIVATION_EMAIL_REPEAT_MINUTES * 60 - $now->diffInSeconds($userActivation->sent_at);

            return UtilsResponseJson::errorResponse([
                ConstantsCustom::ERROR_MESSAGE => trans('support::auth.activationResendTime', ['seconds' => $seconds]),
            ]);
        }

        $userActivation->update(['sent_at' => $now]);

        event(new UserActivationResendEvent($user, $userActivation));

        return UtilsResponseJson::okResponse([
            'message' => trans('support::auth.activationResendSuccess'),
        ]);
    }
}
