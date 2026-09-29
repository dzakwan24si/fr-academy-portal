<x-admin-layout>
    <x-slot name="title">Edit Trainee</x-slot>
    <x-slot name="header">Edit Trainee</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('training-non-reguler.trainees.update', $trainee->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" value="{{ $trainee->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program Training</label>
                    <select name="training_program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ $trainee->training_program_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asal Daerah</label>
                    <input type="text" name="origin" value="{{ $trainee->origin }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Universitas</label>
                    <input type="text" name="university" value="{{ $trainee->university }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                    <input type="text" name="major" value="{{ $trainee->major }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="aktif" {{ $trainee->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="pasif" {{ $trainee->status == 'pasif' ? 'selected' : '' }}>Pasif</option>
                        <option value="resign" {{ $trainee->status == 'resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mt-8 mb-4 border-b pb-2">Data OJT (Opsional)</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Region OJT</label>
                    <input type="text" name="ojt_region" value="{{ $trainee->ojt_region }}" placeholder="Misal: Kalimantan Barat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama PT (Perusahaan)</label>
                    <input type="text" name="ojt_company_name" value="{{ $trainee->ojt_company_name }}" placeholder="Misal: PT. Astra Agro" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Lokasi OJT</label>
                    <input type="text" name="ojt_location_code" value="{{ $trainee->ojt_location_code }}" placeholder="Misal: LOK-01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-[#28844b] text-white rounded-lg font-bold">Update</button>
                <a href="{{ route('training-non-reguler.trainees.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg font-bold">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
