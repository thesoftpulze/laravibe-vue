<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Attributes\Encrypted;
use BackedEnum;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;
use JsonException;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use TypeError;
use UnexpectedValueException;

/**
 * A group of typed application settings. Each public property is one setting, and its default applies until a value is saved.
 *
 * Supported types are string, int, float, bool, array, mixed and backed enums, each optionally nullable.
 *
 * @phpstan-consistent-constructor
 *
 * @implements Arrayable<string, mixed>
 */
abstract class Settings implements Arrayable
{
    /**
     * The name this group's values are stored under.
     */
    abstract public static function group(): string;

    /**
     * Create the group from stored values. A value that no longer fits its property, or cannot be decrypted, keeps its default.
     *
     * @param  array<string, mixed>  $payload
     */
    final public static function fromPayload(array $payload): static
    {
        $settings = new static();

        foreach (self::properties() as $property) {
            if (! array_key_exists($property->getName(), $payload)) {
                continue;
            }

            try {
                $property->setValue($settings, self::decode($property, $payload[$property->getName()]));
            } catch (UnexpectedValueException|DecryptException|JsonException|TypeError) {
                continue;
            }
        }

        return $settings;
    }

    /**
     * Set several settings at once.
     *
     * @param  array<string, mixed>  $values
     *
     * @throws InvalidArgumentException when the group has no setting with one of the names.
     */
    final public function fill(array $values): static
    {
        $properties = [];

        foreach (self::properties() as $property) {
            $properties[$property->getName()] = $property;
        }

        foreach ($values as $name => $value) {
            if (! array_key_exists($name, $properties)) {
                throw new InvalidArgumentException(sprintf('[%s] has no setting named [%s].', static::class, $name));
            }

            $properties[$name]->setValue($this, $value);
        }

        return $this;
    }

    /**
     * The values that differ from their defaults, encoded for storage.
     *
     * @return array<string, mixed>
     */
    final public function toPayload(): array
    {
        $payload = [];

        foreach (self::properties() as $property) {
            $value = $property->getValue($this);

            if ($value === $property->getDefaultValue()) {
                continue;
            }

            if ($value instanceof BackedEnum) {
                $value = $value->value;
            }

            if ($value !== null && self::isEncrypted($property)) {
                $value = Crypt::encryptString(json_encode($value, JSON_THROW_ON_ERROR));
            }

            $payload[$property->getName()] = $value;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    final public function toArray(): array
    {
        $values = [];

        foreach (self::properties() as $property) {
            $values[$property->getName()] = $property->getValue($this);
        }

        return $values;
    }

    /**
     * The group's settings: its public, non-static properties.
     *
     * @return list<ReflectionProperty>
     *
     * @throws LogicException when a setting has no default value.
     */
    final protected static function properties(): array
    {
        $properties = [];

        foreach (new ReflectionClass(static::class)->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->hasDefaultValue()) {
                throw new LogicException(sprintf('Setting [%s::$%s] needs a default value.', static::class, $property->getName()));
            }

            $properties[] = $property;
        }

        return $properties;
    }

    /**
     * Turn a stored value back into the property's type.
     *
     * @throws UnexpectedValueException when the value does not fit the property.
     * @throws LogicException when the property's type is not supported.
     */
    private static function decode(ReflectionProperty $property, mixed $value): mixed
    {
        if ($value !== null && self::isEncrypted($property)) {
            if (! is_string($value)) {
                throw new UnexpectedValueException('An encrypted setting must be stored as a string.');
            }

            $value = json_decode(Crypt::decryptString($value), true, flags: JSON_THROW_ON_ERROR);
        }

        $type = $property->getType();

        if (! $type instanceof ReflectionNamedType) {
            throw new LogicException(sprintf('Setting [%s::$%s] needs a single declared type.', static::class, $property->getName()));
        }

        if ($value === null) {
            return $type->allowsNull() ? null : throw new UnexpectedValueException('The setting is not nullable.');
        }

        $typeName = $type->getName();

        if (is_subclass_of($typeName, BackedEnum::class)) {
            if (! is_int($value) && ! is_string($value)) {
                throw new UnexpectedValueException('An enum setting must be stored as its backing value.');
            }

            return $typeName::tryFrom($value) ?? throw new UnexpectedValueException('The enum has no case with the stored value.');
        }

        $fits = match ($typeName) {
            'string' => is_string($value),
            'int' => is_int($value),
            'float' => is_int($value) || is_float($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            'mixed' => true,
            default => throw new LogicException(sprintf('Setting [%s::$%s] has an unsupported type [%s].', static::class, $property->getName(), $typeName)),
        };

        if (! $fits) {
            throw new UnexpectedValueException('The stored value does not fit the setting type.');
        }

        return $typeName === 'float' && is_int($value) ? (float) $value : $value;
    }

    private static function isEncrypted(ReflectionProperty $property): bool
    {
        return $property->getAttributes(Encrypted::class) !== [];
    }
}
