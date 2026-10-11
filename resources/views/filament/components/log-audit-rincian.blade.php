@php
    $rincian = $getState();
    $perubahan = is_array($rincian) && isset($rincian['perubahan']) && is_array($rincian['perubahan']) ? $rincian['perubahan'] : null;
    $sisaRincian = is_array($rincian) ? array_diff_key($rincian, ['perubahan' => true]) : [];
@endphp

<div class="space-y-4">
    @if(empty($rincian))
        <div class="flex items-center gap-2 rounded-lg border border-slate-700/60 bg-slate-900/40 p-4 text-sm text-slate-400">
            <x-heroicon-o-information-circle class="size-5 shrink-0 text-slate-500" />
            <span>Tidak ada metadata rincian tambahan untuk catatan audit ini.</span>
        </div>
    @else
        {{-- Jika terdapat riwayat perubahan nilai (Diff) --}}
        @if(!empty($perubahan))
            <div class="rounded-xl border border-amber-500/30 bg-amber-950/20 p-4">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-400">
                    <x-heroicon-o-arrows-right-left class="size-4" />
                    <span>Rekam Perubahan Nilai Atribut (Field Diffs)</span>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-700/80 bg-slate-950/70">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-900/80 text-slate-400">
                                <th class="px-3 py-2 font-medium">Atribut / Kolom</th>
                                <th class="px-3 py-2 font-medium">Nilai Sebelumnya</th>
                                <th class="px-3 py-2 font-medium">Nilai Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 font-mono">
                            @foreach($perubahan as $field => $diff)
                                @php
                                    $dari = is_array($diff) && array_key_exists('dari', $diff) ? $diff['dari'] : null;
                                    $menjadi = is_array($diff) && array_key_exists('menjadi', $diff) ? $diff['menjadi'] : null;
                                    $dariTeks = is_bool($dari) ? ($dari ? 'true' : 'false') : ($dari === null || $dari === '' ? '(kosong)' : (string)$dari);
                                    $menjadiTeks = is_bool($menjadi) ? ($menjadi ? 'true' : 'false') : ($menjadi === null || $menjadi === '' ? '(kosong)' : (string)$menjadi);
                                @endphp
                                <tr class="hover:bg-slate-900/50">
                                    <td class="px-3 py-2.5 font-sans font-medium text-slate-300">
                                        {{ str_replace('_', ' ', (string) $field) }}
                                    </td>
                                    <td class="px-3 py-2.5 text-rose-300/90">
                                        <span class="inline-block rounded bg-rose-950/40 px-1.5 py-0.5 border border-rose-900/50">
                                            {{ $dariTeks }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-emerald-300">
                                        <span class="inline-block rounded bg-emerald-950/40 px-1.5 py-0.5 border border-emerald-900/50 font-bold">
                                            {{ $menjadiTeks }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Metadata / Parameter Tambahan --}}
        @if(!empty($sisaRincian))
            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <x-heroicon-o-document-text class="size-4 text-slate-500" />
                    <span>Parameter & Rincian Kontekstual</span>
                </div>
                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    @foreach($sisaRincian as $key => $val)
                        <div class="flex flex-col gap-1 rounded-lg border border-slate-800/80 bg-slate-950/60 p-2.5">
                            <span class="text-[11px] font-medium uppercase tracking-wider text-slate-400">
                                {{ str_replace('_', ' ', (string) $key) }}
                            </span>
                            <span class="font-mono text-xs text-slate-200 break-all">
                                @if(is_array($val) || is_object($val))
                                    {{ json_encode($val, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                                @elseif(is_bool($val))
                                    {{ $val ? 'YA (True)' : 'TIDAK (False)' }}
                                @else
                                    {{ (string) $val }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Raw JSON View untuk Keperluan Audit Forensik --}}
        <details class="group rounded-xl border border-slate-800 bg-slate-950/40">
            <summary class="flex cursor-pointer items-center justify-between p-3 text-xs font-medium text-slate-400 transition hover:text-slate-200">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="size-4 text-slate-500" />
                    <span>Payload Mentah JSON (Forensik)</span>
                </span>
                <span class="text-[11px] text-slate-500 group-open:rotate-180 transition-transform">▼</span>
            </summary>
            <div class="border-t border-slate-800 p-3">
                <pre class="overflow-x-auto rounded bg-slate-950 p-3 font-mono text-[11px] text-slate-300 leading-relaxed max-h-60">{{ json_encode($rincian, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </details>
    @endif
</div>
