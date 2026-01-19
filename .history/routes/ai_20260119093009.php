<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\ExpenseTracker;

Mcp::server(ExpenseTracker::class)->routes(function () {
    $this->post('/generate-report', 'ExpenseReportController@generateReport');
    $this->post('/categorize-expenses', 'ExpenseCategorizationController@categorizeExpenses');
});
