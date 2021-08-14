<?php

namespace Webfoto\Types;

use DateTime;

class InputImage
{
    public string $path;
    public DateTime $timestamp;

    public function __construct(string $path, DateTime $timestamp)
    {
        $this->path = $path;
        $this->timestamp = $timestamp;
    }
}
