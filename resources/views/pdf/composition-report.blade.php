<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/filament-chart-js-plugins.js'])
</head>
<body>
        <h1>Composition Report</h1>
        @foreach($sourceMaterials as $sourceMaterial)
        <h2>{{ $sourceMaterial->name }}</h2>
        <p>{{ $sourceMaterial->grade }}</p>
        <p>{{ $sourceMaterial->supplier_identifier }}</p>
        <p>{{ $sourceMaterial->description }}</p>
    
        <div>
            @livewire(\App\Filament\Resources\SourceMaterialResource\Widgets\CompositionBarWidget::class, ['record' => $sourceMaterial])
        </div>
        
        <table>
            <thead>
                <tr>
                    @foreach($sourceMaterial->composition as $element => $value)
                        <th>{{ $element }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($sourceMaterial->composition as $element => $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        @endforeach
        
</body>
</html>