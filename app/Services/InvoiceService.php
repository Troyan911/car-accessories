<?php

namespace App\Services;

use App\Enums\Order\OrderStatus;
use App\Enums\Order\Unit;
use App\Models\Order;
use App\Services\Contract\InvoiceServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;

class InvoiceService implements InvoiceServiceContract
{
    public function generate(Order $order): Invoice
    {
        $order->loadMissing(['user', 'transaction', 'products']);
        $fullName = $order->fullName;
        $orderSerial = $order->vendor_order_id ?? $order->id;
        $fileName = Str::of("$fullName $orderSerial")->slug();

        $customer = new Buyer([
            'name' => $fullName,
            'custom_fields' => [
                'email' => $order->email,
                'phone' => $order->phone,
                'city' => $order->city,
                'address' => $order->address,
            ],
        ]);

        $invoice = Invoice::make('receipt')
            ->series('BIG')
            ->status($order->status->name->value)
            ->buyer($customer)
            ->date($order->created_at)
            ->currencyDecimalPoint(',')
            ->filename($fileName)
            ->addItems($this->getInvoiceItems($order->products))
            ->logo(public_path('vendor/invoices/sample-logo.png'))
            ->taxRate(config('cart.tax'))
            ->save('public');

        if ($order->status->name === OrderStatus::InProcess->name) {
            $invoice->payUntilDays(3);
        }

        return $invoice;
    }

    protected function getInvoiceItems(Collection $products): array
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = (new InvoiceItem())
                ->title($product->title)
                ->pricePerUnit($product->pivot->single_price)
                ->quantity($product->pivot->quantity)
                ->units(Unit::PSC->value);
        }

        return $items;
    }
}
