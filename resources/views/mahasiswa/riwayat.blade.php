@extends('mahasiswa.layout')

@section('title', 'Riwayat Belanja')
@section('header_title', 'Riwayat Belanja Kantin')
@section('header_subtitle', 'Daftar lengkap seluruh transaksi pembayaran biometrik Anda di Kantin PSDKU')

@section('content')
<!-- Ringkasan Statistik Belanja -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
        <span style="font-size: 0.8rem; color: var(--text-muted);">Total Belanja Berhasil</span>
        <h3 style="font-size: 1.4rem; color: var(--primary-color); margin-top: 4px;">
            Rp {{ number_format($transactions->where('status', 'success')->sum('total_amount'), 0, ',', '.') }}
        </h3>
    </div>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
        <span style="font-size: 0.8rem; color: var(--text-muted);">Jumlah Transaksi</span>
        <h3 style="font-size: 1.4rem; color: var(--primary-color); margin-top: 4px;">
            {{ $transactions->count() }} Kali
        </h3>
    </div>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
        <span style="font-size: 0.8rem; color: var(--text-muted);">Metode Pembayaran Utama</span>
        <h3 style="font-size: 1.4rem; color: {{ ($debit && $debit->is_active) ? 'var(--accent-color)' : 'var(--text-muted)' }}; margin-top: 4px;">
            {{ ($debit && $debit->is_active) ? $debit->bank_name : '-' }}
        </h3>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>Stand / Merchant</th>
                <th>Rincian Item</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td style="white-space: nowrap;">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                <td style="white-space: nowrap;">{{ $tx->transaction_code }}</td>
                <td style="white-space: nowrap;">{{ $tx->kantin->nama_kantin ?? 'Kantin PSDKU' }}</td>
                <td style="font-size: 0.85rem; color: var(--text-muted);">
                    @if($tx->details->count() > 0)
                        @foreach($tx->details as $item)
                            <div>{{ $item->menu->nama_menu ?? 'Menu' }} (x{{ $item->qty }})</div>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td style="font-weight: 600; white-space: nowrap;">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                <td style="white-space: nowrap;">
                    @if($tx->status === 'success')
                        <span class="status-badge active">Berhasil</span>
                    @elseif($tx->status === 'pending')
                        <span class="status-badge pending">Menunggu</span>
                    @elseif($tx->status === 'cancelled')
                        <span class="status-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dibatalkan</span>
                    @else
                        <span class="status-badge danger">Gagal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                    Belum ada riwayat transaksi belanja yang tercatat.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
