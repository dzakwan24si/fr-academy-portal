import React, { useState, useEffect, useMemo } from 'react';
import {
    GraduationCap, Users, UserCheck, UserX, Search, Download, Upload,
    RefreshCw, ChevronUp, ChevronDown, Filter, AlertCircle, X, FileSpreadsheet,
    Plus, Pencil, Trash2, Save, CheckCircle2
} from 'lucide-react';

const CATEGORY_TABS = [
    { code: 'all', name: 'Semua Kategori', label: 'Semua' },
    { code: 'A', name: 'Field Assistant Training', label: 'Field Assistant (FAT)' },
    { code: 'B', name: 'Mill Assistant Training', label: 'Mill Assistant (MAT)' },
    { code: 'C', name: 'Admin Assistant Training', label: 'Admin Assistant (AAT)' },
    { code: 'D', name: 'Traction Assistant Training', label: 'Traction Assistant (TAT)' },
    { code: 'E', name: 'Downstream Management Training', label: 'Downstream (DMT)' },
];

const CATEGORY_OPTIONS = [
    { code: 'A', name: 'Field Assistant Training' },
    { code: 'B', name: 'Mill Assistant Training' },
    { code: 'C', name: 'Admin Assistant Training' },
    { code: 'D', name: 'Traction Assistant Training' },
    { code: 'E', name: 'Downstream Management Training' },
];

export default function MonitoringAlumniTab() {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [summary, setSummary] = useState({});
    const [filterOptions, setFilterOptions] = useState({ kategori: [], tahun: [] });

    // Filter states
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [selectedTahun, setSelectedTahun] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');

    // Sorting
    const [sortBy, setSortBy] = useState('no');
    const [sortOrder, setSortOrder] = useState('asc');

    // Drilldown Modal
    const [drilldownOpen, setDrilldownOpen] = useState(false);
    const [drilldownLoading, setDrilldownLoading] = useState(false);
    const [drilldownData, setDrilldownData] = useState([]);
    const [drilldownInfo, setDrilldownInfo] = useState({ program: '', filter_type: '', label: '' });

    // Import Modal
    const [importModalOpen, setImportModalOpen] = useState(false);
    const [importFile, setImportFile] = useState(null);
    const [importing, setImporting] = useState(false);
    const [importMessage, setImportMessage] = useState(null);

    // CRUD Manual Add / Edit Modal
    const [formModalOpen, setFormModalOpen] = useState(false);
    const [isEditMode, setIsEditMode] = useState(false);
    const [selectedId, setSelectedId] = useState(null);
    const [formData, setFormData] = useState({
        kode_kategori: 'A',
        kategori_program: 'Field Assistant Training',
        nama_program: '',
        tahun: new Date().getFullYear(),
        catatan: '',
    });
    const [saving, setSaving] = useState(false);

    // Delete Modal
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [recordToDelete, setRecordToDelete] = useState(null);
    const [deleting, setDeleting] = useState(false);

    // Global Alert Feedback Message
    const [alertMessage, setAlertMessage] = useState(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Open Add Modal
    const handleOpenAddModal = () => {
        setIsEditMode(false);
        setSelectedId(null);
        setFormData({
            kode_kategori: 'A',
            kategori_program: 'Field Assistant Training',
            nama_program: '',
            tahun: new Date().getFullYear(),
            catatan: '',
        });
        setFormModalOpen(true);
    };

    // Open Edit Modal
    const handleOpenEditModal = (row) => {
        setIsEditMode(true);
        setSelectedId(row.id);
        setFormData({
            kode_kategori: row.kode_kategori || 'A',
            kategori_program: row.kategori_program || 'Field Assistant Training',
            nama_program: row.nama_program || '',
            tahun: row.tahun_training || row.tahun_lulus || row.tahun_rekrut || new Date().getFullYear(),
            catatan: row.catatan || '',
        });
        setFormModalOpen(true);
    };

    // Fetch Data (Strictly batch mode)
    const fetchData = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                kategori: selectedCategory,
                tahun: selectedTahun,
                search: searchQuery,
                view_mode: 'batch',
            });

            const res = await fetch(`/administrasi/monitoring-alumni/api/data?${params.toString()}`);
            const result = await res.json();

            if (result.status === 'success') {
                setData(result.data || []);
                setSummary(result.summary || {});
                setFilterOptions(result.filters || { kategori: [], tahun: [] });
            }
        } catch (err) {
            console.error('Error fetching alumni monitoring data:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, [selectedCategory, selectedTahun]);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        fetchData();
    };

    // Sort table data
    const sortedData = useMemo(() => {
        if (!sortBy) return data;
        return [...data].sort((a, b) => {
            let valA = a[sortBy];
            let valB = b[sortBy];

            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();

            if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
            if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
    }, [data, sortBy, sortOrder]);

    const handleSort = (column) => {
        if (sortBy === column) {
            setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(column);
            setSortOrder('asc');
        }
    };

    // Open Drilldown
    const openDrilldown = async (programNames, filterType, label) => {
        const targetProgram = typeof programNames === 'object' && Array.isArray(programNames) 
            ? programNames.join(',') 
            : String(programNames);

        setDrilldownInfo({ program: targetProgram, filter_type: filterType, label });
        setDrilldownOpen(true);
        setDrilldownLoading(true);
        try {
            const params = new URLSearchParams({
                program: targetProgram,
                filter_type: filterType,
            });
            const res = await fetch(`/administrasi/monitoring-alumni/api/drilldown?${params.toString()}`);
            const result = await res.json();
            if (result.status === 'success') {
                setDrilldownData(result.data || []);
            }
        } catch (err) {
            console.error('Error fetching drilldown data:', err);
        } finally {
            setDrilldownLoading(false);
        }
    };

    // Export CSV
    const handleExport = () => {
        const params = new URLSearchParams({
            kategori: selectedCategory,
            tahun: selectedTahun,
            search: searchQuery,
            view_mode: 'batch',
        });
        window.location.href = `/administrasi/monitoring-alumni/api/export?${params.toString()}`;
    };

    // Import CSV
    const handleImportSubmit = async (e) => {
        e.preventDefault();
        if (!importFile) return;

        setImporting(true);
        setImportMessage(null);

        const form = new FormData();
        form.append('file', importFile);
        form.append('mode', 'replace');

        try {
            const res = await fetch('/administrasi/monitoring-lulusan/api/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: form,
            });
            const result = await res.json();
            setImportMessage(result);
            if (result.status === 'success') {
                fetchData();
            }
        } catch (err) {
            setImportMessage({ status: 'error', message: 'Gagal mengunggah file CSV.' });
        } finally {
            setImporting(false);
        }
    };



    // Category Selection Change in Form Modal
    const handleCategoryChange = (e) => {
        const selectedCode = e.target.value;
        const matched = CATEGORY_OPTIONS.find(c => c.code === selectedCode);
        setFormData(prev => ({
            ...prev,
            kode_kategori: selectedCode,
            kategori_program: matched ? matched.name : 'Program Training',
        }));
    };

    // Save Form (Create or Update)
    const handleFormSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setAlertMessage(null);

        try {
            const url = isEditMode 
                ? `/administrasi/monitoring-alumni/api/${selectedId}`
                : '/administrasi/monitoring-alumni/api/store';
            
            const method = isEditMode ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const result = await res.json();

            if (res.ok && result.status === 'success') {
                setAlertMessage({ status: 'success', message: result.message || 'Data batch program berhasil disimpan!' });
                setFormModalOpen(false);
                fetchData();
            } else {
                setAlertMessage({ status: 'error', message: result.message || 'Gagal menyimpan data.' });
            }
        } catch (err) {
            setAlertMessage({ status: 'error', message: 'Terjadi kesalahan sistem saat menyimpan data.' });
        } finally {
            setSaving(false);
        }
    };

    // Open Delete Modal
    const handleOpenDeleteModal = (row) => {
        setRecordToDelete(row);
        setDeleteModalOpen(true);
    };

    // Confirm Delete
    const handleDeleteSubmit = async () => {
        if (!recordToDelete) return;
        setDeleting(true);

        try {
            const res = await fetch(`/administrasi/monitoring-alumni/api/${recordToDelete.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
            });

            const result = await res.json();
            if (res.ok && result.status === 'success') {
                setAlertMessage({ status: 'success', message: 'Data batch program berhasil dihapus!' });
                setDeleteModalOpen(false);
                setRecordToDelete(null);
                fetchData();
            } else {
                setAlertMessage({ status: 'error', message: result.message || 'Gagal menghapus data.' });
            }
        } catch (err) {
            setAlertMessage({ status: 'error', message: 'Terjadi kesalahan sistem saat menghapus data.' });
        } finally {
            setDeleting(false);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header & Action Controls */}
            <div className="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-100 text-[#4B6A34] flex items-center justify-center font-bold shadow-xs">
                        <GraduationCap className="w-6 h-6" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold text-gray-900 tracking-tight">
                            Monitoring Alumni
                        </h2>
                        <p className="text-xs text-gray-500 font-medium">
                            Analisis kelulusan, retensi & monitoring karir alumni per gelombang batch program
                        </p>
                    </div>
                </div>

                <div className="flex items-center gap-2.5 flex-wrap">
                    <button
                        type="button"
                        onClick={handleExport}
                        className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
                    >
                        <Download className="w-4 h-4 text-gray-500" />
                        <span>Export Excel (21 Kolom Batch)</span>
                    </button>

                    <button
                        type="button"
                        onClick={() => {
                            setImportFile(null);
                            setImportMessage(null);
                            setImportModalOpen(true);
                        }}
                        className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
                    >
                        <Upload className="w-4 h-4 text-gray-500" />
                        <span>Import CSV</span>
                    </button>

                    <button
                        type="button"
                        onClick={handleOpenAddModal}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#4B6A34] hover:bg-[#3d562a] text-xs font-bold text-white transition shadow-sm cursor-pointer"
                    >
                        <Plus className="w-4 h-4" />
                        <span>Tambah Data Batch</span>
                    </button>

                    <button
                        type="button"
                        onClick={fetchData}
                        className="p-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
                        title="Segarkan Data"
                    >
                        <RefreshCw className="w-4 h-4" />
                    </button>
                </div>
            </div>

            {/* Notification Banner */}
            {alertMessage && (
                <div className={`p-4 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs ${
                    alertMessage.status === 'success' 
                        ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' 
                        : 'bg-rose-50 text-rose-800 border border-rose-200'
                }`}>
                    <div className="flex items-center gap-2.5">
                        {alertMessage.status === 'success' ? (
                            <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        ) : (
                            <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
                        )}
                        <span>{alertMessage.message}</span>
                    </div>
                    <button type="button" onClick={() => setAlertMessage(null)} className="text-gray-400 hover:text-gray-600">
                        <X className="w-4 h-4" />
                    </button>
                </div>
            )}

            {/* KPI Summary Cards */}
            <div className="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
                    <span className="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">
                        Total Batch Program
                    </span>
                    <p className="text-2xl font-bold text-gray-900">
                        {summary.total_batches || 0} <span className="text-xs text-gray-400 font-normal">Batch</span>
                    </p>
                    <p className="text-[11px] text-gray-400 mt-0.5">Seluruh angkatan aktif</p>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
                    <span className="text-[11px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Total Trainee Masuk</span>
                    <p className="text-2xl font-bold text-blue-700">{summary.total_trainee?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-blue-500 mt-0.5">Culled: {summary.total_culled || 0} ({summary.pct_culled_weighted || 0}%)</p>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
                    <span className="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Passed (Lulus Training)</span>
                    <p className="text-2xl font-bold text-emerald-700">{summary.total_passed?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-emerald-600/70 mt-0.5">Tingkat kelulusan: {summary.pct_passed_weighted || 0}%</p>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
                    <span className="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Eksis / Aktif Bekerja</span>
                    <p className="text-2xl font-bold text-emerald-800">{summary.total_eksis?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-emerald-600 mt-0.5">Retensi: {summary.pct_existing_weighted || 0}% dari Trainee</p>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs col-span-2 lg:col-span-1">
                    <span className="text-[11px] font-bold text-rose-600 uppercase tracking-wider block mb-1">Total Resign / PHK</span>
                    <p className="text-2xl font-bold text-rose-700">{summary.total_resign?.toLocaleString('id-ID') || 0}</p>
                    <p className="text-[11px] text-rose-500 mt-0.5">Turnover: {summary.pct_resign_weighted || 0}% dari Trainee</p>
                </div>
            </div>

            {/* Category Filter Tabs */}
            <div className="bg-white rounded-2xl border border-gray-200 p-2 shadow-xs flex overflow-x-auto gap-1">
                {CATEGORY_TABS.map((tab) => {
                    const isActive = selectedCategory === tab.code;
                    return (
                        <button
                            key={tab.code}
                            type="button"
                            onClick={() => setSelectedCategory(tab.code)}
                            className={`px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 ${
                                isActive
                                    ? 'bg-[#4B6A34] text-white shadow-xs'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                            }`}
                        >
                            <span>{tab.label}</span>
                        </button>
                    );
                })}
            </div>

            {/* Filter Search Bar & Year */}
            <div className="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <form onSubmit={handleSearchSubmit} className="relative flex-1">
                    <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="Cari nama program batch (misal: Field Assistant - I/2022, MAT I 2016)..."
                        className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-[#4B6A34] focus:bg-white focus:outline-none transition"
                    />
                </form>

                <div className="flex items-center gap-2">
                    <label className="text-xs text-gray-500 font-semibold flex items-center gap-1.5 whitespace-nowrap">
                        <Filter className="w-3.5 h-3.5 text-gray-400" />
                        Tahun:
                        <select
                            value={selectedTahun}
                            onChange={(e) => setSelectedTahun(e.target.value)}
                            className="px-2.5 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-semibold focus:outline-none"
                        >
                            <option value="all">Semua Tahun</option>
                            {filterOptions.tahun?.map((th) => (
                                <option key={th} value={th}>{th}</option>
                            ))}
                        </select>
                    </label>

                    {(selectedCategory !== 'all' || selectedTahun !== 'all' || searchQuery !== '') && (
                        <button
                            type="button"
                            onClick={() => {
                                setSelectedCategory('all');
                                setSelectedTahun('all');
                                setSearchQuery('');
                            }}
                            className="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-medium transition"
                        >
                            Reset Filter
                        </button>
                    )}
                </div>
            </div>

            {/* Main Batch Table */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div className="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <span className="font-semibold text-gray-800">
                        Menampilkan {sortedData.length} Batch Program
                    </span>
                    <span className="text-[11px] text-gray-400">Klik angka Eksis/Resign/Posisi untuk rincian peserta</span>
                </div>

                <div className="overflow-x-auto max-h-[700px]">
                    <table className="w-full text-left text-[11px] border-collapse">
                        <thead className="bg-gray-100 text-gray-700 uppercase font-bold sticky top-0 z-20 shadow-xs select-none">
                            <tr>
                                <th rowSpan={2} className="px-3 py-3 text-center border-r border-gray-200 w-10">No</th>
                                <th rowSpan={2} className="px-3 py-3 border-r border-gray-200 min-w-[200px]">Nama Program</th>
                                <th colSpan={3} className="px-3 py-2 text-center border-r border-gray-200 bg-gray-200/70">Periode Tahun</th>
                                <th colSpan={5} className="px-3 py-2 text-center border-r border-gray-200 bg-blue-100/70 text-blue-900">Peserta Training</th>
                                <th colSpan={4} className="px-3 py-2 text-center border-r border-gray-200 bg-emerald-100/70 text-emerald-900">Kalkulasi % (Formula / Trainee)</th>
                                <th colSpan={5} className="px-3 py-2 text-center border-r border-gray-200 bg-purple-100/70 text-purple-900">Distribusi Posisi Terakhir (Eksis)</th>
                                <th rowSpan={2} className="px-3 py-3 text-center w-16">Aksi</th>
                            </tr>
                            <tr className="border-t border-gray-200 text-[10px]">
                                {/* Periode */}
                                <th className="px-2 py-2 text-center border-r border-gray-200">Rekrut</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200">Training</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200">Lulus</th>
                                {/* Peserta */}
                                <th className="px-2.5 py-2 text-center border-r border-gray-200 bg-blue-50/70">Trainee</th>
                                <th className="px-2.5 py-2 text-center border-r border-gray-200 bg-blue-50/70">Culled</th>
                                <th className="px-2.5 py-2 text-center border-r border-gray-200 bg-blue-50/70 font-extrabold text-blue-900">Passed</th>
                                <th className="px-2.5 py-2 text-center border-r border-gray-200 bg-rose-50/70 font-extrabold text-rose-800">Resign</th>
                                <th className="px-2.5 py-2 text-center border-r border-gray-200 bg-emerald-50/70 font-extrabold text-emerald-800">Eksis</th>
                                {/* % Formula */}
                                <th className="px-2 py-2 text-center border-r border-gray-200">% Culled</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200">% Passed</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200">% Resign</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 font-extrabold">% Eksis</th>
                                {/* Posisi */}
                                <th className="px-2 py-2 text-center border-r border-gray-200 bg-purple-50">PC</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 bg-purple-50">GM</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 bg-purple-50">MGR</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 bg-purple-50">Askep</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 bg-purple-50">Asisten</th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-gray-200 font-medium">
                            {loading ? (
                                <tr>
                                    <td colSpan={22} className="px-4 py-16 text-center text-gray-500">
                                        <RefreshCw className="w-7 h-7 text-[#4B6A34] animate-spin mx-auto mb-2" />
                                        <p className="font-semibold">Menghitung live formula dan rekap data batch...</p>
                                    </td>
                                </tr>
                            ) : sortedData.length === 0 ? (
                                <tr>
                                    <td colSpan={22} className="px-4 py-12 text-center text-gray-400">
                                        <AlertCircle className="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                        <p className="font-semibold text-gray-600">Tidak ada batch program yang ditemukan.</p>
                                    </td>
                                </tr>
                            ) : (
                                sortedData.map((row, idx) => (
                                    <tr key={row.id || idx} className="hover:bg-gray-50/80 transition-colors">
                                        {/* 1. No */}
                                        <td className="px-3 py-2 text-center text-gray-400 font-mono border-r border-gray-100">
                                            {row.no || idx + 1}
                                        </td>

                                        {/* 4. Nama Program */}
                                        <td className="px-3 py-2 border-r border-gray-100">
                                            <div className="font-bold text-gray-900">{row.nama_program}</div>
                                            <span className="text-[10px] text-gray-400">
                                                [{row.kode_kategori}] {row.kategori_program}
                                            </span>
                                        </td>

                                        {/* 5, 6, 7. Tahun Rekrut, Training, Lulus */}
                                        <td className="px-2 py-2 text-center text-gray-600 border-r border-gray-100">{row.tahun_rekrut || '-'}</td>
                                        <td className="px-2 py-2 text-center text-gray-600 border-r border-gray-100">{row.tahun_training || '-'}</td>
                                        <td className="px-2 py-2 text-center text-gray-600 border-r border-gray-100">{row.tahun_lulus || '-'}</td>

                                        {/* 8. Jumlah Trainee */}
                                        <td className="px-2.5 py-2 text-center font-bold text-gray-900 border-r border-gray-100 bg-blue-50/20">
                                            {row.jumlah_trainee}
                                        </td>

                                        {/* 9. Jumlah Culled */}
                                        <td className="px-2.5 py-2 text-center text-rose-600 border-r border-gray-100 bg-rose-50/20">
                                            {row.jumlah_culled}
                                        </td>

                                        {/* 10. Jumlah Passed */}
                                        <td className="px-2.5 py-2 text-center font-extrabold text-blue-900 border-r border-gray-100 bg-blue-50/40">
                                            {row.jumlah_passed}
                                        </td>

                                        {/* 11. Jumlah Resign (Drilldown) */}
                                        <td className="px-2.5 py-2 text-center border-r border-gray-100 bg-rose-50/30">
                                            {row.jumlah_resign > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'resign', `Resign - ${row.nama_program}`)}
                                                    className="px-2 py-0.5 rounded-full text-xs font-bold text-rose-700 hover:bg-rose-200 bg-rose-100 transition cursor-pointer"
                                                    title="Lihat nama asisten resign"
                                                >
                                                    {row.jumlah_resign}
                                                </button>
                                            ) : (
                                                <span className="text-gray-400">0</span>
                                            )}
                                        </td>

                                        {/* 12. Jumlah Eksis (Drilldown) */}
                                        <td className="px-2.5 py-2 text-center border-r border-gray-100 bg-emerald-50/30">
                                            {row.jumlah_eksis > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'eksis', `Eksis/Aktif - ${row.nama_program}`)}
                                                    className="px-2 py-0.5 rounded-full text-xs font-bold text-emerald-800 hover:bg-emerald-200 bg-emerald-100 transition cursor-pointer"
                                                    title="Lihat nama asisten aktif"
                                                >
                                                    {row.jumlah_eksis}
                                                </button>
                                            ) : (
                                                <span className="text-gray-400">0</span>
                                            )}
                                        </td>

                                        {/* 13. % Culled */}
                                        <td className="px-2 py-2 text-center text-gray-500 border-r border-gray-100 font-mono">
                                            {row.pct_culled}%
                                        </td>

                                        {/* 14. % Passed */}
                                        <td className="px-2 py-2 text-center font-bold text-blue-700 border-r border-gray-100 font-mono">
                                            {row.pct_passed}%
                                        </td>

                                        {/* 15. % Resign */}
                                        <td className="px-2 py-2 text-center text-rose-600 border-r border-gray-100 font-mono">
                                            {row.pct_resign}%
                                        </td>

                                        {/* 16. % Existing */}
                                        <td className="px-2 py-2 text-center font-extrabold text-emerald-700 border-r border-gray-100 font-mono bg-emerald-50/20">
                                            {row.pct_existing}%
                                        </td>

                                        {/* 17. Posisi PC */}
                                        <td className="px-2 py-2 text-center border-r border-gray-100 bg-purple-50/20">
                                            {row.posisi_pc > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'pc', `Posisi PC - ${row.nama_program}`)}
                                                    className="font-bold text-purple-700 hover:underline cursor-pointer"
                                                >
                                                    {row.posisi_pc}
                                                </button>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>

                                        {/* 18. Posisi GM */}
                                        <td className="px-2 py-2 text-center border-r border-gray-100 bg-purple-50/20">
                                            {row.posisi_gm > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'gm', `Posisi GM - ${row.nama_program}`)}
                                                    className="font-bold text-purple-700 hover:underline cursor-pointer"
                                                >
                                                    {row.posisi_gm}
                                                </button>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>

                                        {/* 19. Posisi MGR */}
                                        <td className="px-2 py-2 text-center border-r border-gray-100 bg-purple-50/20">
                                            {row.posisi_mgr > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'mgr', `Posisi MGR - ${row.nama_program}`)}
                                                    className="font-bold text-purple-700 hover:underline cursor-pointer"
                                                >
                                                    {row.posisi_mgr}
                                                </button>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>

                                        {/* 20. Posisi Askep */}
                                        <td className="px-2 py-2 text-center border-r border-gray-100 bg-purple-50/20">
                                            {row.posisi_askep > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'askep', `Posisi Askep - ${row.nama_program}`)}
                                                    className="font-bold text-purple-700 hover:underline cursor-pointer"
                                                >
                                                    {row.posisi_askep}
                                                </button>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>

                                        {/* 21. Posisi Asisten */}
                                        <td className="px-2 py-2 text-center border-r border-gray-100 bg-purple-50/20">
                                            {row.posisi_asisten > 0 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => openDrilldown(row.nama_program, 'asisten', `Posisi Asisten - ${row.nama_program}`)}
                                                    className="font-bold text-purple-700 hover:underline cursor-pointer"
                                                >
                                                    {row.posisi_asisten}
                                                </button>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>

                                        {/* 22. Action Column (Edit / Delete) */}
                                        <td className="px-2 py-2 text-center">
                                            <div className="flex items-center justify-center gap-1">
                                                <button
                                                    type="button"
                                                    onClick={() => handleOpenEditModal(row)}
                                                    className="p-1 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-50 transition cursor-pointer"
                                                    title="Edit Data Batch"
                                                >
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => handleOpenDeleteModal(row)}
                                                    className="p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Hapus Data Batch"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>

                        {/* TOTAL ROW */}
                        {sortedData.length > 0 && (
                            <tfoot className="bg-gray-900 text-white font-bold text-[11px] sticky bottom-0 z-20">
                                <tr>
                                    <td colSpan={2} className="px-3 py-3 text-center uppercase tracking-wider border-r border-gray-800">
                                        TOTAL ({summary.total_batches} Program)
                                    </td>
                                    <td colSpan={3} className="px-2 py-3 text-center border-r border-gray-800 text-gray-400">
                                        -
                                    </td>
                                    {/* Peserta Totals */}
                                    <td className="px-2.5 py-3 text-center border-r border-gray-800 text-blue-300">
                                        {summary.total_trainee?.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-2.5 py-3 text-center border-r border-gray-800 text-rose-300">
                                        {summary.total_culled?.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-2.5 py-3 text-center border-r border-gray-800 text-blue-200 font-extrabold">
                                        {summary.total_passed?.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-2.5 py-3 text-center border-r border-gray-800 text-rose-300 font-extrabold">
                                        {summary.total_resign?.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-2.5 py-3 text-center border-r border-gray-800 text-emerald-300 font-extrabold">
                                        {summary.total_eksis?.toLocaleString('id-ID')}
                                    </td>
                                    {/* Weighted % */}
                                    <td className="px-2 py-3 text-center border-r border-gray-800 font-mono text-gray-300">
                                        {summary.pct_culled_weighted}%
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 font-mono text-blue-300">
                                        {summary.pct_passed_weighted}%
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 font-mono text-rose-300">
                                        {summary.pct_resign_weighted}%
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 font-mono text-emerald-300 font-extrabold">
                                        {summary.pct_existing_weighted}%
                                    </td>
                                    {/* Posisi Totals */}
                                    <td className="px-2 py-3 text-center border-r border-gray-800 text-purple-200">
                                        {summary.posisi_pc_total}
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 text-purple-200">
                                        {summary.posisi_gm_total}
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 text-purple-200">
                                        {summary.posisi_mgr_total}
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 text-purple-200">
                                        {summary.posisi_askep_total}
                                    </td>
                                    <td className="px-2 py-3 text-center border-r border-gray-800 text-purple-200">
                                        {summary.posisi_asisten_total}
                                    </td>
                                    <td className="px-2 py-3 text-center text-gray-500">-</td>
                                </tr>
                            </tfoot>
                        )}
                    </table>
                </div>
            </div>

            {/* Drilldown Modal */}
            {drilldownOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 animate-fade-in">
                    <div className="bg-white rounded-2xl max-w-4xl w-full max-h-[85vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200">
                        <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                            <div>
                                <h3 className="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <Users className="w-5 h-5 text-[#4B6A34]" />
                                    <span>Rincian Data: {drilldownInfo.label}</span>
                                </h3>
                                <p className="text-xs text-gray-500">
                                    Menampilkan daftar asisten yang tersinkronisasi dari Database Asisten
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={() => setDrilldownOpen(false)}
                                className="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="p-6 overflow-y-auto flex-1">
                            {drilldownLoading ? (
                                <div className="py-12 text-center text-gray-500">
                                    <RefreshCw className="w-8 h-8 text-[#4B6A34] animate-spin mx-auto mb-2" />
                                    <p className="font-semibold text-sm">Memuat data asisten...</p>
                                </div>
                            ) : drilldownData.length === 0 ? (
                                <div className="py-12 text-center text-gray-400">
                                    <AlertCircle className="w-10 h-10 mx-auto mb-2 text-gray-300" />
                                    <p className="font-semibold text-gray-600 text-sm">Tidak ada data asisten terdaftar untuk kategori ini.</p>
                                    <p className="text-xs text-gray-400 mt-1">
                                        Data asisten detail akan muncul setelah Anda mengimpor data ke menu Database Asisten.
                                    </p>
                                </div>
                            ) : (
                                <div className="border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                                    <table className="w-full text-left text-xs border-collapse">
                                        <thead className="bg-gray-100 text-gray-700 font-bold">
                                            <tr>
                                                <th className="px-3 py-2.5 text-center w-10">No</th>
                                                <th className="px-3 py-2.5">NIK / Nama</th>
                                                <th className="px-3 py-2.5">Program & Batch</th>
                                                <th className="px-3 py-2.5">Status & Jabatan</th>
                                                <th className="px-3 py-2.5">Region / PT</th>
                                                <th className="px-3 py-2.5 text-center">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-200">
                                            {drilldownData.map((ast, i) => (
                                                <tr key={ast.id || i} className="hover:bg-gray-50/80">
                                                    <td className="px-3 py-2 text-center text-gray-400 font-mono">{i + 1}</td>
                                                    <td className="px-3 py-2">
                                                        <div className="font-bold text-gray-900">{ast.nama_lengkap}</div>
                                                        <span className="text-[11px] text-gray-500 font-mono">{ast.nik || '-'}</span>
                                                    </td>
                                                    <td className="px-3 py-2 text-gray-700">
                                                        <span className="font-semibold">{ast.program}</span>
                                                        <span className="text-gray-400 block text-[10px]">{ast.kategori_program}</span>
                                                    </td>
                                                    <td className="px-3 py-2">
                                                        <span className="font-bold text-purple-800">{ast.jabatan_saat_ini || '-'}</span>
                                                        <span className="text-gray-500 block text-[10px]">{ast.status_karyawan}</span>
                                                    </td>
                                                    <td className="px-3 py-2 text-gray-600">
                                                        <div>{ast.region_saat_ini || '-'}</div>
                                                        <span className="text-[10px] text-gray-400">{ast.pt_saat_ini} - {ast.estate_saat_ini}</span>
                                                    </td>
                                                    <td className="px-3 py-2 text-center">
                                                        <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                                                            ast.keterangan === 'Eksis'
                                                                ? 'bg-emerald-100 text-emerald-800'
                                                                : 'bg-rose-100 text-rose-800'
                                                        }`}>
                                                            {ast.keterangan}
                                                        </span>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>

                        <div className="px-6 py-3.5 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                            <span className="text-xs text-gray-500">
                                Total: <strong>{drilldownData.length}</strong> orang
                            </span>
                            <button
                                type="button"
                                onClick={() => setDrilldownOpen(false)}
                                className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition cursor-pointer"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Import Master CSV Modal */}
            {importModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 animate-fade-in">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
                        <div className="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                            <h3 className="text-base font-bold text-gray-900 flex items-center gap-2">
                                <FileSpreadsheet className="w-5 h-5 text-[#4B6A34]" />
                                <span>Import Data Monitoring CSV</span>
                            </h3>
                            <button
                                type="button"
                                onClick={() => setImportModalOpen(false)}
                                className="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {importMessage && (
                            <div className={`p-3.5 rounded-xl text-xs font-semibold mb-4 ${
                                importMessage.status === 'success' 
                                    ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' 
                                    : 'bg-rose-50 text-rose-800 border border-rose-200'
                            }`}>
                                {importMessage.message}
                            </div>
                        )}

                        <form onSubmit={handleImportSubmit} className="space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1.5">
                                    Pilih File CSV Monitoring Batch (21 Kolom):
                                </label>
                                <input
                                    type="file"
                                    accept=".csv,.txt"
                                    onChange={(e) => setImportFile(e.target.files[0])}
                                    className="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#4B6A34] hover:file:bg-emerald-100 cursor-pointer border border-gray-300 rounded-xl p-1"
                                    required
                                />
                                <p className="text-[11px] text-gray-400 mt-1.5">
                                    Format: CSV dengan delimiter koma (,) atau titik koma (;). Mendukung 21 kolom standar.
                                </p>
                            </div>

                            <div className="flex items-center justify-end gap-2.5 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setImportModalOpen(false)}
                                    className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={importing || !importFile}
                                    className="px-4 py-2 bg-[#4B6A34] hover:bg-[#3D562A] text-white text-xs font-bold rounded-xl shadow-xs transition disabled:opacity-50 inline-flex items-center gap-2 cursor-pointer"
                                >
                                    {importing && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                                    <span>{importing ? 'Mengimpor...' : 'Mulai Impor CSV'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal Tambah / Edit Data Batch Program */}
            {formModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 animate-fade-in">
                    <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 relative">
                        <div className="flex items-center justify-between pb-4 border-b border-gray-100">
                            <div className="flex items-center gap-2.5">
                                <div className="w-9 h-9 rounded-xl bg-emerald-100 text-[#4B6A34] flex items-center justify-center shrink-0">
                                    {isEditMode ? <Pencil className="w-5 h-5" /> : <Plus className="w-5 h-5" />}
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900 text-base">
                                        {isEditMode ? 'Edit Data Batch Program' : 'Tambah Data Batch Program'}
                                    </h3>
                                    <p className="text-xs text-gray-500">
                                        {isEditMode ? 'Perbarui nama program & tahun angkatan' : 'Input nama program dan tahun angkatan secara manual'}
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => setFormModalOpen(false)}
                                className="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleFormSubmit} className="mt-4 space-y-4">
                            {/* Kategori Program */}
                            <div>
                                <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Kategori Program
                                </label>
                                <select
                                    value={formData.kode_kategori}
                                    onChange={handleCategoryChange}
                                    className="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-xs font-semibold text-gray-900 outline-none focus:ring-2 focus:ring-[#4B6A34] bg-white transition"
                                    required
                                >
                                    {CATEGORY_OPTIONS.map(c => (
                                        <option key={c.code} value={c.code}>
                                            [{c.code}] {c.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Nama Program */}
                            <div>
                                <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Nama Program Batch <span className="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={formData.nama_program}
                                    onChange={(e) => setFormData(prev => ({ ...prev, nama_program: e.target.value }))}
                                    placeholder="Contoh: Field Assistant - I/2026 atau FAT II 2026"
                                    className="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-xs font-semibold text-gray-900 outline-none focus:ring-2 focus:ring-[#4B6A34] transition"
                                    required
                                />
                            </div>

                            {/* Tahun */}
                            <div>
                                <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Tahun Angkatan / Training <span className="text-rose-600">*</span>
                                </label>
                                <input
                                    type="number"
                                    min="1990"
                                    max="2100"
                                    value={formData.tahun}
                                    onChange={(e) => setFormData(prev => ({ ...prev, tahun: e.target.value ? parseInt(e.target.value) : '' }))}
                                    className="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-xs font-semibold text-gray-900 outline-none focus:ring-2 focus:ring-[#4B6A34]"
                                    required
                                />
                            </div>

                            {/* Info Card explaining live derivation */}
                            <div className="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3 text-[11px] text-emerald-900 flex items-start gap-2">
                                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                <div>
                                    <p className="font-bold mb-0.5">Otomatisasi Data Peserta</p>
                                    <p className="text-emerald-800 leading-snug">
                                        Jumlah peserta (Trainee, Culled, Passed, Resign, Eksis) dan distribusi jabatan alumni akan terhitung secara otomatis dari data peserta di tab <strong>Monitoring Asisten</strong>.
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setFormModalOpen(false)}
                                    className="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={saving}
                                    className="px-5 py-2 text-xs font-bold text-white bg-[#4B6A34] hover:bg-[#3d562a] rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                                >
                                    {saving ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <Save className="w-3.5 h-3.5" />}
                                    <span>{saving ? 'Menyimpan...' : 'Simpan Data'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal Hapus Data Batch */}
            {deleteModalOpen && recordToDelete && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 animate-fade-in">
                    <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
                        <div className="flex items-center gap-3 mb-4">
                            <div className="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                <Trash2 className="w-5 h-5" />
                            </div>
                            <div>
                                <h3 className="font-bold text-gray-900 text-base">Hapus Data Batch Program</h3>
                                <p className="text-xs text-gray-500">Konfirmasi penghapusan data</p>
                            </div>
                        </div>

                        <p className="text-xs text-gray-600 mb-6">
                            Apakah Anda yakin ingin menghapus data batch program <strong className="text-gray-900">{recordToDelete.nama_program}</strong>? Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <div className="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                            <button
                                type="button"
                                onClick={() => {
                                    setDeleteModalOpen(false);
                                    setRecordToDelete(null);
                                }}
                                className="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                onClick={handleDeleteSubmit}
                                disabled={deleting}
                                className="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                            >
                                {deleting && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                                <span>{deleting ? 'Menghapus...' : 'Ya, Hapus'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
