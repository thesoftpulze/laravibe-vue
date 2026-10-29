<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Facades\Storage;

final readonly class DeleteUserAction
{
    public function __construct(
        private Config $config,
        private ConnectionResolverInterface $database,
    ) {}

    /**
     * Delete the user, their browser sessions and their profile picture.
     *
     * The picture is removed last, so a failed deletion leaves the user with it intact.
     */
    public function handle(User $user): void
    {
        $avatarPath = $user->avatar_path;

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

        if ($avatarPath !== null) {
            Storage::disk('public')->delete($avatarPath);
        }
    }
}
