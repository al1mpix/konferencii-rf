<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Подберите зал для');
    }

    public function test_registration_form_is_available(): void
    {
        $this->get('/register')->assertOk()->assertSee('Создать аккаунт');
    }

    public function test_login_form_is_available(): void
    {
        $this->get('/login')->assertOk()->assertSee('Войти в аккаунт');
    }
}
