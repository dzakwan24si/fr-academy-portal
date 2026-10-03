<x-admin-layout>
    <x-slot name="title">Tambah Trainee</x-slot>
    <x-slot name="header">Tambah Trainee</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('training-non-reguler.trainees.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program Training</label>
                    <select name="training_program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asal Daerah (Origin)</label>
                    <input type="text" name="origin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Universitas</label>
                    <input type="text" name="university" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jurusan (Major)</label>
                    <input type="text" name="major" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="aktif">Aktif</option>
                        <option value="pasif">Pasif</option>
                        <option value="resign">Resign</option>
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mt-8 mb-4 border-b pb-2">Data OJT (Opsional)</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Region OJT</label>
                    <input type="text" name="ojt_region" placeholder="Misal: Kalimantan Barat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama PT (Perusahaan)</label>
                    <input type="text" name="ojt_company_name" placeholder="Misal: PT. Astra Agro" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Lokasi OJT</label>
                    <input type="text" name="ojt_location_code" placeholder="Misal: LOK-01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-[#28844b] text-white rounded-lg font-bold">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
