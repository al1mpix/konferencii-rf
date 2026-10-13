<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:conference', function () {
    $this->comment('Конференции.РФ — Laravel + MySQL');
})->purpose('Show basic project information');
