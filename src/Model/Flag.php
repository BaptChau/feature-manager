<?php

namespace BaptChau\Model;

use BaptChau\Exception\InvalidFlagException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class Flag
{
    private string $flagName;
    private DateTime $deleteDate;
    private string $description;
    /** @var AbstractConstraint[] */
    private array $constraints;
    private string $author;

    /**
     * @param AbstractConstraint[] $constraints
     * @throws InvalidFlagException
     */
    public function __construct(
        string $flagName,
        string $deleteDate,
        string $description,
        array $constraints,
        string $author
    )
    {
        foreach (['flagName' => $flagName, 'description' => $description, 'author' => $author] as $field => $value) {
            if (trim($value) === "") {
                throw new InvalidFlagException("$field can't be empty");
            }
        }
        foreach ($constraints as $constraint) {
            if (!$constraint instanceof AbstractConstraint) {
                throw new InvalidFlagException("Constraints must be instances of " . AbstractConstraint::class);
            }
        }

        try {
            $this->deleteDate = new DateTime($deleteDate);
        } catch (Exception) {
            throw new InvalidFlagException("deleteDate \"$deleteDate\" is not a valid date");
        }

        $this->flagName = $flagName;
        $this->description = $description;
        $this->constraints = $constraints;
        $this->author = $author;
    }

    public function getFlagName(): string
    {
        return $this->flagName;
    }

    public function getDeleteDate(): DateTime
    {
        return $this->deleteDate;
    }

    public function getDeleteDateFormat(string $format = "Y-m-d"): string
    {
        return date_format($this->deleteDate, $format);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return AbstractConstraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * A flag is enabled when every constraint is satisfied
     *
     * @param string|null $subject identifier checked against list constraints (user id, email...)
     * @param DateTimeInterface|null $now defaults to current time
     * @return bool
     */
    public function isEnabled(?string $subject = null, ?DateTimeInterface $now = null): bool
    {
        $now ??= new DateTimeImmutable();

        foreach ($this->constraints as $constraint) {
            if (!$constraint->isSatisfied($subject, $now)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Expired flags should be removed from the code and the config
     */
    public function isExpired(?DateTimeInterface $now = null): bool
    {
        $now ??= new DateTimeImmutable();

        return $this->deleteDate < $now;
    }

    /**
     * Build a flag from a yaml entry: ['flag_name' => [author, description, ...]]
     *
     * @throws InvalidFlagException
     */
    public static function createFromArray(array $flag): Flag
    {
        $flagName = array_key_first($flag);
        if (!is_string($flagName) || !is_array($flag[$flagName])) {
            throw new InvalidFlagException("Flag must be defined as flag_name: { ... }");
        }

        return self::createFromConfig($flagName, $flag[$flagName]);
    }

    /**
     * Build a flag from its name and its yaml config
     *
     * @throws InvalidFlagException
     */
    public static function createFromConfig(string $flagName, array $config): Flag
    {
        foreach (['author', 'description', 'deleteDate'] as $field) {
            if (!isset($config[$field]) || !is_scalar($config[$field])) {
                throw new InvalidFlagException("Flag \"$flagName\": missing \"$field\"");
            }
        }

        $deleteDate = $config['deleteDate'];
        // yaml parses unquoted dates as timestamps
        if (is_int($deleteDate)) {
            $deleteDate = "@$deleteDate";
        }

        $constraints = [];
        foreach ($config['constraints'] ?? [] as $type => $value) {
            try {
                $constraints[] = AbstractConstraint::create((string) $type, $value);
            } catch (InvalidFlagException $e) {
                throw new InvalidFlagException("Flag \"$flagName\": " . $e->getMessage(), 0, $e);
            }
        }

        return new Flag(
            $flagName,
            (string) $deleteDate,
            (string) $config['description'],
            $constraints,
            (string) $config['author']
        );
    }
}
