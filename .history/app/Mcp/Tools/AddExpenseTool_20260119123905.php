<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Models\Expense;

class AddExpenseTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
Adds a new expense to the expense tracker.

If no date is provided, today's date is used.
MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'description'  => 'required|string',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
        ]);

        $expense = Expense::create([
            'description'  => $validated['description'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'] ?? now()->toDateString(),
        ]);

        return Response::json([
            'id'           => $expense->id,
            'description'  => $expense->description,
            'amount'       => $expense->amount,
            'expense_date' => $expense->expense_date,
            'status'       => 'created',
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'description' => $schema
                ->string()
                ->description('What the expense is for, e.g. "Food", "Taxi", "Lunch"'),

            'amount' => $schema
                ->number()
                ->description('Expense amount in rupees'),

            'expense_date' => $schema
                ->string()
                ->format('date')
                ->optional()
                ->description('Date of the expense (YYYY-MM-DD). Defaults to today.'),
        ];
    }
}
