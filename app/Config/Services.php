<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use Myth\Auth\Config\Auth as AuthConfig;
use App\Models\LoginModel;
use App\Authentication\LocalAuthenticator;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * Override authentication service untuk menggunakan custom LocalAuthenticator
     * yang support user_agent
     */
    public static function authentication(string $lib = 'local', ?\CodeIgniter\Model $userModel = null, ?\CodeIgniter\Model $loginModel = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authentication', $lib, $userModel, $loginModel);
        }

        // Use custom LoginModel yang support user_agent
        $loginModel ??= model(LoginModel::class);

        // Use default UserModel dari Myth Auth
        $userModel ??= model(\Myth\Auth\Models\UserModel::class);

        /** @var AuthConfig $config */
        $config = config('Auth');

        // Use custom LocalAuthenticator untuk lib 'local'
        if ($lib === 'local') {
            $instance = new LocalAuthenticator($config);
        } else {
            // Fallback ke default class untuk lib lain
            $class = $config->authenticationLibs[$lib] ?? \Myth\Auth\Authentication\LocalAuthenticator::class;
            $instance = new $class($config);
        }

        return $instance
            ->setUserModel($userModel)
            ->setLoginModel($loginModel);
    }
}
