<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Price;
use App\Models\Product;
use App\Models\Stock;

/**
 * Pricing, stock and timeline rules shared by checkout, cancellation and admin order changes.
 */
class OrderService
{
    /** Local 01XXXXXXXXX form of a Bangladeshi mobile number. */
    public static function normalizeMobile(?string $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return str_starts_with($digits, '880') ? '0' . substr($digits, 3) : $digits;
    }

    /** The price row for a product and size, falling back to the product's first price. */
    public function priceRow(Product $product, ?string $sizeName): ?Price
    {
        if ($sizeName) {
            $row = Price::where('product_id', $product->id)
                ->whereHas('size', fn ($q) => $q->where('standard', $sizeName))
                ->first();

            if ($row) {
                return $row;
            }
        }

        return Price::where('product_id', $product->id)->orderBy('id')->first();
    }

    /** Unit price after the row's percentage discount. Prices always come from the database. */
    public function unitPrice(?Price $row): float
    {
        if (! $row) {
            return 0.0;
        }

        $discount = max(0, min(100, (float) $row->discount));

        return round((float) $row->price * (100 - $discount) / 100, 2);
    }

    /** The stock row that backs a price row (or size). Call inside a transaction to lock it. */
    public function stockRow(int $productId, ?Price $row, bool $lock = false): ?Stock
    {
        $query = Stock::where('product_id', $productId);

        if ($row) {
            $byPrice = (clone $query)->where('price_id', $row->id);
            $match = ($lock ? $byPrice->lockForUpdate() : $byPrice)->first();

            if ($match) {
                return $match;
            }

            if ($row->size_id) {
                $bySize = (clone $query)->where('size_id', $row->size_id);
                $match = ($lock ? $bySize->lockForUpdate() : $bySize)->first();

                if ($match) {
                    return $match;
                }
            }
        }

        return ($lock ? $query->lockForUpdate() : $query)->orderBy('id')->first();
    }

    /** Puts the quantities of a cancelled or returned order back on the shelf. */
    public function restock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::find($item->product_id);

            if (! $product) {
                continue;
            }

            $stock = $this->stockRow($product->id, $this->priceRow($product, $item->size_name), true);
            $stock?->increment('quantity', $item->quantity);
            $product->increment('stock_available', $item->quantity);
        }
    }

    public function addTimeline(Order $order, string $status, string $note): void
    {
        $timeline = $order->delivery_timeline ?? [];
        $timeline[] = ['status' => $status, 'time' => now()->toIso8601String(), 'note' => $note];
        $order->delivery_timeline = $timeline;
    }
}
