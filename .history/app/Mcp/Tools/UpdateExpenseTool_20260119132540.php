<?php

namespace App\Mcp\Tools;

use App\Models\Expense;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateExpenseTool extends Tool
{
    protected string $description =
        'Update an existing expense. Only provided fields will be changed.';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id'   => 'required|integer|exists:expenses,id',
            'description'  => 'nullable|string',
            'amount'       => 'nullable|numeric|min:0',
            'expense_date' => 'nullable|date',
        ]);

        // Remove expense_id from update payload
        $expenseId = $validated['expense_id'];
        unset($validated['expense_id']);

        // Ensure at least one field is provided
        if (empty(array_filter($validated, fn ($v) => !is_null($v)))) {
            return Response::text(
                'No fields provided to update.',
                422
            );
        }

        $expense = Expense::findOrFail($expenseId);

        $expense->fill($validated);
        $expense->save();

        return Response::json([
            'message' => 'Expense updated successfully.',
            'expense' => $expense,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'expense_id' => $schema->integer()
                ->description('ID of the expense to update (required).'),

            'description' => $schema->string()
                ->description('Updated expense description.'),

            'amount' => $schema->number()
                ->description('Updated expense amount.'),

            'expense_date' => $schema->string()
                ->format('date')
                ->description('Updated expense date (YYYY-MM-DD).'),
        ];
    }
}
