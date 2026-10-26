<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;
use TheSoftPulze\LaravibeStandards\Resources\AppResourceCollection;

it('resolves a resource to plain inertia props', function (): void {
    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $resource = UserResource::make($user);

    expect($resource->toInertia())
        ->toMatchArray([
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ])
        ->toHaveKey('email_verified_at', $user->email_verified_at?->toDateTimeString())
        ->not->toHaveKey('remember_token')
        ->not->toHaveKey('created_at')
        ->not->toHaveKey('updated_at');
});

it('supports alias prefix suffix transforms and timestamps', function (): void {
    $createdAt = CarbonImmutable::parse('2026-04-27 13:45:00');
    $updatedAt = CarbonImmutable::parse('2026-04-27 13:50:00');

    $resource = new class(new class() implements Arrayable
    {
        public int $id = 7;

        public string $name = 'alpha';

        public CarbonImmutable $created_at;

        public CarbonImmutable $updated_at;

        public function __construct()
        {
            $this->created_at = CarbonImmutable::parse('2026-04-27 13:45:00');
            $this->updated_at = CarbonImmutable::parse('2026-04-27 13:50:00');
        }

        /**
         * @return array<string, mixed>
         */
        public function toArray(): array
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }

        /**
         * @return array<string, mixed>
         */
        public function getAttributes(): array
        {
            return $this->toArray();
        }
    }) extends AppResource {

        /**
         * @return array<int|string, mixed>
         */
        public function toArray(Request $request): array
        {
            return [
                $this->id(),
                $this->attribute('name', alias: 'value', prefix: 'profile_', suffix: '_label', resolver: fn (string $name): string => mb_strtoupper($name)),
                ...$this->timestamps(),
            ];
        }
    };

    expect($resource->toInertia())->toBe([
        'id' => 7,
        'profile_value_label' => 'ALPHA',
        'created_at' => $createdAt->toDateTimeString(),
        'updated_at' => $updatedAt->toDateTimeString(),
    ]);
});

it('includes loaded relations when explicitly defined', function (): void {
    $resource = new class(new class() implements Arrayable
    {
        public string $name = 'Owner';

        /** @var array<int, array{name: string}> */
        public array $members = [
            ['name' => 'Alex'],
            ['name' => 'Taylor'],
        ];

        /**
         * @return array<string, mixed>
         */
        public function toArray(): array
        {
            return [
                'name' => $this->name,
            ];
        }

        /**
         * @return array<string, mixed>
         */
        public function getAttributes(): array
        {
            return $this->toArray();
        }

        public function relationLoaded(string $key): bool
        {
            return $key === 'members';
        }
    }) extends AppResource {

        /**
         * @return array<int|string, mixed>
         */
        public function toArray(Request $request): array
        {
            return [
                $this->attribute('name'),
                $this->relation('members', resolver: fn (array $members): array => array_map(
                    fn (array $member): string => mb_strtoupper($member['name']),
                    $members,
                )),
            ];
        }
    };

    expect($resource->toInertia())->toBe([
        'name' => 'Owner',
        'members' => ['ALEX', 'TAYLOR'],
    ]);
});

it('builds a typed collection for resources', function (): void {
    User::factory()->count(2)->create();

    $collection = UserResource::collection(User::query()->orderBy('id')->get());

    expect($collection)
        ->toBeInstanceOf(AppResourceCollection::class)
        ->and($collection->toInertia())
        ->toHaveCount(2)
        ->each->toHaveKeys([
            'id',
            'name',
            'email',
            'email_verified_at',
        ])
        ->each->not->toHaveKeys([
            'remember_token',
            'created_at',
            'updated_at',
        ]);
});

it('resolves paginated collections for inertia with meta and links', function (): void {
    User::factory()->count(3)->create();

    $users = User::query()->orderBy('id')->paginate(2);
    $payload = UserResource::collection($users)->toInertia();

    expect($payload)
        ->toHaveKeys(['data', 'links', 'meta'])
        ->and($payload['data'])->toHaveCount(2)
        ->and($payload['meta'])->toMatchArray([
            'current_page' => 1,
            'from' => 1,
            'last_page' => 2,
            'per_page' => 2,
            'to' => 2,
            'total' => 3,
        ])
        ->and($payload['links'])->toHaveKeys(['first', 'last', 'prev', 'next']);
});

it('uses the same pagination shape for json responses', function (): void {
    User::factory()->count(3)->create();

    $users = User::query()->orderBy('id')->paginate(2);
    $payload = UserResource::collection($users)->response()->getData(true);

    expect($payload)
        ->toHaveKeys(['data', 'links', 'meta'])
        ->and($payload['data'])->toHaveCount(2)
        ->and($payload['meta'])->toMatchArray([
            'current_page' => 1,
            'last_page' => 2,
            'per_page' => 2,
            'total' => 3,
        ]);
});
