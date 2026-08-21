<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_index_page_requires_login(): void
    {
        $response = $this->get('/customers');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_access_customers(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@elitetechprecision.co.uk',
            'password' => bcrypt('admin123'),
        ]);

        Customer::factory()->create([
            'name' => 'Northgate Retail Park',
            'email' => 'info@northgate.example',
            'phone' => '01234567890',
            'status' => 'active',
            'region' => 'Dublin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@elitetechprecision.co.uk',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/customers');
        $this->assertAuthenticatedAs($user);

        $this->get('/customers')
            ->assertOk()
            ->assertSee('Customers')
            ->assertSee('Northgate Retail Park');
    }
}
