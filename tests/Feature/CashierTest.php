<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function actingAsCashier(): User
{
    $user = User::factory()->create();
    test()->actingAs($user);

    return $user;
}

it('reduces tracked stock and records stock movements on checkout', function () {
    actingAsCashier();

    $tracked = Product::factory()->create([
        'name' => 'Galon Baru',
        'price' => 45000,
        'stock' => 10,
        'track_stock' => true,
    ]);

    $response = $this->postJson(route('kasir.store'), [
        'items' => [['product_id' => $tracked->id, 'quantity' => 3]],
        'payment_method' => 'tunai',
        'paid_amount' => 150000,
    ]);

    $response->assertOk();

    expect($tracked->fresh()->stock)->toBe(7);
    expect(Transaction::count())->toBe(1);
    expect(StockMovement::where('type', 'keluar')->where('product_id', $tracked->id)->first()->quantity)->toBe(3);

    $transaction = Transaction::first();
    expect($transaction->total)->toBe(135000);
    expect($transaction->change_amount)->toBe(15000);
    expect($transaction->invoice_number)->toMatch('/^INV-\d{8}-0001$/');
});

it('does not reduce stock for products without stock tracking', function () {
    actingAsCashier();

    $service = Product::factory()->create([
        'name' => 'Isi Ulang Air Galon',
        'price' => 6000,
        'stock' => 0,
        'track_stock' => false,
    ]);

    $response = $this->postJson(route('kasir.store'), [
        'items' => [['product_id' => $service->id, 'quantity' => 5]],
        'payment_method' => 'tunai',
        'paid_amount' => 30000,
    ]);

    $response->assertOk();

    expect($service->fresh()->stock)->toBe(0);
    expect(StockMovement::count())->toBe(0);
});

it('rejects checkout when stock is insufficient', function () {
    actingAsCashier();

    $product = Product::factory()->create([
        'name' => 'Segel Galon',
        'price' => 2000,
        'stock' => 2,
        'track_stock' => true,
    ]);

    $response = $this->postJson(route('kasir.store'), [
        'items' => [['product_id' => $product->id, 'quantity' => 5]],
        'payment_method' => 'tunai',
        'paid_amount' => 10000,
    ]);

    $response->assertStatus(422);
    expect(Transaction::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(2);
});

it('rejects checkout when cash paid is less than total', function () {
    actingAsCashier();

    $product = Product::factory()->create([
        'name' => 'Galon Baru',
        'price' => 45000,
        'stock' => 10,
        'track_stock' => true,
    ]);

    $response = $this->postJson(route('kasir.store'), [
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
        'payment_method' => 'tunai',
        'paid_amount' => 10000,
    ]);

    $response->assertStatus(422);
    expect(Transaction::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(10);
});
