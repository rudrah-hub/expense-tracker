<?php

namespace App\Mcp\Tools;

use App\Models\Expense;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class DeleteExpenseTool extends Tool
{
    protected string $description =
        'Delete an expense permanently using its ID.';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id' => 'required|integer|exists:expenses,id',
        ]);

        $expense = Expense::findOrFail($validated['expense_id']);

        $expense->delete();

        return Response::json([
            'message' => 'Expense deleted successfully.',
            'deleted_expense_id' => $expense->id,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'expense_id' => $schema->integer()
                ->description('ID of the expense to delete (required).'),
        ];
    }
}
