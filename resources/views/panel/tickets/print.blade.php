<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $transaction->reference }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
            background: white;
            color: black;
        }

        .ticket {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .ticket-info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .items {
            margin-bottom: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .item-name {
            flex: 1;
        }

        .item-price {
            text-align: right;
            min-width: 60px;
        }

        .totals {
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .final-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .payments {
            margin-top: 10px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .payment {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .change {
            margin-top: 10px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 9px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">Imprimer</button>

    <div class="ticket">
        <div class="header">
            <div class="company-name">{{ config('app.name') }}</div>
            <div class="ticket-info">
                Ticket #{{ $transaction->reference }}<br>
                {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                {{ $transaction->user->name ?? 'Vendeur' }}
            </div>
        </div>

        <div class="items">
            @foreach($transaction->items as $item)
                <div class="item">
                    <div class="item-name">
                        {{ $item->quantity }}x {{ $item->article_name }}
                        @if($item->variant_name)
                            <br><small>{{ $item->variant_name }}</small>
                        @endif
                    </div>
                    <div class="item-price">
                        €{{ number_format($item->unit_price_ttc, 2, ',', ' ') }}
                    </div>
                </div>
                <div class="item">
                    <div class="item-name"></div>
                    <div class="item-price">
                        €{{ number_format($item->total_price_ttc, 2, ',', ' ') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-line">
                <span>Sous-total HT:</span>
                <span>€{{ number_format($totals['subtotal_ht'], 2, ',', ' ') }}</span>
            </div>
            <div class="total-line">
                <span>TVA:</span>
                <span>€{{ number_format($totals['total_tva'], 2, ',', ' ') }}</span>
            </div>
            <div class="total-line">
                <span>Sous-total TTC:</span>
                <span>€{{ number_format($totals['subtotal_ttc'], 2, ',', ' ') }}</span>
            </div>
            @if($totals['total_discount'] > 0)
                <div class="total-line">
                    <span>Remise:</span>
                    <span>-€{{ number_format($totals['total_discount'], 2, ',', ' ') }}</span>
                </div>
            @endif
            <div class="total-line final-total">
                <span>TOTAL:</span>
                <span>€{{ number_format($totals['final_total'], 2, ',', ' ') }}</span>
            </div>
        </div>

        <div class="payments">
            @foreach($transaction->payments as $payment)
                <div class="payment">
                    <span>{{ $payment->paymentMethod->name }}:</span>
                    <span>€{{ number_format($payment->amount, 2, ',', ' ') }}</span>
                </div>
            @endforeach
        </div>

        @php
            $totalPaid = $transaction->payments->sum('amount');
            $changeAmount = $totalPaid - $totals['final_total'];
        @endphp

        @if($changeAmount > 0)
            <div class="change">
                <div class="payment">
                    <span>Monnaie rendue:</span>
                    <span>€{{ number_format($changeAmount, 2, ',', ' ') }}</span>
                </div>
            </div>
        @endif

        @if($transaction->notes)
            <div style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                <strong>Note:</strong><br>
                {{ $transaction->notes }}
            </div>
        @endif

        <div class="footer">
            Merci de votre visite !<br>
            {{ config('app.name') }} - {{ date('Y') }}
        </div>
    </div>
</body>
</html>
