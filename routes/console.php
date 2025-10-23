<?php

use Illuminate\Support\Facades\{Schedule};

Schedule::command('logs:clear-old --days=30')->daily();
