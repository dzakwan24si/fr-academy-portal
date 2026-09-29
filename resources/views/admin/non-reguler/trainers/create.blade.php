<x-admin-layout>
    <x-slot name="title">Tambah Trainer</x-slot>
    <x-slot name="header">Tambah Trainer</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('training-non-reguler.trainers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-[#28844b] text-white rounded-lg font-bold">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
