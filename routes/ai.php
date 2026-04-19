<?php

// MCP chỉ hoạt động ở môi trường development
// Trên production (composer install --no-dev), package laravel/mcp không tồn tại
if (! class_exists(\Laravel\Mcp\Facades\Mcp::class)) {
    return;
}

use App\Mcp\Servers\WarehouseServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('warehouse', WarehouseServer::class);
