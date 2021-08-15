<?php

namespace Webfoto\Types;

use DateTime;

class Image
{
    public string $path;
    public DateTime $timestamp;

    public function __construct(string $path, DateTime $timestamp)
    {
        $this->path = $path;
        $this->timestamp = $timestamp;
    }
}
