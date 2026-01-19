<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetExpenseTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        A description of what this tool does.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id' => 'integer',
            'month' => 'integer',
            'year' => '|integer',
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'expense_id' => $schema->integer()->description('The ID of the expense to retrieve.'),
            'month' => $schema->integer()->description('The month for which to retrieve expenses.'),
            'year' => $schema->integer()->description('The year for which to retrieve expenses.')
        ];
    }
}
