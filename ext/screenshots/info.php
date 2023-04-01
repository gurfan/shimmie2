<?php

declare(strict_types=1);

namespace Shimmie2;

class ScreenshotListInfo extends ExtensionInfo
{
    public const KEY = "screenshots_list";

    public string $key = self::KEY;
    public string $name = "Screenshots List";
    public string $url = "";
    public array $authors = ["BooruAdmin"];
    public string $license = self::LICENSE_GPLV2;
    public string $description = "Isolates game screenshots to a seperate tab.";
    public ?string $documentation = "";
}
