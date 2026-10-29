<?php

declare(strict_types=1);

use App\Settings\AuthenticationSettings;
use App\Settings\GeneralSettings;
use App\Settings\MailSettings;

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Groups
    |--------------------------------------------------------------------------
    |
    | Each group is a class extending App\Settings\Settings. Listed groups can
    | be injected anywhere, and the config values they override are applied
    | when the application boots. See app/Settings/README.md for details.
    |
    */

    'groups' => [
        GeneralSettings::class,
        AuthenticationSettings::class,
        MailSettings::class,
    ],

];
