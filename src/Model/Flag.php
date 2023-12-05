<?php

namespace BaptChau\Model;

use DateTime;

class Flag
{
    private string $flagName;
    private DateTime $deleteDate;
    private string $description;
    private array $constraints;
    private string $author;

    public function __construct(
        string $flagName,
        string $deleteDate,
        string $description,
        array $constraints,
        string $author
    )
    {
        $this->flagName = $flagName;
        $this->deleteDate = new DateTime($deleteDate);
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

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public static function createFromArray(array $flag): Flag
    {
        $flagName = array_key_first($flag);
        return new Flag(
            $flagName,
            $flag[$flagName]['deleteDate'],
            $flag[$flagName]['description'],
            $flag[$flagName]['constraints'],
            $flag[$flagName]['author']
        );
    }
}
