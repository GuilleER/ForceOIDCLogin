<?php
namespace Piwik\Plugins\ForceOIDCLogin;

use Piwik\Piwik;
use Piwik\SettingsPiwik;
use Piwik\Url;

class ForceOIDCLogin extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'Request.dispatch' => 'forceOidcEverywhere',
        ];
    }

    public function forceOidcEverywhere()
    {
        $isLoggedIn = Piwik::getCurrentUserLogin() !== 'anonymous';
        $module = Piwik::getModule();
        $action = Piwik::getAction();

        // Detect if we're already in the OIDC flow to avoid redirect loops
        $isOidcFlow = ($module === 'LoginOIDC');

        if (!$isLoggedIn && !$isOidcFlow && empty($_GET['normal'])) {
            // Correct URL to start the OIDC login flow with the provider parameter
            $oidcUrl = SettingsPiwik::getPiwikUrl() . '?module=LoginOIDC&action=signIn&provider=oidc';

            // Prevent redirect loops
            if (Url::getCurrentUrl() !== $oidcUrl) {
                header("Location: $oidcUrl");
                exit;
            }
        }
    }
}
