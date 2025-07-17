<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Articles Inconnus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
        }

        .stats {
            margin-bottom: 30px;
        }

        .stat-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            width: 16.66%;
        }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .status-regularized {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .status-non-identifiable {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .page-break {
            page-break-before: always;
        }

        h3 {
            font-size: 16px;
            margin: 20px 0 10px 0;
            color: #333;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="title">Rapport des Articles Inconnus</div>
    <div class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
</div>

<div class="stats">
    <div class="stat-row">
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['pending']) }}</div>
            <div class="stat-label">En attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['regularized']) }}</div>
            <div class="stat-label">Régularisés</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['non_identifiable']) }}</div>
            <div class="stat-label">Non identifiables</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['total_amount'], 2) }} €</div>
            <div class="stat-label">Montant total</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($stats['total_vat'], 2) }} €</div>
            <div class="stat-label">TVA totale</div>
        </div>
    </div>
</div>

@if($unknownItems->count() > 0)
    <h3>Liste des Articles Inconnus</h3>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Prix unit.</th>
            <th>Qté</th>
            <th>Total</th>
            <th>TVA</th>
            <th>Statut</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($unknownItems as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ Str::limit($item->description ?? $item->nom, 30) }}</td>
                <td>{{ number_format($item->prix, 2) }} €</td>
                <td>{{ $item->transactionItem->quantity ?? 1 }}</td>
                <td>{{ number_format($item->prix, 2) }} €</td>
                <td>{{ number_format($item->tva, 2) }} €</td>
                <td>
                    @if(!$item->est_regularise)
                        <span class="status-pending">En attente</span>
                    @elseif($item->note_interne && str_starts_with($item->note_interne, 'Non identifiable'))
                        <span class="status-non-identifiable">Non identifiable</span>
                    @else
                        <span class="status-regularized">Régularisé</span>
                    @endif
                </td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($unknownItems->where('est_regularise', true)->where('note_interne', 'not like', 'Non identifiable%')->count() > 0)
        <div class="page-break"></div>
        <h3>Articles Régularisés</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Variant correspondant</th>
                <th>Notes</th>
                <th>Régularisé le</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unknownItems->where('est_regularise', true)->where('note_interne', 'not like', 'Non identifiable%') as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ Str::limit($item->description ?? $item->nom, 30) }}</td>
                    <td>{{ $item->transactionItem->variant->barcode ?? 'Variant supprimé' }}</td>
                    <td>{{ Str::limit($item->note_interne ?? '-', 40) }}</td>
                    <td>{{ $item->updated_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if($unknownItems->where('est_regularise', true)->where('note_interne', 'like', 'Non identifiable%')->count() > 0)
        <div class="page-break"></div>
        <h3>Articles Non Identifiables</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Raison</th>
                <th>Marqué le</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unknownItems->where('est_regularise', true)->where('note_interne', 'like', 'Non identifiable%') as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ Str::limit($item->description ?? $item->nom, 30) }}</td>
                    <td>{{ Str::limit(str_replace('Non identifiable: ', '', $item->note_interne), 40) }}</td>
                    <td>{{ $item->updated_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@else
    <p>Aucun article inconnu trouvé pour cette période.</p>
@endif

<div class="footer">
    <p>Rapport généré automatiquement par le système de caisse</p>
    <p>Conforme à la législation belge sur la gestion des articles non identifiés</p>
</div>
</body>
</html>
