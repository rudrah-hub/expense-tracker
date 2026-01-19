<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class ExpenseTrackerResource extends Resource
{
    /**
     * The resource's description.
     */
    protected string $description = <<<MARKDOWN
Context information for the expense tracker.

Includes:
- Today's date
- Server timezone
- Week start day
- Date handling rules
MARKDOWN;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        return Response::json([
            'today' => now()->toDateString(),
            'timezone' => config('app.timezone', 'Asia/Kolkata'),
            'week_starts_on' => 'monday',

            'date_rules' => [
                'today' => 'Current calendar date',
                'yesterday' => 'today - 1 day',
                'tomorrow' => 'today + 1 day',
                'this_week' => 'Week containing today',
                'last_week' => 'Previous calendar week',
                'this_month' => 'Month containing today',
                'last_month' => 'Previous calendar month',
                'this_year' => 'Year containing today',
            ],

            'defaults' => [
                'expense_query_period' => 'day',
                'expense_query_aggregate' => 'list',
            ],
        ]);
    }
}
