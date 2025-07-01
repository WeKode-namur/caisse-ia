<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression des étiquettes</title>
    <style>
        @media print {
            body {margin: 0;}
            .label {page-break-inside: avoid;}
        }
        body {background: #fff; margin: 0; padding: 0;}
        .labels-container {display: flex;flex-wrap: wrap;gap: 0;}
        .label {
            width: 56mm;
            height: 22mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            border: 1px solid #000;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .label-inner {
            margin: 0 0 0 2mm;
            width: 53mm;
            height: 19mm;
            display: flex;
            align-items: center;
        }

        .qr {
            width: 16mm;
            height: 16mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .info {
            flex: 1;
            padding-left: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        .name {font-size: 4mm; font-weight: bold; margin-bottom: 1mm;}
        .attrs {font-size: 3mm; margin-bottom: 1mm;}
        .ref {font-size: 2.8mm; color: #333; font-style: italic; margin-bottom: 1mm;}
        .price {font-size: 3.6mm; font-weight: bold; position: absolute; right: 4mm; bottom: 2mm;}
    </style>
</head>
<body>
<div class="labels-container">
    @foreach($variants as $variant)
        @php
            $qty = (int)($quantities[$variant->id] ?? 1);
            $reference = $variant->reference ?: ($variant->article->reference ?? null);
            $attrs = $variant->attributeValues->map(function($attr) {
                return $attr->attribute->name . ' : ' . $attr->value;
            })->take(4)->implode('<br>');
            $price = $variant->sell_price ?? $variant->article->sell_price ?? null;
            $qrCode = $qrCodes[$variant->id] ?? null;
        @endphp
        @for($i = 0; $i < $qty; $i++)
            <div class="label">
                <div class="label-inner">
                    <div class="qr">
                        @if($qrCode)
                            <img src="{{ $qrCode }}" alt="QR Code" style="width: 20mm; height: 20mm;">
                        @endif
                    </div>
                    <div class="info">
                        <div class="name">{{ $variant->name ?? $variant->article->name }}</div>
                        <div class="attrs">{!! $attrs !!}</div>
                        @if($reference)
                            <div class="ref">Réf : <span style="font-style: italic;">{{ $reference }}</span></div>
                        @endif
                    </div>
                    @if($price)
                        <div class="price">{{ number_format($price, 2, ',', ' ') }} €</div>
                    @endif
                </div>
            </div>
        @endfor
    @endforeach
</div>
<script>
    window.onload = function () {
        window.print();
        setTimeout(function () {
            window.close();
        }, 500);
    };
</script>
</body>
</html>
