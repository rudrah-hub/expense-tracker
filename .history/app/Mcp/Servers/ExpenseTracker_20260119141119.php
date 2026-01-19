<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use App\Mcp\Tools\AddExpenseTool;
use App\Mcp\Tools\GetExpenseTool;
use App\Mcp\Tools\UpdateExpenseTool;
use App\Mcp\Tools\DeleteExpenseTool;
use App\Mcp\Resources\ExpenseTrackerResource;
use App\Mcp\Prompts\ExpenseSummaryPrompt;

class ExpenseTracker extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Expense Tracker';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Instructions describing how to use the server and its features.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        AddExpenseTool::class,
        GetExpenseTool::class,
        UpdateExpenseTool::class,
        DeleteExpenseTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        ExpenseTrackerResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
