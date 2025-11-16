<?php

namespace App\Controllers;

use Myth\Auth\Controllers\AuthController as MythAuthController;

/**
 * Custom AuthController yang extend dari vendor AuthController
 * Override attemptLogin() untuk menambahkan custom logic setelah login
 */
class AuthController extends MythAuthController
{
    /**
     * Override attemptLogin untuk menambahkan custom session setup
     */
    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = strtolower($this->request->getPost('login'));
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (! $this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)->withCookies();
        }

        // Custom logic: Setup session data setelah login berhasil
        // Cek apakah ada custom handler di App\Controllers\Auth
        try {
            if (class_exists('\App\Controllers\Auth')) {
                $authHandler = new \App\Controllers\Auth();
                if (method_exists($authHandler, 'setupPostLoginSession')) {
                    $redirectURL = $authHandler->setupPostLoginSession();
                    return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error, lanjutkan dengan default behavior
            log_message('debug', 'Custom post-login handler tidak tersedia: ' . $e->getMessage());
        }

        // Default behavior jika custom handler tidak ada
        $redirectURL = session('redirect_url') ?? site_url('/');
        unset($_SESSION['redirect_url']);

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
    }
}

