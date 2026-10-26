<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\DTOs\Account\BrowserSessionDTO;
use App\Models\User;
use App\Support\UserAgent;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class ListBrowserSessionsAction
{
    public function __construct(
        private Config $config,
        private ConnectionResolverInterface $database,
    ) {}

    /**
     * List the user's active browser sessions, newest first. Only the database session driver stores them.
     *
     * @return Collection<int, BrowserSessionDTO>
     */
    public function handle(User $user, string $currentSessionId): Collection
    {
        if ($this->config->get('session.driver') !== 'database') {
            return new Collection();
        }

        /** @var string|null $connection */
        $connection = $this->config->get('session.connection');
        /** @var string $table */
        $table = $this->config->get('session.table', 'sessions');

        return $this->database->connection($connection)->table($table)
            ->where('user_id', $user->getAuthIdentifier())
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(function (object $session) use ($currentSessionId): BrowserSessionDTO {
                /** @var object{id: string, ip_address: ?string, user_agent: ?string, last_activity: int} $session */
                $userAgent = new UserAgent($session->user_agent ?? '');

                return new BrowserSessionDTO(
                    platform: $userAgent->platform(),
                    browser: $userAgent->browser(),
                    isDesktop: $userAgent->isDesktop(),
                    isCurrentDevice: $session->id === $currentSessionId,
                    lastActive: Date::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ipAddress: $session->ip_address,
                );
            })
            ->values();
    }
}
