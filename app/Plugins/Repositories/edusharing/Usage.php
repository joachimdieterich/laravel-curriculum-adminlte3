<?php

namespace App\Plugins\Repositories\edusharing;

class Usage
{
    public $nodeId;
    public $nodeVersion;
    public $containerId;
    public $resourceId;
    public $usageId;

    public function __construct(
        $nodeId,
        $nodeVersion,
        $containerId,
        $resourceId,
        $usageId)
    {
        $this->nodeId = $nodeId;
        $this->nodeVersion = $nodeVersion;
        $this->containerId = $containerId;
        $this->resourceId = $resourceId;
        $this->usageId = $usageId;
    }

}
