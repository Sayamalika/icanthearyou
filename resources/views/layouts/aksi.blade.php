<div class="aksi-group">
    @isset($detail)
        <a href="{{ $detail }}" class="btn btn-light btn-sm">Detail</a>
    @endisset

    @if (auth()->user()->idrole === 1)
        <a href="{{ $edit }}" class="btn btn-ghost btn-sm">Ubah</a>

        <form
            action="{{ $hapus }}"
            method="POST"
            class="form-inline"
            onsubmit="return confirm(@js('Hapus ' . $nama . '? Pastikan data ini tidak lagi diperlukan.'))"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
        </form>
    @endif
</div>
