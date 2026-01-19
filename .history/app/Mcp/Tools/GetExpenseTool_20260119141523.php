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
        - If period=today → use today's date
        - If period=range → use from + to
        - If aggregate=total → return sum(amount)
        - If aggregate=list → return expense records

        If no period is provided:
        - Default to today
        MD;


    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'period' => 'nullable|string',
            'date' => 'nullable|date',
            'month' => 'nullable|string',
            'year' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'aggregate' => 'nullable|string',
        ]);

        $query = Expense::query();

        match ($data['period'] ?? 'day') {
            'day' => $query->whereDate(
                'expense_date',
                $data['date'] ?? now()->toDateString()
            ),

            'month' => $query->whereMonth(
                'expense_date',
                substr($data['month'], 5, 2)
            )->whereYear(
                'expense_date',
                substr($data['month'], 0, 4)
            ),

            'year' => $query->whereYear(
                'expense_date',
                $data['year']
            ),

            'today' => $query->whereDate(
                'expense_date',
                now()->toDateString()
            ),

            'range' => $query->whereBetween(
                'expense_date',
                [$data['from'], $data['to']]
            ),
        };

        if (($data['aggregate'] ?? 'list') === 'total') {
            return Response::json([
                'total' => $query->sum('amount')
            ]);
        }

        return Response::json([
            'expenses' => $query->orderBy('expense_date')->get()
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
