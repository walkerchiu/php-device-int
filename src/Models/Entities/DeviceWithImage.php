<?php

namespace WalkerChiu\Device\Models\Entities;

use WalkerChiu\Device\Models\Entities\Device;
use WalkerChiu\MorphImage\Models\Entities\ImageTrait;

class DeviceWithImage extends Device
{
    use ImageTrait;
}
