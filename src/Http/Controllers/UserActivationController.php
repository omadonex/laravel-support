<?php

namespace Omadonex\LaravelSupport\Http\Controllers;

use App\Http\Controllers\Controller;
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

            if (!$user->isActivated()) {
                $user->activate($request->all());
            }

            $userActivation->delete();

            if (!auth()->check()) {
                Auth::login($user);
            }

            UtilsApp::addLiveNotify(trans('support::auth.activated'));

            return UtilsResponseJson::okResponse([
                'redirectUrl' => route('content.lesson.index'),
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
        if (!auth()->check() || auth()->user()->isActivated() || !auth()->user()->userActivation) {
            return UtilsResponseJson::errorResponse([
                'message' => 'error',
            ]);
        }

        event(new UserActivationResendEvent(auth()->user()));

        return UtilsResponseJson::okResponse([
            'message' => 'ok',
        ]);
    }
}
