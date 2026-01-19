<?php

namespace App\Mcp\Tools;

use App\Models\Expense;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetExpenseTool extends Tool
{
    protected string $description = 'Retrieve expenses by ID, date, month, or year.';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id' => 'nullable|integer',
            'date'       => 'nullable|date',
            'month'      => 'nullable|integer|min:1|max:12',
            'year'       => 'nullable|integer|min:2000|max:2100',
        ]);

        $query = Expense::query();

        /* ===============================
           PRIORITY-BASED FILTERING
        ================================ */

        // 1️⃣ Exact expense lookup
        if (!empty($validated['expense_id'])) {
            return Response::json(
                $query->where('id', $validated['expense_id'])->get()
            );
        }

        // 2️⃣ Exact date
        if (!empty($validated['date'])) {
            $query->whereDate('expense_date', $validated['date']);
        }

        // 3️⃣ Month + Year
        if (!empty($validated['month']) && !empty($validated['year'])) {
            $query->whereYear('expense_date', $validated['year'])
                  ->whereMonth('expense_date', $validated['month']);
        }

        // 4️⃣ Only Year
        elseif (!empty($validated['year'])) {
            $query->whereYear('expense_date', $validated['year']);
        }

        /* ===============================
           EXECUTION
        ================================ */

        $expenses = $query
            ->orderBy('expense_date', 'desc')
            ->limit(100) // safety for LLM calls
            ->get();

        return Response::json([
            'count' => $expenses->count(),
            'data'  => $expenses,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'expense_id' => $schema->integer()
                ->description('Exact expense ID (highest priority).'),

            'date' => $schema->string()
                ->format('date')
                ->description('Retrieve expenses for a specific date (YYYY-MM-DD).'),

            'month' => $schema->integer()
                ->description('Month number (1–12). Requires year.'),

            'year' => $schema->integer()
                ->description('Year (e.g., 2026).'),
        ];
    }
}
