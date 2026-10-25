<?php

use BaptChau\Exception\InvalidFlagException;
use BaptChau\FeatureManager;
use PHPUnit\Framework\TestCase;


class FeatureManagerTest extends TestCase
{
    private FeatureManager $manager;

    protected function setUp(): void
    {
        $this->manager = FeatureManager::fromYamlFile("test/config/features.yaml");
    }

    public function testLoadFlags()
    {
        $this->assertCount(4, $this->manager->all());
        $this->assertTrue($this->manager->has("beta_dashboard"));
        $this->assertSame("baptiste-chaudron", $this->manager->get("beta_dashboard")->getAuthor());
        $this->assertNull($this->manager->get("unknown"));
    }

    public function testDateConstraint()
    {
        $this->assertFalse($this->manager->isEnabled("christmas_banner", null, new DateTime("2023-12-19")));
        $this->assertTrue($this->manager->isEnabled("christmas_banner", null, new DateTime("2023-12-20")));
        $this->assertTrue($this->manager->isEnabled("christmas_banner", null, new DateTime("2023-12-26 23:59")));
        $this->assertFalse($this->manager->isEnabled("christmas_banner", null, new DateTime("2023-12-27")));
    }

    public function testListConstraint()
    {
        $this->assertTrue($this->manager->isEnabled("beta_dashboard", "user-1"));
        $this->assertFalse($this->manager->isEnabled("beta_dashboard", "user-3"));
        $this->assertFalse($this->manager->isEnabled("beta_dashboard"));
    }

    public function testSwitchConstraint()
    {
        $this->assertFalse($this->manager->isEnabled("killed_feature"));
    }

    public function testNoConstraint()
    {
        $this->assertTrue($this->manager->isEnabled("always_on"));
    }

    public function testUnknownFlagIsDisabled()
    {
        $this->assertFalse($this->manager->isEnabled("unknown"));
    }

    public function testExpiredFlags()
    {
        $expired = $this->manager->getExpiredFlags(new DateTime("2024-02-01"));

        $this->assertSame(["christmas_banner", "always_on"], array_keys($expired));
    }

    public function testProjectFeatureFlagFileIsValid()
    {
        $this->assertInstanceOf(FeatureManager::class, FeatureManager::fromYamlFile());
    }

    public function testMalformedYamlThrows()
    {
        $this->expectException(InvalidFlagException::class);

        FeatureManager::fromYamlFile("test/config/missing.yaml");
    }

    public function testInvalidConfigThrows()
    {
        $this->expectException(InvalidFlagException::class);

        FeatureManager::fromArray(['flags' => ['bad' => ['author' => "", 'description' => "d", 'deleteDate' => "2024-01-01"]]]);
    }

    public function testMissingFlagsKeyThrows()
    {
        $this->expectException(InvalidFlagException::class);

        FeatureManager::fromArray([]);
    }
}
