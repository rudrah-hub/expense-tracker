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
    protected string $description = <<<MD
        Fetch expenses based on time filters.

        Rules:
        - If period=day → use date
        - If period=month → use month
        - If period=year → use year
        - If period=range → use from + to
        - If aggregate=total → return sum(amount)
        - If aggregate=list → return expense records

        If no period is provided:
        - Default to today
        MD;


    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'expense_id' => 'sometimes|integer',
            'date'       => 'sometimes|nullable|date',
            'month'      => 'sometimes|nullable|integer|min:1|max:12',
            'year'       => 'sometimes|nullable|integer|min:2000',
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
            'period' => $schema->string()
                ->enum(['day','week','month','year','range'])
                ->description('Time period type'),

            'date' => $schema->string()->format('date')->nullable(),
            'month' => $schema->string()->pattern('^\\d{4}-\\d{2}$')->nullable(),
            'year' => $schema->string()->pattern('^\\d{4}$')->nullable(),

            'from' => $schema->string()->format('date')->nullable(),
            'to' => $schema->string()->format('date')->nullable(),

            'aggregate' => $schema->string()
                ->enum(['list','total'])
                ->default('list')
        ];
    }

}
