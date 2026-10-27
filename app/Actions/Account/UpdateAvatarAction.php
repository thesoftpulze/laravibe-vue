<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final readonly class UpdateAvatarAction
{
    /**
     * Store a new profile picture for the user and remove the previous one.
     *
     * @throws RuntimeException When the image cannot be written to storage.
     */
    public function handle(User $user, UploadedFile $avatar): void
    {
        $previousPath = $user->avatar_path;

        $path = $avatar->store('avatars', 'public');

        if ($path === false) {
            throw new RuntimeException('Unable to store the profile picture.');
        }

        $user->avatar_path = $path;
        $user->save();

        if ($previousPath !== null) {
            Storage::disk('public')->delete($previousPath);
        }
    }
}
