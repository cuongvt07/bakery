<div class="p-4 space-y-4">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">🎫 Hỗ trợ</h1>
        <p class="text-amber-100">Gửi yêu cầu hỗ trợ cho admin</p>
    </div>

    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    {{-- Support Form --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Điểm bán *</label>
            <select wire:model="selectedAgencyId" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả yêu cầu khẩn cấp</label>
            <textarea wire:model="message" rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                placeholder="Mô tả chi tiết vấn đề cần hỗ trợ khẩn cấp từ điểm bán..."></textarea>
            @error('message')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button wire:click="submit"
            class="w-full btn-mobile bg-gradient-to-r from-red-500 to-red-600 text-white font-bold shadow-lg">
            🚨 Gửi Ticket Khẩn Cấp
        </button>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <div class="text-2xl">💡</div>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Lưu ý:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Admin sẽ phản hồi trong vòng 24h</li>
                    <li>Mô tả rõ ràng để được hỗ trợ nhanh hơn</li>
                    <li>Gọi hotline nếu cần hỗ trợ gấp</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Contact Info --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">📞 Liên hệ trực tiếp</h3>
        <div class="space-y-3">
            <a href="tel:0123456789" class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
                <div>
                    <div class="font-semibold text-gray-900">Hotline</div>
                    <div class="text-sm text-gray-600">0123 456 789</div>
                </div>
            </a>
        </div>
    </div>
</div>
