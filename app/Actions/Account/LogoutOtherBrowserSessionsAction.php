<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Facades\Auth;

final readonly class LogoutOtherBrowserSessionsAction
{
    public function __construct(
        private Config $config,
        private ConnectionResolverInterface $database,
    ) {}

    /**
     * Sign the user out everywhere except the current session.
     */
    public function handle(User $user, string $password, string $currentSessionId): void
    {
        Auth::logoutOtherDevices($password);

        if ($this->config->get('session.driver') !== 'database') {
            return;
        }

        /** @var string|null $connection */
        $connection = $this->config->get('session.connection');
        /** @var string $table */
        $table = $this->config->get('session.table', 'sessions');

        $this->database->connection($connection)->table($table)
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
