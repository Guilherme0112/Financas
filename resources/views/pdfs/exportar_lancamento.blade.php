<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Lançamentos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f3f4f6;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        th {
            font-weight: bold;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        /* Marca d’água */
        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.2;
            z-index: -1000;
        }

        .watermark img {
            width: 400px;
        }
    </style>
</head>
<body>

    {{-- Marca d’água --}}
    <div class="watermark">
        <img src="{{ public_path('logo-faturai.png') }}">
    </div>

    <h1>Relatório de Lançamentos</h1>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Tipo</th>
                <th>Mês de Referência</th>
                <th>Categoria</th>
                <th>Foi Pago</th>
            </tr>
        </thead>

        <tbody>
            @foreach($dados as $item)
                <tr>
                    <td>{{ $item['nome'] }}</td>
                    <td>{{ $item['descricao'] }}</td>
                    <td class="text-right">
                        {{ number_format($item['valor'], 2, ',', '.') }}
                    </td>
                    <td>{{ $item['tipo'] }}</td>
                    <td>{{ $item['mes_referencia'] }}</td>
                    <td>{{ $item['categoria_entrada'] ?? $item['categoria_saida'] }}</td>
                    <td>{{ $item['foi_pago'] ? 'Sim' : 'Não' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
