<?php

use BaptChau\Config\ConfigLocator;
use BaptChau\Exception\InvalidFlagException;
use PHPUnit\Framework\TestCase;


class ConfigLocatorTest extends TestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->projectRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid("feature-manager-");
        mkdir($this->projectRoot);
    }

    protected function tearDown(): void
    {
        @unlink($this->projectRoot . "/composer.json");
        rmdir($this->projectRoot);
    }

    public function testDefaultPathWithoutComposerFile()
    {
        $this->assertSame(
            $this->projectRoot . DIRECTORY_SEPARATOR . "config/feature_flag.yaml",
            ConfigLocator::getConfigFile($this->projectRoot)
        );
    }

    public function testDefaultPathWithoutOverride()
    {
        $this->writeComposer(['name' => "acme/app"]);

        $this->assertSame(
            $this->projectRoot . DIRECTORY_SEPARATOR . "config/feature_flag.yaml",
            ConfigLocator::getConfigFile($this->projectRoot)
        );
    }

    public function testRelativeOverride()
    {
        $this->writeComposer(['extra' => ['feature-manager' => ['config-file' => "flags/my_flags.yaml"]]]);

        $this->assertSame(
            $this->projectRoot . DIRECTORY_SEPARATOR . "flags/my_flags.yaml",
            ConfigLocator::getConfigFile($this->projectRoot)
        );
    }

    public function testAbsoluteOverride()
    {
        $this->writeComposer(['extra' => ['feature-manager' => ['config-file' => "/etc/app/flags.yaml"]]]);

        $this->assertSame("/etc/app/flags.yaml", ConfigLocator::getConfigFile($this->projectRoot));
    }

    public function testInvalidOverrideThrows()
    {
        $this->writeComposer(['extra' => ['feature-manager' => ['config-file' => ""]]]);

        $this->expectException(InvalidFlagException::class);

        ConfigLocator::getConfigFile($this->projectRoot);
    }

    private function writeComposer(array $composer): void
    {
        file_put_contents($this->projectRoot . "/composer.json", json_encode($composer));
    }
}
