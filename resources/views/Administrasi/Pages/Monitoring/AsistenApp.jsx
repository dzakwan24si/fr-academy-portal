import React, { useState, useEffect, useMemo } from 'react';
import {
    Users, UserCheck, UserX, Award, Briefcase, FileSpreadsheet, Plus,
    Upload, Download, Search, RefreshCw, Eye, Edit2, Trash2, X, Check,
    AlertCircle, ChevronLeft, ChevronRight, ChevronUp, ChevronDown,
    Building2, MapPin, GraduationCap, Calendar, ShieldCheck, HeartPulse,
    AlertTriangle, FileText, CheckCircle2, Phone, Mail, Home, CreditCard
} from 'lucide-react';

const ASISTEN_SECTIONS = [
    {
        id: 'identitas',
        title: 'Identitas Pribadi',
        icon: Users,
        color: 'text-blue-600 bg-blue-50 border-blue-200',
        fields: [
            { key: 'nik', label: 'NIK / ID Karyawan', type: 'text' },
            { key: 'nama', label: 'Nama Lengkap', type: 'text', required: true },
            { key: 'jenis_kelamin', label: 'Jenis Kelamin', type: 'select', options: ['', 'Laki-laki', 'Perempuan'] },
            { key: 'tempat_lahir', label: 'Tempat Lahir', type: 'text' },
            { key: 'tanggal_lahir', label: 'Tanggal Lahir', type: 'date' },
            { key: 'usia', label: 'Usia (Tahun)', type: 'number' },
            { key: 'agama', label: 'Agama', type: 'text' },
            { key: 'status_pernikahan', label: 'Status Pernikahan', type: 'select', options: ['', 'Lajang', 'Menikah', 'Cerai'] },
            { key: 'no_hp', label: 'Nomor HP', type: 'text' },
            { key: 'email', label: 'Email', type: 'email' },
            { key: 'alamat_domisili', label: 'Alamat Domisili', type: 'textarea' },
            { key: 'no_ktp', label: 'Nomor KTP (NIK KTP)', type: 'text' },
        ]
    },
    {
        id: 'pendidikan',
        title: 'Pendidikan',
        icon: GraduationCap,
        color: 'text-indigo-600 bg-indigo-50 border-indigo-200',
        fields: [
            { key: 'jenjang_pendidikan', label: 'Jenjang Pendidikan', type: 'select', options: ['', 'D3', 'D4', 'S1', 'S2', 'SMK/SMA'] },
            { key: 'universitas', label: 'Universitas / Perguruan Tinggi', type: 'text' },
            { key: 'fakultas', label: 'Fakultas', type: 'text' },
            { key: 'jurusan', label: 'Jurusan / Program Studi', type: 'text' },
            { key: 'ipk', label: 'IPK (Skala 4.00)', type: 'number', step: '0.01' },
            { key: 'tahun_lulus', label: 'Tahun Kelulusan Kuliah', type: 'number' },
        ]
    },
    {
        id: 'rekrutmen',
        title: 'Rekrutmen & Program Training',
        icon: Briefcase,
        color: 'text-emerald-600 bg-emerald-50 border-emerald-200',
        fields: [
            { key: 'asal_rekrut', label: 'Asal Rekrut / Lokasi', type: 'text' },
            { key: 'jalur_rekrutmen', label: 'Jalur Rekrutmen', type: 'text' },
            { key: 'program', label: 'Nama Program Training', type: 'text' },
            { key: 'batch', label: 'Batch / Gelombang', type: 'text' },
            { key: 'tanggal_mulai_training', label: 'Tanggal Mulai Training', type: 'date' },
            { key: 'tanggal_selesai_training', label: 'Tanggal Selesai Training', type: 'date' },
            { key: 'tahun_angkatan', label: 'Tahun Angkatan', type: 'number' },
        ]
    },
    {
        id: 'penilaian_seleksi',
        title: 'Penilaian Seleksi',
        icon: ShieldCheck,
        color: 'text-amber-600 bg-amber-50 border-amber-200',
        fields: [
            { key: 'nilai_psikotest', label: 'Nilai Psikotest', type: 'number', step: '0.01' },
            { key: 'nilai_interview_hr', label: 'Nilai Interview HR', type: 'number', step: '0.01' },
            { key: 'nilai_interview_user', label: 'Nilai Interview User', type: 'number', step: '0.01' },
            { key: 'status_mcu', label: 'Status / Hasil MCU', type: 'select', options: ['', 'Fit', 'Fit with Note', 'Unfit', 'Pending'] },
            { key: 'ranking_seleksi', label: 'Ranking Seleksi', type: 'number' },
        ]
    },
    {
        id: 'kelulusan_training',
        title: 'Progress & Kelulusan Training',
        icon: Award,
        color: 'text-purple-600 bg-purple-50 border-purple-200',
        fields: [
            { key: 'nilai_akademik', label: 'Nilai Akademik (Teori)', type: 'number', step: '0.01' },
            { key: 'nilai_praktek', label: 'Nilai Praktek (Lapangan)', type: 'number', step: '0.01' },
            { key: 'nilai_sikap', label: 'Nilai Sikap / Attitude', type: 'number', step: '0.01' },
            { key: 'nilai_akhir_training', label: 'Nilai Akhir Training', type: 'number', step: '0.01' },
            { key: 'status_kelulusan', label: 'Status Kelulusan', type: 'select', options: ['', 'Lulus', 'Tidak Lulus', 'Training', 'Resign'] },
            { key: 'tanggal_kelulusan', label: 'Tanggal Kelulusan', type: 'date' },
        ]
    },
    {
        id: 'penempatan_awal',
        title: 'Penempatan Awal (Setelah Training)',
        icon: Building2,
        color: 'text-cyan-600 bg-cyan-50 border-cyan-200',
        fields: [
            { key: 'jabatan_awal', label: 'Jabatan Awal (Setelah Training)', type: 'text' },
            { key: 'pt_penempatan', label: 'Penempatan PT', type: 'text' },
            { key: 'regional_penempatan_training', label: 'Regional (Penempatan Training)', type: 'text' },
            { key: 'unit_kerja_penempatan_training', label: 'Unit Kerja (Penempatan Training)', type: 'text' },
            { key: 'tanggal_sk_awal', label: 'Tanggal SK Awal', type: 'date' },
        ]
    },
    {
        id: 'kepegawaian_saatini',
        title: 'Kepegawaian Saat Ini',
        icon: UserCheck,
        color: 'text-[#4B6A34] bg-green-50 border-green-200',
        fields: [
            { key: 'status_karyawan', label: 'Status Karyawan', type: 'select', options: ['', 'PKWTT', 'PKWT', 'Trainee', 'Resign', 'PHK'] },
            { key: 'jabatan_terakhir', label: 'Jabatan Terakhir / Saat Ini', type: 'text' },
            { key: 'level_jabatan', label: 'Level / Golongan Jabatan', type: 'text' },
            { key: 'region_saat_ini', label: 'Region (Kepegawaian Saat Ini)', type: 'text' },
            { key: 'unit_kerja_saat_ini', label: 'Unit Kerja (Kepegawaian Saat Ini)', type: 'text' },
            { key: 'atasan_langsung', label: 'Atasan Langsung', type: 'text' },
            { key: 'tanggal_pengangkatan', label: 'Tanggal Pengangkatan Karyawan', type: 'date' },
        ]
    },
    {
        id: 'resign_ijazah',
        title: 'Resign & Ijazah',
        icon: UserX,
        color: 'text-rose-600 bg-rose-50 border-rose-200',
        fields: [
            { key: 'status_resign', label: 'Status Resign', type: 'select', options: ['', 'Aktif', 'Resign', 'PHK'] },
            { key: 'tanggal_resign', label: 'Tanggal Resign', type: 'date' },
            { key: 'alasan_resign', label: 'Alasan Resign', type: 'textarea' },
            { key: 'status_ijazah', label: 'Status Ijazah', type: 'select', options: ['', 'Ditahan', 'Sudah Diambil', 'Belum Diserahkan'] },
            { key: 'tanggal_pengambilan_ijazah', label: 'Tanggal Pengambilan Ijazah', type: 'date' },
            { key: 'no_tanda_terima_ijazah', label: 'No Tanda Terima Ijazah', type: 'text' },
        ]
    },
    {
        id: 'lainnya',
        title: 'Lainnya & Dokumen',
        icon: FileText,
        color: 'text-slate-600 bg-slate-50 border-slate-200',
        fields: [
            { key: 'no_bpjs_tk', label: 'No BPJS Ketenagakerjaan', type: 'text' },
            { key: 'no_bpjs_kes', label: 'No BPJS Kesehatan', type: 'text' },
            { key: 'npwp', label: 'Nomor NPWP', type: 'text' },
            { key: 'keterangan', label: 'Keterangan Status', type: 'select', options: ['', 'Aktif', 'Resign', 'Gagal'] },
        ]
    }
];

const INITIAL_FORM = ASISTEN_SECTIONS.reduce((acc, sec) => {
    sec.fields.forEach(f => {
        acc[f.key] = '';
    });
    return acc;
}, { no: '' });

export default function AsistenApp() {
    // State
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [summary, setSummary] = useState({
        total_asisten: 0,
        total_aktif: 0,
        total_resign: 0,
        total_gagal: 0
    });
    const [filterOptions, setFilterOptions] = useState({
        asal_rekrut: [],
        program: [],
        region_saat_ini: [],
        status_karyawan: [],
        level_jabatan: [],
        keterangan: []
    });

    // Filters
    const [selectedAsal, setSelectedAsal] = useState('all');
    const [selectedProgram, setSelectedProgram] = useState('all');
    const [selectedRegion, setSelectedRegion] = useState('all');
    const [selectedStatus, setSelectedStatus] = useState('all');
    const [selectedLevel, setSelectedLevel] = useState('all');
    const [selectedKeterangan, setSelectedKeterangan] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');

    // Pagination & Sorting
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(25);
    const [pagination, setPagination] = useState({ total: 0, last_page: 1 });
    const [sortBy, setSortBy] = useState('no');
    const [sortOrder, setSortOrder] = useState('asc');

    // Modals
    const [detailModalOpen, setDetailModalOpen] = useState(false);
    const [selectedAsisten, setSelectedAsisten] = useState(null);
    const [formModalOpen, setFormModalOpen] = useState(false);
    const [formMode, setFormMode] = useState('create'); // 'create' | 'edit'
    const [formData, setFormData] = useState(INITIAL_FORM);
    const [activeFormTab, setActiveFormTab] = useState('identitas');
    const [formErrors, setFormErrors] = useState({});
    const [submitting, setSubmitting] = useState(false);

    // Import / Export Modals
    const [importModalOpen, setImportModalOpen] = useState(false);
    const [importFile, setImportFile] = useState(null);
    const [importMode, setImportMode] = useState('append'); // 'append' | 'replace'
    const [importing, setImporting] = useState(false);
    const [importResult, setImportResult] = useState(null);
    const [exportDropdownOpen, setExportDropdownOpen] = useState(false);
    const [templateDropdownOpen, setTemplateDropdownOpen] = useState(false);

    // Delete Confirmation
    const [deleteConfirmOpen, setDeleteConfirmOpen] = useState(false);
    const [itemToDelete, setItemToDelete] = useState(null);

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Fetch Data
    const fetchData = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: perPage,
                sort_by: sortBy,
                sort_order: sortOrder,
                asal_rekrut: selectedAsal,
                program: selectedProgram,
                region_saat_ini: selectedRegion,
                status_karyawan: selectedStatus,
                level_jabatan: selectedLevel,
                keterangan: selectedKeterangan,
                search: searchQuery
            });

            const res = await fetch(`/administrasi/asisten/api/data?${params.toString()}`);
            const result = await res.json();

            if (result.status === 'success') {
                setData(result.data || []);
                setPagination(result.pagination || { total: 0, last_page: 1 });
                setSummary(result.summary || {});
                setFilterOptions(result.filters || {
                    asal_rekrut: [],
                    program: [],
                    region_saat_ini: [],
                    status_karyawan: [],
                    level_jabatan: [],
                    keterangan: []
                });
            }
        } catch (err) {
            console.error('Error fetching asisten data:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, [currentPage, perPage, sortBy, sortOrder, selectedAsal, selectedProgram, selectedRegion, selectedStatus, selectedLevel, selectedKeterangan]);

    // Handle search with debounce / enter
    const handleSearchSubmit = (e) => {
        e.preventDefault();
        setCurrentPage(1);
        fetchData();
    };

    const handleResetFilters = () => {
        setSelectedAsal('all');
        setSelectedProgram('all');
        setSelectedRegion('all');
        setSelectedStatus('all');
        setSelectedLevel('all');
        setSelectedKeterangan('all');
        setSearchQuery('');
        setCurrentPage(1);
    };

    // Sort Handler
    const handleSort = (column) => {
        if (sortBy === column) {
            setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(column);
            setSortOrder('asc');
        }
    };

    // Detail Modal Handlers
    const openDetailModal = async (item) => {
        setSelectedAsisten(item);
        setDetailModalOpen(true);
        try {
            const res = await fetch(`/administrasi/asisten/api/${item.id}`);
            const result = await res.json();
            if (result.status === 'success') {
                setSelectedAsisten(result.data);
            }
        } catch (err) {
            console.error('Error fetching detail:', err);
        }
    };

    // Create / Edit Handlers
    const openCreateModal = () => {
        setFormMode('create');
        setFormData(INITIAL_FORM);
        setFormErrors({});
        setActiveFormTab('identitas');
        setFormModalOpen(true);
    };

    const openEditModal = (item) => {
        setFormMode('edit');
        const populated = { ...INITIAL_FORM };
        Object.keys(populated).forEach(k => {
            populated[k] = item[k] !== null && item[k] !== undefined ? item[k] : '';
        });
        populated.id = item.id;
        setFormData(populated);
        setFormErrors({});
        setActiveFormTab('identitas');
        setFormModalOpen(true);
    };

    const handleFormChange = (key, value) => {
        setFormData(prev => ({ ...prev, [key]: value }));
        if (formErrors[key]) {
            setFormErrors(prev => {
                const next = { ...prev };
                delete next[key];
                return next;
            });
        }
    };

    const handleFormSubmit = async (e) => {
        e.preventDefault();
        if (!formData.nama || formData.nama.trim() === '') {
            setFormErrors({ nama: ['Nama wajib diisi'] });
            setActiveFormTab('identitas');
            return;
        }

        setSubmitting(true);
        setFormErrors({});

        try {
            const url = formMode === 'create'
                ? '/administrasi/asisten/api/store'
                : `/administrasi/asisten/api/${formData.id}`;

            const method = formMode === 'create' ? 'POST' : 'PUT';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await res.json();
            if (result.status === 'success') {
                setFormModalOpen(false);
                fetchData();
                if (detailModalOpen && selectedAsisten && selectedAsisten.id === formData.id) {
                    setSelectedAsisten(result.data);
                }
            } else if (result.errors) {
                setFormErrors(result.errors);
            }
        } catch (err) {
            console.error('Error saving data:', err);
        } finally {
            setSubmitting(false);
        }
    };

    // Delete Handlers
    const confirmDelete = (item) => {
        setItemToDelete(item);
        setDeleteConfirmOpen(true);
    };

    const handleDelete = async () => {
        if (!itemToDelete) return;
        try {
            const res = await fetch(`/administrasi/asisten/api/${itemToDelete.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                }
            });
            const result = await res.json();
            if (result.status === 'success') {
                setDeleteConfirmOpen(false);
                setItemToDelete(null);
                if (detailModalOpen && selectedAsisten?.id === itemToDelete.id) {
                    setDetailModalOpen(false);
                }
                fetchData();
            }
        } catch (err) {
            console.error('Error deleting:', err);
        }
    };

    // Import CSV Handler
    const handleImportSubmit = async (e) => {
        e.preventDefault();
        if (!importFile) return;

        setImporting(true);
        setImportResult(null);

        const form = new FormData();
        form.append('file', importFile);
        form.append('mode', importMode);

        try {
            const res = await fetch('/administrasi/asisten/api/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: form
            });

            const result = await res.json();
            setImportResult(result);
            if (result.status === 'success') {
                fetchData();
            }
        } catch (err) {
            setImportResult({
                status: 'error',
                message: 'Terjadi kesalahan saat mengunggah file.'
            });
        } finally {
            setImporting(false);
        }
    };

    // Export CSV Trigger
    const handleExport = (type) => {
        setExportDropdownOpen(false);
        const params = new URLSearchParams({
            type: type,
            asal_rekrut: selectedAsal,
            program: selectedProgram,
            region_saat_ini: selectedRegion,
            status_karyawan: selectedStatus,
            level_jabatan: selectedLevel,
            search: searchQuery
        });
        window.location.href = `/administrasi/asisten/api/export?${params.toString()}`;
    };

    // Download Template Excel Trigger
    const handleDownloadTemplate = (type = 'lengkap') => {
        setTemplateDropdownOpen(false);
        window.location.href = `/administrasi/asisten/template?type=${type}`;
    };

    return (
        <div className="space-y-6">
            {/* Header Banner */}
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div className="flex items-center gap-3 mb-1">
                        <div className="w-10 h-10 rounded-xl bg-emerald-100 text-[#4B6A34] flex items-center justify-center font-bold">
                            <Users className="w-6 h-6" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-gray-900 tracking-tight">Database Asisten & Trainee</h1>
                            <p className="text-xs text-gray-500 font-medium">Manajemen data lengkap 56 kolom peserta & kepegawaian alumni training</p>
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-2.5">
                    {/* Template Excel Dropdown */}
                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => {
                                setTemplateDropdownOpen(!templateDropdownOpen);
                                setExportDropdownOpen(false);
                            }}
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition shadow-xs"
                            title="Unduh format template Excel untuk Database Asisten"
                        >
                            <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                            <span>Template Excel</span>
                            <ChevronDown className="w-3.5 h-3.5 text-gray-400" />
                        </button>

                        {templateDropdownOpen && (
                            <div className="absolute right-0 mt-1.5 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-30 py-1 text-sm animate-in fade-in">
                                <button
                                    type="button"
                                    onClick={() => handleDownloadTemplate('lengkap')}
                                    className="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-emerald-50 hover:text-[#4B6A34] flex items-center gap-2.5 font-medium transition"
                                >
                                    <FileSpreadsheet className="w-4 h-4 text-emerald-600 shrink-0" />
                                    <div>
                                        <p className="font-semibold text-xs">Template Lengkap (56 Kolom)</p>
                                        <p className="text-[11px] text-gray-400">Seluruh atribut master data & contoh</p>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => handleDownloadTemplate('ringkas')}
                                    className="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-emerald-50 hover:text-[#4B6A34] flex items-center gap-2.5 font-medium transition border-t border-gray-100"
                                >
                                    <FileText className="w-4 h-4 text-blue-600 shrink-0" />
                                    <div>
                                        <p className="font-semibold text-xs">Template Ringkas (12 Kolom)</p>
                                        <p className="text-[11px] text-gray-400">Format data pokok & penempatan</p>
                                    </div>
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Export Dropdown */}
                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => setExportDropdownOpen(!exportDropdownOpen)}
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition shadow-xs"
                        >
                            <Download className="w-4 h-4 text-gray-500" />
                            <span>Export Excel</span>
                            <ChevronDown className="w-3.5 h-3.5 text-gray-400" />
                        </button>

                        {exportDropdownOpen && (
                            <div className="absolute right-0 mt-1.5 w-60 bg-white border border-gray-200 rounded-xl shadow-lg z-30 py-1 text-sm animate-in fade-in">
                                <button
                                    type="button"
                                    onClick={() => handleExport('ringkas')}
                                    className="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-emerald-50 hover:text-[#4B6A34] flex items-center gap-2.5 font-medium transition"
                                >
                                    <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                                    <div>
                                        <p className="font-semibold text-xs">Ekspor Ringkas (8 Kolom)</p>
                                        <p className="text-[11px] text-gray-400">Kolom utama sesuai tampilan tabel</p>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => handleExport('lengkap')}
                                    className="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-emerald-50 hover:text-[#4B6A34] flex items-center gap-2.5 font-medium transition border-t border-gray-100"
                                >
                                    <FileText className="w-4 h-4 text-blue-600" />
                                    <div>
                                        <p className="font-semibold text-xs">Ekspor Lengkap (56 Kolom)</p>
                                        <p className="text-[11px] text-gray-400">Seluruh atribut master data</p>
                                    </div>
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Import CSV */}
                    <button
                        type="button"
                        onClick={() => {
                            setImportFile(null);
                            setImportResult(null);
                            setImportModalOpen(true);
                        }}
                        className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition shadow-xs"
                    >
                        <Upload className="w-4 h-4 text-gray-500" />
                        <span>Import CSV</span>
                    </button>

                    {/* Add Single Data */}
                    <button
                        type="button"
                        onClick={openCreateModal}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#4B6A34] text-white text-sm font-semibold hover:bg-[#3D572A] transition shadow-xs"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Tambah Data</span>
                    </button>
                </div>
            </div>

            {/* KPI Summary Cards (4 Cards) */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {/* 1. Total Database */}
                <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs">
                    <div className="flex items-center justify-between mb-1.5">
                        <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Database</span>
                        <div className="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <Users className="w-4 h-4" />
                        </div>
                    </div>
                    <p className="text-2xl font-bold text-gray-900">{summary.total_asisten?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-gray-400 mt-0.5">Seluruh asisten & trainee</p>
                </div>

                {/* 2. Aktif Bekerja / Eksis */}
                <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs">
                    <div className="flex items-center justify-between mb-1.5">
                        <span className="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Aktif Bekerja / Eksis</span>
                        <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <UserCheck className="w-4 h-4" />
                        </div>
                    </div>
                    <p className="text-2xl font-bold text-emerald-700">{summary.total_aktif?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-emerald-600/70 mt-0.5">Status karyawan aktif</p>
                </div>

                {/* 3. Resign / PHK */}
                <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs">
                    <div className="flex items-center justify-between mb-1.5">
                        <span className="text-xs font-semibold text-rose-600 uppercase tracking-wider">Resign / PHK</span>
                        <div className="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <UserX className="w-4 h-4" />
                        </div>
                    </div>
                    <p className="text-2xl font-bold text-rose-700">{summary.total_resign?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-rose-600/70 mt-0.5">Asisten Resign</p>
                </div>

                {/* 4. Gagal */}
                <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs">
                    <div className="flex items-center justify-between mb-1.5">
                        <span className="text-xs font-semibold text-amber-600 uppercase tracking-wider">Gagal</span>
                        <div className="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <AlertTriangle className="w-4 h-4" />
                        </div>
                    </div>
                    <p className="text-2xl font-bold text-amber-700">{summary.total_gagal?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-amber-600/70 mt-0.5">Asisten yang gagal</p>
                </div>
            </div>

            {/* Filter Bar */}
            <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                <form onSubmit={handleSearchSubmit} className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <div className="relative flex-1">
                        <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Cari nama asisten, NIK, program, unit kerja, PT..."
                            className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#4B6A34] focus:bg-white transition"
                        />
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            type="submit"
                            className="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 transition"
                        >
                            Cari
                        </button>
                        <button
                            type="button"
                            onClick={handleResetFilters}
                            className="px-3 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition flex items-center gap-1.5"
                            title="Reset Filter"
                        >
                            <RefreshCw className="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>
                    </div>
                </form>

                {/* Filter Dropdowns */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-gray-100">
                    <div>
                        <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Asal Rekrut</label>
                        <select
                            value={selectedAsal}
                            onChange={(e) => { setSelectedAsal(e.target.value); setCurrentPage(1); }}
                            className="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-[#4B6A34] focus:outline-none"
                        >
                            <option value="all">Semua Asal Rekrut ({filterOptions.asal_rekrut?.length || 0})</option>
                            {filterOptions.asal_rekrut?.map((loc) => (
                                <option key={loc} value={loc}>{loc}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Program Training</label>
                        <select
                            value={selectedProgram}
                            onChange={(e) => { setSelectedProgram(e.target.value); setCurrentPage(1); }}
                            className="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-[#4B6A34] focus:outline-none"
                        >
                            <option value="all">Semua Program ({filterOptions.program?.length || 0})</option>
                            {filterOptions.program?.map((prog) => (
                                <option key={prog} value={prog}>{prog}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Region Saat Ini</label>
                        <select
                            value={selectedRegion}
                            onChange={(e) => { setSelectedRegion(e.target.value); setCurrentPage(1); }}
                            className="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-[#4B6A34] focus:outline-none"
                        >
                            <option value="all">Semua Region ({filterOptions.region_saat_ini?.length || 0})</option>
                            {filterOptions.region_saat_ini?.map((reg) => (
                                <option key={reg} value={reg}>{reg}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Level / Jabatan</label>
                        <select
                            value={selectedLevel}
                            onChange={(e) => { setSelectedLevel(e.target.value); setCurrentPage(1); }}
                            className="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-[#4B6A34] focus:outline-none"
                        >
                            <option value="all">Semua Level ({filterOptions.level_jabatan?.length || 0})</option>
                            {filterOptions.level_jabatan?.map((lvl) => (
                                <option key={lvl} value={lvl}>{lvl}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Keterangan</label>
                        <select
                            value={selectedKeterangan}
                            onChange={(e) => { setSelectedKeterangan(e.target.value); setCurrentPage(1); }}
                            className="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-[#4B6A34] focus:outline-none"
                        >
                            <option value="all">Semua Keterangan ({filterOptions.keterangan?.length || 0})</option>
                            {filterOptions.keterangan?.map((ket) => (
                                <option key={ket} value={ket}>{ket}</option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>

            {/* Main Table: Strictly 8 Columns */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <div className="flex items-center gap-2">
                        <span className="text-sm font-bold text-gray-800">Daftar Asisten & Trainee</span>
                        <span className="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full font-semibold">
                            {pagination.total} Data Ditemukan
                        </span>
                    </div>

                    <div className="flex items-center gap-3">
                        <label className="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                            Tampilkan:
                            <select
                                value={perPage}
                                onChange={(e) => { setPerPage(Number(e.target.value)); setCurrentPage(1); }}
                                className="px-2 py-1 bg-gray-50 border border-gray-300 rounded-md text-xs font-semibold focus:outline-none"
                            >
                                <option value={10}>10</option>
                                <option value={25}>25</option>
                                <option value={50}>50</option>
                                <option value={100}>100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase tracking-wider font-bold select-none">
                            <tr>
                                <th className="px-4 py-3.5 text-center w-12">No</th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('nama')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Nama</span>
                                        {sortBy === 'nama' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('asal_rekrut')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Asal Rekrut</span>
                                        {sortBy === 'asal_rekrut' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('program')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Program</span>
                                        {sortBy === 'program' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('jabatan_awal')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Jabatan (Awal)</span>
                                        {sortBy === 'jabatan_awal' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('level_jabatan')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Level Jabatan</span>
                                        {sortBy === 'level_jabatan' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('jabatan_terakhir')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Jabatan (Saat Ini)</span>
                                        {sortBy === 'jabatan_terakhir' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('pt_penempatan')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Penempatan (PT)</span>
                                        {sortBy === 'pt_penempatan' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th
                                    className="px-4 py-3.5 cursor-pointer hover:bg-gray-100 transition"
                                    onClick={() => handleSort('region_saat_ini')}
                                >
                                    <div className="flex items-center gap-1.5">
                                        <span>Region (Saat Ini)</span>
                                        {sortBy === 'region_saat_ini' && (sortOrder === 'asc' ? <ChevronUp className="w-3.5 h-3.5 text-[#4B6A34]" /> : <ChevronDown className="w-3.5 h-3.5 text-[#4B6A34]" />)}
                                    </div>
                                </th>
                                <th className="px-4 py-3.5">
                                    <span>Unit Kerja</span>
                                </th>
                                <th className="px-4 py-3.5">
                                    <span>Keterangan</span>
                                </th>
                                <th className="px-4 py-3.5 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {loading ? (
                                <tr>
                                    <td colSpan={12} className="px-4 py-12 text-center text-gray-500">
                                        <RefreshCw className="w-6 h-6 text-[#4B6A34] animate-spin mx-auto mb-2" />
                                        <span>Memuat data asisten & trainee...</span>
                                    </td>
                                </tr>
                            ) : data.length === 0 ? (
                                <tr>
                                    <td colSpan={12} className="px-4 py-12 text-center text-gray-400">
                                        <AlertCircle className="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                        <p className="font-semibold text-gray-600">Tidak ada data asisten yang sesuai kriteria.</p>
                                        <p className="text-[11px] text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter.</p>
                                    </td>
                                </tr>
                            ) : (
                                data.map((item, idx) => {
                                    const rowNumber = (currentPage - 1) * perPage + (idx + 1);
                                    const unitDisplay = item.unit_kerja_display || '-';
                                    const isFallback = item.is_unit_kerja_fallback;

                                    return (
                                        <tr key={item.id} className="hover:bg-gray-50/80 transition-colors">
                                            <td className="px-4 py-3 text-center text-gray-400 font-mono text-[11px]">
                                                {rowNumber}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="font-bold text-gray-900 text-xs">{item.nama}</div>
                                                {item.nik && (
                                                    <div className="text-[11px] text-gray-400 font-mono">{item.nik}</div>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700 font-medium">
                                                {item.asal_rekrut || '-'}
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="font-semibold text-gray-800">{item.program || '-'}</span>
                                                {item.batch && (
                                                    <span className="text-[10px] text-gray-400 block">Batch {item.batch}</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700">
                                                {item.jabatan_awal || '-'}
                                            </td>
                                            <td className="px-4 py-3">
                                                {item.level_jabatan ? (
                                                    <span className="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                        {item.level_jabatan}
                                                    </span>
                                                ) : (
                                                    <span className="text-gray-400">-</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700 font-medium">
                                                {item.jabatan_terakhir || '-'}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700 font-medium">
                                                {item.pt_penempatan || '-'}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700">
                                                {item.region_saat_ini || '-'}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="font-semibold text-gray-800">{unitDisplay}</div>
                                                {isFallback && (
                                                    <span className="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded mt-0.5">
                                                        [Penempatan Awal]
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                {item.keterangan === 'Resign' ? (
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                                        Resign
                                                    </span>
                                                ) : item.keterangan === 'Gagal' ? (
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        Gagal
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Aktif
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-center">
                                                <div className="flex items-center justify-center gap-1">
                                                    <button
                                                        type="button"
                                                        onClick={() => openDetailModal(item)}
                                                        className="px-2.5 py-1 bg-emerald-50 text-[#4B6A34] hover:bg-emerald-100 font-bold text-[11px] rounded-lg transition inline-flex items-center gap-1 border border-emerald-200"
                                                        title="Lihat Detail Kartu"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                        <span>Detail</span>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => openEditModal(item)}
                                                        className="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition"
                                                        title="Edit Data"
                                                    >
                                                        <Edit2 className="w-3.5 h-3.5" />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => confirmDelete(item)}
                                                        className="p-1 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition"
                                                        title="Hapus Data"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination Controls */}
                {pagination.last_page > 1 && (
                    <div className="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600 flex-wrap gap-2">
                        <div>
                            Halaman <span className="font-bold text-gray-900">{pagination.current_page}</span> dari{' '}
                            <span className="font-bold text-gray-900">{pagination.last_page}</span> (Total {pagination.total} data)
                        </div>
                        <div className="flex items-center gap-1.5">
                            <button
                                type="button"
                                disabled={pagination.current_page <= 1}
                                onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                                className="px-2.5 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-transparent font-medium transition inline-flex items-center gap-1"
                            >
                                <ChevronLeft className="w-3.5 h-3.5" />
                                <span>Sebelumnya</span>
                            </button>

                            <span className="px-2 font-mono font-bold text-gray-700">
                                {pagination.current_page} / {pagination.last_page}
                            </span>

                            <button
                                type="button"
                                disabled={pagination.current_page >= pagination.last_page}
                                onClick={() => setCurrentPage(prev => Math.min(pagination.last_page, prev + 1))}
                                className="px-2.5 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-transparent font-medium transition inline-flex items-center gap-1"
                            >
                                <span>Berikutnya</span>
                                <ChevronRight className="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {/* ========================================================================= */}
            {/* DETAIL MODAL (CARD FORMAT - 9 STRUCTURED CARD SECTIONS)                  */}
            {/* ========================================================================= */}
            {detailModalOpen && selectedAsisten && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 overflow-y-auto animate-in fade-in">
                    <div className="bg-gray-50 rounded-3xl max-w-5xl w-full border border-gray-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        {/* Modal Header */}
                        <div className="bg-white px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4 sticky top-0 z-10">
                            <div className="flex items-center gap-3.5">
                                <div className="w-12 h-12 rounded-2xl bg-[#4B6A34] text-white flex items-center justify-center font-bold text-base shadow-sm">
                                    {selectedAsisten.nama ? selectedAsisten.nama.substring(0, 2).toUpperCase() : 'AS'}
                                </div>
                                <div>
                                    <div className="flex items-center gap-2 flex-wrap">
                                        <h2 className="text-lg font-bold text-gray-900">{selectedAsisten.nama}</h2>
                                        {selectedAsisten.status_karyawan && (
                                            <span className="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                {selectedAsisten.status_karyawan}
                                            </span>
                                        )}
                                        {selectedAsisten.status_resign && selectedAsisten.status_resign !== 'Aktif' && (
                                            <span className="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">
                                                {selectedAsisten.status_resign}
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-xs text-gray-500 font-mono mt-0.5">
                                        NIK: {selectedAsisten.nik || '-'} &bull; Program: {selectedAsisten.program || '-'} {selectedAsisten.batch ? `(Batch ${selectedAsisten.batch})` : ''}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setDetailModalOpen(false);
                                        openEditModal(selectedAsisten);
                                    }}
                                    className="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs border border-blue-200 flex items-center gap-1.5 transition"
                                >
                                    <Edit2 className="w-3.5 h-3.5" />
                                    <span>Edit</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setDetailModalOpen(false);
                                        confirmDelete(selectedAsisten);
                                    }}
                                    className="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-xs border border-rose-200 flex items-center gap-1.5 transition"
                                >
                                    <Trash2 className="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setDetailModalOpen(false)}
                                    className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        {/* Modal Body: 9 Structured Cards */}
                        <div className="p-6 overflow-y-auto space-y-5">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {ASISTEN_SECTIONS.map((sec) => {
                                    const IconComponent = sec.icon;
                                    return (
                                        <div
                                            key={sec.id}
                                            className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs hover:border-gray-300 transition"
                                        >
                                            <div className="flex items-center gap-2.5 pb-3 mb-3 border-b border-gray-100">
                                                <div className={`w-7 h-7 rounded-lg flex items-center justify-center font-bold border ${sec.color}`}>
                                                    <IconComponent className="w-4 h-4" />
                                                </div>
                                                <h3 className="text-xs font-bold text-gray-900 tracking-tight">{sec.title}</h3>
                                            </div>

                                            <dl className="space-y-2.5 text-xs">
                                                {sec.fields.map((field) => {
                                                    let val = selectedAsisten[field.key];
                                                    if (val === null || val === undefined || val === '') {
                                                        val = '-';
                                                    }

                                                    // Fallback check for unit kerja
                                                    const isUnitFallback = field.key === 'unit_kerja_saat_ini' && selectedAsisten.is_unit_kerja_fallback;

                                                    return (
                                                        <div key={field.key} className="flex flex-col">
                                                            <dt className="text-[11px] text-gray-400 font-medium leading-tight">
                                                                {field.label}
                                                            </dt>
                                                            <dd className="font-semibold text-gray-800 mt-0.5 break-words">
                                                                {val}
                                                                {isUnitFallback && (
                                                                    <span className="text-[10px] text-amber-600 block font-normal">
                                                                        (Diambil dari Penempatan Training)
                                                                    </span>
                                                                )}
                                                            </dd>
                                                        </div>
                                                    );
                                                })}
                                            </dl>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
                            <div>
                                Ditambahkan: <span className="font-mono">{selectedAsisten.created_at ? new Date(selectedAsisten.created_at).toLocaleDateString('id-ID') : '-'}</span>
                            </div>
                            <button
                                type="button"
                                onClick={() => setDetailModalOpen(false)}
                                className="px-5 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition"
                            >
                                Tutup Detail
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* CREATE / EDIT FORM MODAL (9 SECTIONS TABBED / MULTI-STEP)                */}
            {/* ========================================================================= */}
            {formModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 overflow-y-auto animate-in fade-in">
                    <div className="bg-white rounded-3xl max-w-4xl w-full border border-gray-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        {/* Header */}
                        <div className="px-6 py-5 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
                            <div>
                                <h2 className="text-lg font-bold text-gray-900">
                                    {formMode === 'create' ? 'Tambah Data Asisten Baru' : `Edit Data Asisten: ${formData.nama || ''}`}
                                </h2>
                                <p className="text-xs text-gray-500">Lengkapi formulir 9 bagian kelompok data di bawah ini</p>
                            </div>
                            <button
                                type="button"
                                onClick={() => setFormModalOpen(false)}
                                className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>

                        {/* Section Tabs */}
                        <div className="px-6 border-b border-gray-200 bg-gray-50 flex overflow-x-auto gap-1 py-2">
                            {ASISTEN_SECTIONS.map((sec, idx) => {
                                const IconComp = sec.icon;
                                const isActive = activeFormTab === sec.id;
                                return (
                                    <button
                                        key={sec.id}
                                        type="button"
                                        onClick={() => setActiveFormTab(sec.id)}
                                        className={`px-3 py-2 rounded-xl text-xs font-bold whitespace-nowrap flex items-center gap-1.5 transition ${
                                            isActive
                                                ? 'bg-white text-[#4B6A34] shadow-xs border border-gray-200'
                                                : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'
                                        }`}
                                    >
                                        <IconComp className="w-3.5 h-3.5" />
                                        <span>{idx + 1}. {sec.title}</span>
                                    </button>
                                );
                            })}
                        </div>

                        {/* Form Body */}
                        <form onSubmit={handleFormSubmit} className="flex-1 overflow-y-auto p-6 space-y-6">
                            {ASISTEN_SECTIONS.map((sec) => {
                                if (activeFormTab !== sec.id) return null;

                                return (
                                    <div key={sec.id} className="space-y-4">
                                        <div className="flex items-center gap-2 pb-2 border-b border-gray-100">
                                            <div className={`w-7 h-7 rounded-lg flex items-center justify-center border ${sec.color}`}>
                                                <sec.icon className="w-4 h-4" />
                                            </div>
                                            <div>
                                                <h3 className="text-sm font-bold text-gray-900">{sec.title}</h3>
                                                <p className="text-[11px] text-gray-400">Isi data atribut kelompok {sec.title.toLowerCase()}</p>
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {sec.fields.map((field) => {
                                                const err = formErrors[field.key];
                                                return (
                                                    <div key={field.key} className={field.type === 'textarea' ? 'md:col-span-2' : ''}>
                                                        <label className="block text-xs font-semibold text-gray-700 mb-1">
                                                            {field.label} {field.required && <span className="text-rose-500">*</span>}
                                                        </label>

                                                        {field.type === 'select' ? (
                                                            <select
                                                                value={formData[field.key] || ''}
                                                                onChange={(e) => handleFormChange(field.key, e.target.value)}
                                                                className={`w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#4B6A34] focus:bg-white focus:outline-none transition ${
                                                                    err ? 'border-rose-400' : 'border-gray-300'
                                                                }`}
                                                            >
                                                                {field.options.map((opt) => (
                                                                    <option key={opt} value={opt}>{opt || '-- Pilih --'}</option>
                                                                ))}
                                                            </select>
                                                        ) : field.type === 'textarea' ? (
                                                            <textarea
                                                                rows={3}
                                                                value={formData[field.key] || ''}
                                                                onChange={(e) => handleFormChange(field.key, e.target.value)}
                                                                placeholder={`Masukkan ${field.label.toLowerCase()}...`}
                                                                className={`w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#4B6A34] focus:bg-white focus:outline-none transition ${
                                                                    err ? 'border-rose-400' : 'border-gray-300'
                                                                }`}
                                                            />
                                                        ) : (
                                                            <input
                                                                type={field.type}
                                                                step={field.step}
                                                                value={formData[field.key] || ''}
                                                                onChange={(e) => handleFormChange(field.key, e.target.value)}
                                                                placeholder={`Masukkan ${field.label.toLowerCase()}...`}
                                                                className={`w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs focus:ring-2 focus:ring-[#4B6A34] focus:bg-white focus:outline-none transition ${
                                                                    err ? 'border-rose-400' : 'border-gray-300'
                                                                }`}
                                                            />
                                                        )}

                                                        {err && (
                                                            <p className="text-[11px] text-rose-500 mt-1 font-medium">{err[0]}</p>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                );
                            })}
                        </form>

                        {/* Footer Buttons */}
                        <div className="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                {/* Previous Tab */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        const currIdx = ASISTEN_SECTIONS.findIndex(s => s.id === activeFormTab);
                                        if (currIdx > 0) setActiveFormTab(ASISTEN_SECTIONS[currIdx - 1].id);
                                    }}
                                    disabled={activeFormTab === ASISTEN_SECTIONS[0].id}
                                    className="px-3 py-1.5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-white disabled:opacity-30 disabled:hover:bg-transparent transition"
                                >
                                    &larr; Tab Sebelumnya
                                </button>
                                {/* Next Tab */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        const currIdx = ASISTEN_SECTIONS.findIndex(s => s.id === activeFormTab);
                                        if (currIdx < ASISTEN_SECTIONS.length - 1) setActiveFormTab(ASISTEN_SECTIONS[currIdx + 1].id);
                                    }}
                                    disabled={activeFormTab === ASISTEN_SECTIONS[ASISTEN_SECTIONS.length - 1].id}
                                    className="px-3 py-1.5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-white disabled:opacity-30 disabled:hover:bg-transparent transition"
                                >
                                    Tab Berikutnya &rarr;
                                </button>
                            </div>

                            <div className="flex items-center gap-2.5">
                                <button
                                    type="button"
                                    onClick={() => setFormModalOpen(false)}
                                    className="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-white transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="button"
                                    disabled={submitting}
                                    onClick={handleFormSubmit}
                                    className="px-5 py-2 bg-[#4B6A34] text-white rounded-xl text-xs font-bold hover:bg-[#3D572A] disabled:opacity-50 transition flex items-center gap-1.5 shadow-xs"
                                >
                                    {submitting ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <Check className="w-3.5 h-3.5" />}
                                    <span>{formMode === 'create' ? 'Simpan Asisten' : 'Simpan Perubahan'}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* IMPORT CSV MODAL                                                          */}
            {/* ========================================================================= */}
            {importModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in">
                    <div className="bg-white rounded-3xl max-w-lg w-full border border-gray-200 shadow-2xl overflow-hidden">
                        <div className="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <div className="w-9 h-9 rounded-xl bg-emerald-50 text-[#4B6A34] flex items-center justify-center">
                                    <Upload className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900 text-sm">Import Data dari Excel / CSV</h3>
                                    <p className="text-[11px] text-gray-500">Mendukung file .xlsx, .xls, atau .csv (56 kolom master)</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => setImportModalOpen(false)}
                                className="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition"
                            >
                                <X className="w-3.5 h-3.5" />
                            </button>
                        </div>

                        <form onSubmit={handleImportSubmit} className="p-6 space-y-4">
                            {/* Download Template Banner */}
                            <div className="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3.5 flex items-center justify-between">
                                <div className="flex items-center gap-2.5">
                                    <FileSpreadsheet className="w-5 h-5 text-emerald-600 shrink-0" />
                                    <div>
                                        <p className="text-xs font-bold text-gray-900">Belum punya format file?</p>
                                        <p className="text-[11px] text-gray-500">Unduh template Excel siap isi dengan contoh data.</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-1.5 shrink-0">
                                    <button
                                        type="button"
                                        onClick={() => handleDownloadTemplate('lengkap')}
                                        className="px-2.5 py-1.5 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-semibold rounded-lg shadow-2xs transition flex items-center gap-1"
                                        title="Unduh Template Lengkap (56 Kolom)"
                                    >
                                        <Download className="w-3.5 h-3.5" />
                                        <span>Template Excel</span>
                                    </button>
                                </div>
                            </div>

                            {/* File Drag/Drop Input */}
                            <div>
                                <label className="block text-xs font-semibold text-gray-700 mb-1.5">Pilih File Excel / CSV</label>
                                <input
                                    type="file"
                                    accept=".csv,.txt,.xlsx,.xls"
                                    onChange={(e) => {
                                        setImportFile(e.target.files[0] || null);
                                        setImportResult(null);
                                    }}
                                    className="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#4B6A34] hover:file:bg-emerald-100 cursor-pointer border border-gray-300 rounded-xl p-2 bg-gray-50"
                                />
                            </div>

                            {/* Mode Selection */}
                            <div>
                                <label className="block text-xs font-semibold text-gray-700 mb-1.5">Mode Import</label>
                                <div className="grid grid-cols-2 gap-3">
                                    <label className={`p-3 rounded-xl border cursor-pointer text-xs transition ${
                                        importMode === 'append' ? 'border-[#4B6A34] bg-emerald-50/50 text-[#4B6A34] font-semibold' : 'border-gray-200 text-gray-600'
                                    }`}>
                                        <input
                                            type="radio"
                                            name="importMode"
                                            value="append"
                                            checked={importMode === 'append'}
                                            onChange={() => setImportMode('append')}
                                            className="mr-2"
                                        />
                                        <span>Tambah Data (Append)</span>
                                        <p className="text-[10px] text-gray-400 mt-1 font-normal">Menambahkan baris tanpa menghapus data yang ada</p>
                                    </label>

                                    <label className={`p-3 rounded-xl border cursor-pointer text-xs transition ${
                                        importMode === 'replace' ? 'border-rose-500 bg-rose-50/50 text-rose-700 font-semibold' : 'border-gray-200 text-gray-600'
                                    }`}>
                                        <input
                                            type="radio"
                                            name="importMode"
                                            value="replace"
                                            checked={importMode === 'replace'}
                                            onChange={() => setImportMode('replace')}
                                            className="mr-2"
                                        />
                                        <span>Timpa Semua (Replace)</span>
                                        <p className="text-[10px] text-gray-400 mt-1 font-normal">Menghapus seluruh data lama dan mengganti dengan CSV baru</p>
                                    </label>
                                </div>
                            </div>

                            {/* Result Summary */}
                            {importResult && (
                                <div className={`p-3.5 rounded-xl border text-xs ${
                                    importResult.status === 'success'
                                        ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                                        : 'bg-rose-50 border-rose-200 text-rose-800'
                                }`}>
                                    <div className="flex items-center gap-2 font-bold mb-1">
                                        {importResult.status === 'success' ? <CheckCircle2 className="w-4 h-4 text-emerald-600" /> : <AlertCircle className="w-4 h-4 text-rose-600" />}
                                        <span>{importResult.message}</span>
                                    </div>
                                    {importResult.errors && importResult.errors.length > 0 && (
                                        <div className="mt-2 text-[11px] max-h-32 overflow-y-auto space-y-1">
                                            <p className="font-semibold text-rose-700">Detail Baris Bermasalah:</p>
                                            {importResult.errors.slice(0, 5).map((err, i) => (
                                                <p key={i} className="text-gray-600">Baris {err.row}: {err.reason}</p>
                                            ))}
                                            {importResult.errors.length > 5 && (
                                                <p className="text-gray-400">... dan {importResult.errors.length - 5} baris lainnya</p>
                                            )}
                                        </div>
                                    )}
                                </div>
                            )}

                            {/* Buttons */}
                            <div className="pt-2 flex items-center justify-end gap-2.5">
                                <button
                                    type="button"
                                    onClick={() => setImportModalOpen(false)}
                                    className="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Tutup
                                </button>
                                <button
                                    type="submit"
                                    disabled={!importFile || importing}
                                    className="px-5 py-2 bg-[#4B6A34] text-white rounded-xl text-xs font-bold hover:bg-[#3D572A] disabled:opacity-40 transition flex items-center gap-1.5"
                                >
                                    {importing ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <Upload className="w-3.5 h-3.5" />}
                                    <span>Mulai Import</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* DELETE CONFIRM MODAL                                                      */}
            {/* ========================================================================= */}
            {deleteConfirmOpen && itemToDelete && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in">
                    <div className="bg-white rounded-3xl max-w-sm w-full border border-gray-200 shadow-2xl p-6 text-center space-y-4">
                        <div className="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                            <AlertTriangle className="w-6 h-6" />
                        </div>
                        <div>
                            <h3 className="font-bold text-gray-900 text-base">Hapus Data Asisten?</h3>
                            <p className="text-xs text-gray-500 mt-1">
                                Anda yakin ingin menghapus data <strong>{itemToDelete.nama}</strong>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                        <div className="flex items-center justify-center gap-2.5 pt-2">
                            <button
                                type="button"
                                onClick={() => setDeleteConfirmOpen(false)}
                                className="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                onClick={handleDelete}
                                className="px-5 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition"
                            >
                                Ya, Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            )}

        </div>
    );
}
