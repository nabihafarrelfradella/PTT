@extends('mahasiswa.layout')

@section('title', 'Metode Pembayaran')
@section('header_title', 'Metode Pembayaran (Midtrans Auto-Debit)')
@section('header_subtitle', 'Kelola kartu debit bank Anda yang tertaut dengan otorisasi sidik jari')

@section('styles')
<style>
    .credit-card {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: white;
        border-radius: 16px;
        padding: 1.75rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 220px;
        position: relative;
        overflow: hidden;
    }
    .credit-card::before {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        top: -80px;
        right: -80px;
    }
    .card-chip {
        width: 45px;
        height: 32px;
        background: linear-gradient(135deg, #fcd34d, #f59e0b);
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    .card-number {
        font-family: monospace;
        font-size: 1.35rem;
        letter-spacing: 2px;
        font-weight: 600;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; align-items: start;">
    <!-- Visual Kartu Debit -->
    <div>
        <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--primary-color);">Kartu Debit Terdaftar</h3>
        @if($debit && $debit->is_active)
            <div class="credit-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; font-size: 1.2rem; letter-spacing: 1px;" id="cardBankDisplay">{{ $debit->bank_name }}</span>
                    <span style="font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 0.2rem 0.6rem; border-radius: 4px;">Midtrans 1-Click</span>
                </div>
                <div class="card-chip"></div>
                <div class="card-number" id="cardNumberDisplay">{{ $debit->card_token_masked }}</div>
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <div style="font-size: 0.7rem; opacity: 0.8; text-transform: uppercase;">Pemegang Kartu</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ $user->name }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; opacity: 0.8; text-transform: uppercase;">Status</div>
                        <div style="font-weight: 600; font-size: 0.85rem; color: #a7f3d0;">● Aktif Terhubung</div>
                    </div>
                </div>
            </div>
        @else
            <div style="background: #ffffff; border: 2px dashed #d1d5db; border-radius: 16px; padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 1rem; color: #9ca3af;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">Belum Ada Kartu Tertaut</h4>
                <p style="font-size: 0.85rem;">Isi formulir di samping untuk menautkan kartu debit bank Anda.</p>
            </div>
        @endif
    </div>

    <!-- Form Tautkan / Ganti Kartu -->
    <div style="background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary-color);">Tautkan / Perbarui Kartu Debit</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Kartu ini akan digunakan otomatis saat Anda menempelkan sidik jari di Kios POS.</p>

        <form action="{{ route('mahasiswa.pembayaran.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Pilih Bank</label>
                <select name="bank_name" class="form-control" id="bankSelect" required onchange="updateCardLive()">
                    <option value="Bank BCA" {{ ($debit->bank_name ?? '') === 'Bank BCA' ? 'selected' : '' }}>Bank BCA</option>
                    <option value="Bank Mandiri" {{ ($debit->bank_name ?? '') === 'Bank Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                    <option value="Bank BRI" {{ ($debit->bank_name ?? '') === 'Bank BRI' ? 'selected' : '' }}>Bank BRI</option>
                    <option value="Bank BNI" {{ ($debit->bank_name ?? '') === 'Bank BNI' ? 'selected' : '' }}>Bank BNI</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor Kartu Debit (16 Digit)</label>
                <input type="text" name="card_number" id="cardNumberInput" class="form-control" placeholder="4811 1111 1111 1234" maxlength="19" required oninput="updateCardLive()">
            </div>

            <div style="margin-bottom: 1.5rem; text-align: right;">
                <button type="button" onclick="autofillCard()" style="background: none; border: none; color: var(--accent-color); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                    Gunakan Nomor Kartu Sandbox Demo
                </button>
            </div>

            <button type="submit" class="action-btn" style="width: 100%; padding: 0.9rem;">
                Simpan & Tautkan Kartu ke Midtrans
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function formatCardDisplay(val) {
        let cleaned = val.replace(/\D/g, '');
        if (!cleaned) return '{{ $debit->card_token_masked ?? "4811 11** **** 1234" }}';
        let parts = [];
        for (let i = 0; i < cleaned.length && i < 16; i += 4) {
            parts.push(cleaned.substring(i, i + 4));
        }
        return parts.join(' ');
    }

    function updateCardLive() {
        const bank = document.getElementById('bankSelect').value;
        const numInput = document.getElementById('cardNumberInput').value;

        const bankElem = document.getElementById('cardBankDisplay');
        if (bankElem) bankElem.textContent = bank || 'Bank BCA';

        const numElem = document.getElementById('cardNumberDisplay');
        if (numElem) {
            if (numInput.trim()) {
                numElem.textContent = formatCardDisplay(numInput);
            } else {
                numElem.textContent = '{{ $debit->card_token_masked ?? "4811 11** **** 1234" }}';
            }
        }
    }

    function autofillCard() {
        const randomSuffix = Math.floor(1000 + Math.random() * 9000);
        const newCard = '4811 1111 1111 ' + randomSuffix;
        document.getElementById('cardNumberInput').value = newCard;
        document.getElementById('bankSelect').value = 'Bank BCA';
        updateCardLive();
    }

    // Auto format input with spaces every 4 digits while typing
    document.getElementById('cardNumberInput').addEventListener('input', function(e) {
        let val = this.value.replace(/\D/g, '').substring(0, 16);
        let formatted = '';
        for (let i = 0; i < val.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += val[i];
        }
        this.value = formatted;
        updateCardLive();
    });
</script>
@endsection
