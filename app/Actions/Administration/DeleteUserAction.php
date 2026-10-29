<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\Actions\Account\DeleteAvatarAction;
use App\Models\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\ConnectionResolverInterface;

final readonly class DeleteUserAction
{
    public function __construct(
        private DeleteAvatarAction $deleteAvatar,
        private Config $config,
        private ConnectionResolverInterface $database,
    ) {}

    /**
     * Delete the user, their profile picture and their browser sessions.
     */
    public function handle(User $user): void
    {
        $this->deleteAvatar->handle($user);

        if ($this->config->get('session.driver') === 'database') {
            /** @var string|null $connection */
            $connection = $this->config->get('session.connection');
            /** @var string $table */
            $table = $this->config->get('session.table', 'sessions');

            $this->database->connection($connection)->table($table)
                ->where('user_id', $user->getAuthIdentifier())
                ->delete();
        }

        $user->delete();
    }
}
