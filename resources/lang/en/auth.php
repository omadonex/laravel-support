<?php

return [
    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'activate' => 'You need to activate your account. We have sent you an activation link, please check your email.',
    'activateInfo' => 'If you dont receive e-mail yet, click on the next button',
    'activationResendError' => 'Resending e-mail custom error.',
    'activationResendTime' => 'Next e-mail can be sent after :seconds sec.',
    'activationResendSuccess' => 'Activation e-mail has been successfully sent.',
    'activationToken' => 'Activation token is not correct. Please check your link.',
    'activated' => 'Your account has been successfully activated',

    'forms' => [
        'login' => [
            'title' => 'Sign in',
            'fields' => [
                'login' => 'Username / E-mail',
                'password' => 'Password',
                'remember' => 'Remember me',
            ],
            'buttons' => [
                'submit' => 'Login',
                'forgot' => 'Forgot password?',
            ],
        ],
        'register' => [
            'title' => 'Registration',
            'fields' => [
                'username' => 'Username',
                'email' => 'E-mail',
                'password' => 'Password',
                'password_confirmation' => 'Password confirmation',
            ],
            'buttons' => [
                'submit' => 'Register',
            ],
        ],
        'activation' => [
            'title' => 'Account activation',
            'buttons' => [
                'submit' => 'Activate',
            ],
        ],
        'passRequest' => [
            'title' => 'Reset password',
            'fields' => [
                'email' => 'E-mail',
            ],
            'buttons' => [
                'submit' => 'Send password reset link',
            ],
        ],
        'passReset' => [
            'title' => 'Reset password',
            'fields' => [
                'email' => 'E-mail',
                'password' => 'New password',
                'password_confirmation' => 'New password confirmation',
            ],
            'buttons' => [
                'submit' => 'Reset password',
            ],
        ],
    ],
];
