<div>
    <h2>Gestión del Sitemap</h2>

    <input type="text" wire:model="newUrl" placeholder="Nueva URL">
    <select wire:model="changefreq">
        <option value="daily">Diario</option>
        <option value="weekly">Semanal</option>
        <option value="monthly">Mensual</option>
    </select>
    <input type="number" step="0.1" wire:model="priority" min="0.1" max="1.0">
    <button wire:click="addUrl">Agregar</button>

    <ul>
        @foreach($urls as $url)
            <li>{{ $url->url }} ({{ $url->changefreq }}, {{ $url->priority }})
                <button wire:click="deleteUrl({{ $url->id }})">❌</button>
            </li>
        @endforeach
    </ul>

    <button wire:click="$emit('generateSitemap')">Regenerar Sitemap</button>
</div>
