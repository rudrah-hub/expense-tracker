<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class ExpenseTrackerResource extends Resource
{
    /**
     * The resource's description.
     */
    protected string $description = <<<'MARKDOWN'
        A description of what this resource contains.
    MARKDOWN;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        const resources = {
  today: new Date().toISOString().slice(0, 10),
  timezone: "Asia/Kolkata",
  week_starts_on: "monday"
};


        return Response::text('The resource content.');
    }
}
