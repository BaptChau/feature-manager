<?php

namespace BaptChau;

use BaptChau\Config\ConfigLocator;
use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\Flag;
use DateTimeInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FeatureManager
{
    /** @var array<string, Flag> */
    private array $flags = [];

    /**
     * @param Flag[] $flags
     */
    public function __construct(array $flags = [])
    {
        foreach ($flags as $flag) {
            $this->addFlag($flag);
        }
    }

    /**
     * @param string|null $path defaults to the project flag file, see ConfigLocator
     * @throws InvalidFlagException if the file can't be read or a flag is malformed
     */
    public static function fromYamlFile(?string $path = null): FeatureManager
    {
        $path ??= ConfigLocator::getConfigFile();

        try {
            $config = Yaml::parseFile($path);
        } catch (ParseException $e) {
            throw new InvalidFlagException("Can't read $path: " . $e->getMessage(), 0, $e);
        }

        return self::fromArray($config ?? []);
    }

    /**
     * @param array $config parsed config, flags under the "flags" key
     * @throws InvalidFlagException if a flag is malformed
     */
    public static function fromArray(array $config): FeatureManager
    {
        if (!isset($config['flags']) || !is_array($config['flags'])) {
            throw new InvalidFlagException("Config must contain a \"flags\" map");
        }

        $manager = new FeatureManager();
        foreach ($config['flags'] as $flagName => $flagConfig) {
            if (!is_array($flagConfig)) {
                throw new InvalidFlagException("Flag \"$flagName\" must be a map");
            }
            $manager->addFlag(Flag::createFromConfig((string) $flagName, $flagConfig));
        }
        return $manager;
    }

    public function addFlag(Flag $flag): void
    {
        if ($this->has($flag->getFlagName())) {
            throw new InvalidFlagException("Flag \"{$flag->getFlagName()}\" is already defined");
        }
        $this->flags[$flag->getFlagName()] = $flag;
    }

    public function has(string $flagName): bool
    {
        return isset($this->flags[$flagName]);
    }

    public function get(string $flagName): ?Flag
    {
        return $this->flags[$flagName] ?? null;
    }

    /**
     * @return array<string, Flag>
     */
    public function all(): array
    {
        return $this->flags;
    }

    /**
     * Unknown flags are considered disabled
     *
     * @param string $flagName
     * @param string|null $subject identifier checked against list constraints (user id, email...)
     * @param DateTimeInterface|null $now defaults to current time
     * @return bool
     */
    public function isEnabled(string $flagName, ?string $subject = null, ?DateTimeInterface $now = null): bool
    {
        return $this->get($flagName)?->isEnabled($subject, $now) ?? false;
    }

    /**
     * Flags past their deleteDate, to clean up
     *
     * @return array<string, Flag>
     */
    public function getExpiredFlags(?DateTimeInterface $now = null): array
    {
        return array_filter($this->flags, fn (Flag $flag) => $flag->isExpired($now));
    }
}
