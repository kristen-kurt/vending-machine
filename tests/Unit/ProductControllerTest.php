<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    public function test_index_returns_view()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    public function test_admin_can_create_product()
    {
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 9.99,
            'quantity_available' => 10
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_user_cannot_create_product()
    {
        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 9.99,
            'quantity_available' => 10
        ]);

        $response->assertStatus(403);
    }

    public function test_purchase_reduces_quantity()
    {
        $product = Product::factory()->create([
            'quantity_available' => 5,
            'price' => 10.00
        ]);

        $response = $this->actingAs($this->user)
            ->post("/products/{$product->id}/purchase", [
                'quantity' => 2
            ]);

        $response->assertRedirect();
        $this->assertEquals(3, $product->fresh()->quantity_available);
    }
    public function test_destroy_deletes_product()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
