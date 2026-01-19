<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\ExpenseTracker;

Mcp::web('/mcp/demo', ExpenseTracker::class);
