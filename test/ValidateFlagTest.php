<?php

use BaptChau\Helper\FlagValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;


class ValidateFlagTest extends TestCase
{
    public function testValidFlag()
    {
        $config = Yaml::parseFile("test/config/validFeature.yaml");

        $this->assertNotEmpty($config, "Feature config is empty");
        $this->assertArrayHasKey("flags", $config);

        //Check valid flag
        $this->assertTrue(FlagValidator::validateFlag($config['flags']));
    }

    public function testUnvalidFlag()
    {
        $config = Yaml::parseFile("test/config/invalidFeature.yaml");

        $this->assertFalse(FlagValidator::validateFlag($config['flags']['invalid_flag']['badAuthor']));
        $this->assertFalse(FlagValidator::validateFlag($config['flags']['invalid_flag']['deleteDate']));
        $this->assertFalse(FlagValidator::validateFlag($config['flags']['invalid_flag']['badOpeningDate']));
    }
}
