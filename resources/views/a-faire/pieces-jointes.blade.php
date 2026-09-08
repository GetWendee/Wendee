<div class="mt-4 pt-4 border-t border-gray-100">
    <div class="text-xs font-semibold text-gray-500 mb-2">Pièces jointes</div>
    @if($tache->piecesJointes->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-3">
        @foreach($tache->piecesJointes as $piece)
        <div class="relative group">
            @if(str_starts_with($piece->mime_type ?? '', 'image/'))
            <a href="{{ route('a-faire.pieces-jointes.show', $piece) }}" target="_blank">
                <img src="{{ route('a-faire.pieces-jointes.show', $piece) }}" alt="{{ $piece->nom_original }}" class="w-16 h-16 object-cover rounded-md border border-gray-200">
            </a>
            @else
            <a href="{{ route('a-faire.pieces-jointes.show', $piece) }}" target="_blank" class="flex items-center justify-center w-16 h-16 rounded-md border border-gray-200 bg-white text-[10px] text-gray-500 text-center px-1 leading-tight">{{ $piece->nom_original ?? 'Fichier' }}</a>
            @endif
            <form method="POST" action="{{ route('a-faire.pieces-jointes.destroy', $piece) }}" onsubmit="return confirm('Supprimer cette pièce jointe ?');" class="absolute -top-2 -right-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-5 h-5 rounded-full bg-red-600 text-white text-xs leading-none flex items-center justify-center">&times;</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
    <form method="POST" action="{{ route('a-faire.pieces-jointes.store', $tache) }}" enctype="multipart/form-data" x-ref="pjForm{{ $tache->id }}" class="flex items-center gap-2">
        @csrf
        <input type="file" name="fichier" x-ref="pjInput{{ $tache->id }}" class="hidden" @change="$refs['pjForm{{ $tache->id }}'].submit()">
        <div
            tabindex="0"
            @paste="
                let handled = false;
                for (const item of $event.clipboardData.items) {
                    if (item.type.startsWith('image/')) {
                        const file = item.getAsFile();
                        const dt = new DataTransfer();
                        dt.items.add(new File([file], 'capture-' + Date.now() + '.png', { type: file.type }));
                        $refs['pjInput{{ $tache->id }}'].files = dt.files;
                        $refs['pjForm{{ $tache->id }}'].submit();
                        handled = true;
                        break;
                    }
                }
                if (handled) { $event.preventDefault(); }
            "
            class="flex-1 text-xs text-gray-400 border border-dashed border-gray-300 rounded-md px-3 py-2 cursor-text focus:outline-none focus:border-pink-400 focus:text-gray-600"
        >Coller une capture d'écran ici (Ctrl+V)</div>
        <button type="button" @click="$refs['pjInput{{ $tache->id }}'].click()" class="px-3 py-2 text-xs font-semibold rounded-md border border-gray-300 text-gray-700 whitespace-nowrap">Choisir un fichier</button>
    </form>
</div>
