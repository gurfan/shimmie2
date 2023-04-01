<?php

declare(strict_types=1);

namespace Shimmie2;

class ScreenshotsTabInfo extends ExtensionInfo
{
    public const KEY = "screenshots_tab";

    public string $key = self::KEY;
    public string $name = "Screenshots Tab";
    public string $url = "";
    public array $authors = ["BooruAdmin"];
    public string $license = self::LICENSE_GPLV2;
    public string $description = "Isolates game screenshots to a seperate tab.";
    public ?string $documentation = "";
}
