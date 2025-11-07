<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentReceipt>
 */
class PaymentReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_date' => now(),
            'amount_paid' => 2500.00,
            'payment_year' => now()->year,
            'payment_month' => now()->month,
            'reference' => $this->faker->bothify('###-????'),
            'account_holder_name' => $this->faker->name(),
            'issuing_bank' => $this->faker->company(),
            'payment_method' => $this->faker->randomElement(['cash', 'transfer', 'card', 'check']),
            'receipt_image' => 'payment-receipts/test.jpg',
            'status' => 'pending',
        ];
    }
}
