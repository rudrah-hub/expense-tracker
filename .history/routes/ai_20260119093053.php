<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\ExpenseTracker;

Mcp::web('/mcp/expense-tracker', ExpenseTracker::class);
