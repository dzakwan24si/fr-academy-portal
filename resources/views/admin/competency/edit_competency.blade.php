<x-admin-layout>
    <x-slot name="title">Edit Uraian Kompetensi - {{ $competency->name }}</x-slot>
    <x-slot name="header">
        Edit Uraian Kompetensi No. {{ $competency->sort_order }}
    </x-slot>
    <x-slot name="headerSub">
        Kamus {{ $dictionary->name }} ({{ $dictionary->year }})
    </x-slot>
    <x-slot name="headerAction">
        <a href="{{ route('admin.competency.show', $dictionary) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Kamus
        </a>
    </x-slot>

    @php
        $knowledgeItems = $competency->itemsOfType(\App\Models\Competency\CompetencyItem::TYPE_KNOWLEDGE)->pluck('body')->all();
        $skillItems = $competency->itemsOfType(\App\Models\Competency\CompetencyItem::TYPE_SKILL)->pluck('body')->all();
        $devItems = $competency->itemsOfType(\App\Models\Competency\CompetencyItem::TYPE_DEVELOPMENT)->pluck('body')->all();
    @endphp

    <div x-data="{
        knowledgeList: {{ json_encode($knowledgeItems) }},
        skillList: {{ json_encode($skillItems) }},
        devList: {{ json_encode($devItems) }},

        addItem(type) {
            if (type === 'knowledge') this.knowledgeList.push('');
            if (type === 'skill') this.skillList.push('');
            if (type === 'development') this.devList.push('');
        },
        removeItem(type, index) {
            if (type === 'knowledge') this.knowledgeList.splice(index, 1);
            if (type === 'skill') this.skillList.splice(index, 1);
            if (type === 'development') this.devList.splice(index, 1);
        },
        moveUp(type, index) {
            if (index <= 0) return;
            let list = type === 'knowledge' ? this.knowledgeList : (type === 'skill' ? this.skillList : this.devList);
            let temp = list[index];
            list[index] = list[index - 1];
            list[index - 1] = temp;
        },
        moveDown(type, index) {
            let list = type === 'knowledge' ? this.knowledgeList : (type === 'skill' ? this.skillList : this.devList);
            if (index >= list.length - 1) return;
            let temp = list[index];
            list[index] = list[index + 1];
            list[index + 1] = temp;
        }
    }" class="space-y-8">

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3 shrink-0"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Terdapat kesalahan pengisian:</h4>
                        <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.competency.competencies.update', $competency) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- SECTION 1: INFORMASI UTAMA -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Informasi Utama Kompetensi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-12">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kompetensi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $competency->name) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-sm font-bold text-gray-900" required>
                    </div>

                    <div class="md:col-span-12">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Definisi Kompetensi</label>
                        <textarea name="definition" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm leading-relaxed" placeholder="Tuliskan definisi standar kompetensi ini...">{{ old('definition', $competency->definition) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: MATRIKS JABATAN X LEVEL 1-4 -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Matriks Proficiency Level (Jabatan &times; L1&ndash;L4)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Isi uraian kriteria performa untuk tiap jabatan pada level 1 (Basic) hingga level 4 (Expert).</p>
                </div>

                <div class="overflow-x-auto p-6">
                    @if($dictionary->positions->isEmpty())
                        <p class="text-sm text-gray-500 italic text-center py-4">Belum ada jabatan di kamus ini. Tambahkan jabatan terlebih dahulu pada halaman Kelola Kamus.</p>
                    @else
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-gray-100 text-xs font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">
                                    <th class="px-4 py-3 w-48 sticky left-0 z-10 bg-gray-100">Jabatan</th>
                                    @foreach(\App\Models\Competency\CompetencyDictionary::LEVEL_LABELS as $lvl => $label)
                                        <th class="px-4 py-3">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($dictionary->positions as $pos)
                                    <tr class="align-top hover:bg-gray-50/50">
                                        <td class="px-4 py-3 font-bold text-gray-800 sticky left-0 z-10 bg-white">
                                            {{ $pos->name }}
                                        </td>
                                        @foreach(\App\Models\Competency\CompetencyDictionary::LEVEL_LABELS as $lvl => $label)
                                            @php
                                                $desc = old("levels.{$pos->id}.{$lvl}", $levelMap->get("{$pos->id}-{$lvl}", ''));
                                            @endphp
                                            <td class="px-3 py-3">
                                                <textarea name="levels[{{ $pos->id }}][{{ $lvl }}]"
                                                          rows="3"
                                                          class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] text-xs leading-relaxed p-2.5 transition bg-gray-50/30 focus:bg-white"
                                                          placeholder="Uraian L{{ $lvl }} untuk {{ $pos->name }}...">{{ $desc }}</textarea>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- SECTION 3: BUTIR INDIKATOR (3 KATEGORI) -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- 1. Knowledge -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                Pengetahuan Wajib
                            </h4>
                            <button type="button" @click="addItem('knowledge')" class="px-3 py-1 bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold rounded-lg text-xs transition inline-flex items-center">
                                <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> Tambah
                            </button>
                        </div>

                        <template x-for="(item, idx) in knowledgeList" :key="idx">
                            <div class="flex items-start gap-2 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                                <span class="text-xs font-bold text-gray-400 mt-2.5 w-4 text-center" x-text="idx + 1"></span>
                                <textarea name="items[knowledge][]" x-model="knowledgeList[idx]" rows="2" class="w-full rounded-lg border-gray-300 text-xs p-2 focus:border-sky-500 focus:ring-sky-500" placeholder="Butir pengetahuan wajib..."></textarea>
                                <div class="flex flex-col gap-1 shrink-0">
                                    <button type="button" @click="moveUp('knowledge', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-up" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="moveDown('knowledge', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="removeItem('knowledge', idx)" class="p-1 text-red-500 hover:text-red-700"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                                </div>
                            </div>
                        </template>

                        <div x-show="knowledgeList.length === 0" class="text-xs text-gray-400 italic text-center py-4">
                            Belum ada butir pengetahuan.
                        </div>
                    </div>
                </div>

                <!-- 2. Skill -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Keterampilan Wajib
                            </h4>
                            <button type="button" @click="addItem('skill')" class="px-3 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-lg text-xs transition inline-flex items-center">
                                <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> Tambah
                            </button>
                        </div>

                        <template x-for="(item, idx) in skillList" :key="idx">
                            <div class="flex items-start gap-2 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                                <span class="text-xs font-bold text-gray-400 mt-2.5 w-4 text-center" x-text="idx + 1"></span>
                                <textarea name="items[skill][]" x-model="skillList[idx]" rows="2" class="w-full rounded-lg border-gray-300 text-xs p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Butir keterampilan wajib..."></textarea>
                                <div class="flex flex-col gap-1 shrink-0">
                                    <button type="button" @click="moveUp('skill', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-up" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="moveDown('skill', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="removeItem('skill', idx)" class="p-1 text-red-500 hover:text-red-700"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                                </div>
                            </div>
                        </template>

                        <div x-show="skillList.length === 0" class="text-xs text-gray-400 italic text-center py-4">
                            Belum ada butir keterampilan.
                        </div>
                    </div>
                </div>

                <!-- 3. Development -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Program Development
                            </h4>
                            <button type="button" @click="addItem('development')" class="px-3 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold rounded-lg text-xs transition inline-flex items-center">
                                <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> Tambah
                            </button>
                        </div>

                        <template x-for="(item, idx) in devList" :key="idx">
                            <div class="flex items-start gap-2 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                                <span class="text-xs font-bold text-gray-400 mt-2.5 w-4 text-center" x-text="idx + 1"></span>
                                <textarea name="items[development][]" x-model="devList[idx]" rows="2" class="w-full rounded-lg border-gray-300 text-xs p-2 focus:border-amber-500 focus:ring-amber-500" placeholder="Pilihan program development..."></textarea>
                                <div class="flex flex-col gap-1 shrink-0">
                                    <button type="button" @click="moveUp('development', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-up" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="moveDown('development', idx)" class="p-1 text-gray-400 hover:text-gray-700"><i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></button>
                                    <button type="button" @click="removeItem('development', idx)" class="p-1 text-red-500 hover:text-red-700"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                                </div>
                            </div>
                        </template>

                        <div x-show="devList.length === 0" class="text-xs text-gray-400 italic text-center py-4">
                            Belum ada program development.
                        </div>
                    </div>
                </div>

            </div>

            <!-- BUTTON SIMPAN -->
            <div class="sticky bottom-6 z-30 bg-white/95 backdrop-blur-md rounded-2xl border border-gray-200 shadow-xl p-4 flex items-center justify-between">
                <a href="{{ route('admin.competency.show', $dictionary) }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</a>
                <button type="submit" class="px-8 py-3 bg-[#509c38] text-white font-extrabold rounded-xl hover:bg-green-700 transition shadow-lg text-sm inline-flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan Uraian
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
