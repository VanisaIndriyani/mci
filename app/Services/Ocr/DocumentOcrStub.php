<?php

namespace App\Services\Ocr;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentOcrStub
{
    /**
     * @return array<string, mixed>
     */
    public function extract(string $kind, UploadedFile $file): array
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $normalized = Str::of($baseName)->replace(['__', '--'], '_')->replace([' ', '-'], '_')->lower()->value();

        return match ($kind) {
            'po' => $this->extractPo($normalized),
            'delivery' => $this->extractDelivery($normalized),
            'invoice' => $this->extractInvoice($normalized),
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function extractPo(string $text): array
    {
        return [
            'po_number' => $this->extractNumber($text, ['po', 'purchaseorder']),
            'po_date' => $this->extractDate($text),
            'customer_name' => $this->extractCustomer($text),
            'unit_price' => $this->extractPrice($text),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractDelivery(string $text): array
    {
        return [
            'delivery_number' => $this->extractNumber($text, ['sj', 'suratjalan', 'delivery']),
            'delivery_date' => $this->extractDate($text),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractInvoice(string $text): array
    {
        return [
            'invoice_number' => $this->extractNumber($text, ['inv', 'invoice']),
            'invoice_date' => $this->extractDate($text),
            'amount' => $this->extractPrice($text),
        ];
    }

    private function extractPrice(string $text): ?float
    {
        if (preg_match('/(?:^|_)price[_-]?(\d+)/i', $text, $m) === 1) {
            return (float) $m[1];
        }

        if (preg_match('/(?:^|_)amt[_-]?(\d+)/i', $text, $m) === 1) {
            return (float) $m[1];
        }

        return null;
    }

    private function extractNumber(string $text, array $prefixes): ?string
    {
        foreach ($prefixes as $prefix) {
            if (preg_match('/(?:^|_)'.$prefix.'[_-]?([a-z0-9\\/\\.]+)/i', $text, $m) === 1) {
                return strtoupper($m[1]);
            }
        }

        if (preg_match('/(?:^|_)([a-z]{1,5}[0-9]{2,})/i', $text, $m) === 1) {
            return strtoupper($m[1]);
        }

        return null;
    }

    private function extractDate(string $text): ?string
    {
        if (preg_match('/(20\\d{2})[\\-_\\.]?(0[1-9]|1[0-2])[\\-_\\.]?([0-2]\\d|3[0-1])/', $text, $m) === 1) {
            return $m[1].'-'.$m[2].'-'.$m[3];
        }

        return null;
    }

    private function extractCustomer(string $text): ?string
    {
        if (preg_match('/(?:^|_)cust(?:omer)?[_-]?([a-z0-9]+)/i', $text, $m) === 1) {
            return Str::of($m[1])->replace('_', ' ')->upper()->value();
        }

        if (preg_match('/(?:^|_)pt[_-]?([a-z0-9_]+)/i', $text, $m) === 1) {
            return 'PT '.Str::of($m[1])->replace('_', ' ')->upper()->value();
        }

        return null;
    }
}
