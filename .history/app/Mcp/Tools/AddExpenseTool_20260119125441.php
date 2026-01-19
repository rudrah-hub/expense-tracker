<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Models\Expense;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class AddExpenseTool extends Tool
{
    protected string $description = 'Adds a new expense to the expense tracker.';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'description'   => 'required|string',
            'amount'        => 'required|numeric',
            'expense_date'  => 'nullable|date',
        ]);

        $expense = Expense::create([
            'description'  => $validated['description'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'] ?? now()->toDateString(),
        ]);

        return Response::json([
            'status' => 'success',
            'expense' => $expense
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'description' => $schema->string()
                ->description('What the expense is for'),

            'amount' => $schema->number()
                ->description('Expense amount in rupees'),

            'expense_date' => $schema->string()
                ->format('date')
                ->description('YYYY-MM-DD (optional)')
        ];
    }
}
