<?php

namespace App\Mcp\Tools;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetExpenseTool extends Tool
{
    protected string $description =
        'Fetch expenses. If no date is provided, today’s expenses are returned automatically.';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id' => 'sometimes|integer',
            'date'       => 'sometimes|nullable|date',
            'month'      => 'sometimes|nullable|integer|min:1|max:12',
            'year'       => 'sometimes|integer|min:2000',
        ]);

        if (
            !isset($validated['expense_id']) &&
            !isset($validated['date']) &&
            !isset($validated['month']) &&
            !isset($validated['year'])
        ) {
            $validated['date'] = Carbon::today()->toDateString();
        }

        $query = Expense::query();

        if (isset($validated['expense_id'])) {
            $query->where('id', $validated['expense_id']);
        }

        if (isset($validated['date'])) {
            $query->whereDate('expense_date', $validated['date']);
        }

        if (isset($validated['month'])) {
            $query->whereMonth('expense_date', $validated['month']);
        }

        if (isset($validated['year'])) {
            $query->whereYear('expense_date', $validated['year']);
        }

        return Response::json([
            'filters_applied' => $validated,
            'expenses' => $query->get(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'expense_id' => $schema->integer()
                ->description('Specific expense ID (optional).'),

            'date' => $schema->string()
                ->description('Date of expenses (YYYY-MM-DD). Defaults to today.'),

            'month' => $schema->integer()
                ->description('Month number (1–12). Optional.'),

            'year' => $schema->integer()
                ->description('Year (e.g. 2026). Optional.'),
        ];
    }
}
