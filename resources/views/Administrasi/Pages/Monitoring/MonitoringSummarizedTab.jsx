import React, { useState, useEffect, useMemo } from 'react';
import {
    BarChart3, Users, UserCheck, UserX, Search, Download,
    RefreshCw, ChevronUp, ChevronDown, Filter, AlertCircle, X
} from 'lucide-react';

const CATEGORY_TABS = [
    { code: 'all', name: 'Semua Kategori', label: 'Semua' },
    { code: 'A', name: 'Field Assistant Training', label: 'Field Assistant (FAT)' },
    { code: 'B', name: 'Mill Assistant Training', label: 'Mill Assistant (MAT)' },
    { code: 'C', name: 'Admin Assistant Training', label: 'Admin Assistant (AAT)' },
    { code: 'D', name: 'Traction Assistant Training', label: 'Traction Assistant (TAT)' },
    { code: 'E', name: 'Downstream Management Training', label: 'Downstream (DMT)' },
];

export default function MonitoringSummarizedTab() {
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

    // Fetch Data (Strictly aggregated mode)
    const fetchData = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                kategori: selectedCategory,
                tahun: selectedTahun,
                search: searchQuery,
                view_mode: 'aggregated',
            });

            const res = await fetch(`/administrasi/monitoring-summarized/api/data?${params.toString()}`);
            const result = await res.json();

            if (result.status === 'success') {
                setData(result.data || []);
                setSummary(result.summary || {});
                setFilterOptions(result.filters || { kategori: [], tahun: [] });
            }
        } catch (err) {
            console.error('Error fetching summarized monitoring data:', err);
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
            const res = await fetch(`/administrasi/monitoring-summarized/api/drilldown?${params.toString()}`);
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
            view_mode: 'aggregated',
        });
        window.location.href = `/administrasi/monitoring-summarized/api/export?${params.toString()}`;
    };

    return (
        <div className="space-y-6">
            {/* Header & Export Controls */}
            <div className="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-100 text-[#4B6A34] flex items-center justify-center font-bold shadow-xs">
                        <BarChart3 className="w-6 h-6" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold text-gray-900 tracking-tight">
                            Monitoring Summarized
                        </h2>
                        <p className="text-xs text-gray-500 font-medium">
                            Konsolidasi dan agregasi data alumni per kelompok program & tahun kelulusan
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
                        <span>Export Excel Summarized</span>
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

            {/* KPI Summary Cards */}
            <div className="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
                <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
                    <span className="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">
                        Total Kelompok Program
                    </span>
                    <p className="text-2xl font-bold text-gray-900">
                        {summary.total_batches || 0} <span className="text-xs text-gray-400 font-normal">Kelompok</span>
                    </p>
                    <p className="text-[11px] text-gray-400 mt-0.5">Agregasi per program & tahun</p>
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
                        placeholder="Cari program atau tahun lulus (misal: FAT 2021, MAT 2019)..."
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

            {/* Main Aggregated Table: 21 Columns */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div className="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <span className="font-semibold text-gray-800">
                        Menampilkan {sortedData.length} Kelompok Program Teragregasi
                    </span>
                    <span className="text-[11px] text-gray-400">Klik angka Eksis/Resign/Posisi untuk melihat rincian nama peserta</span>
                </div>

                <div className="overflow-x-auto max-h-[700px]">
                    <table className="w-full text-left text-[11px] border-collapse">
                        <thead className="bg-gray-100 text-gray-700 uppercase font-bold sticky top-0 z-20 shadow-xs select-none">
                            <tr>
                                <th rowSpan={2} className="px-3 py-3 text-center border-r border-gray-200 w-10">No</th>
                                <th rowSpan={2} className="px-3 py-3 border-r border-gray-200 min-w-[220px]">
                                    Program & Tahun Kelulusan
                                </th>
                                <th colSpan={3} className="px-3 py-2 text-center border-r border-gray-200 bg-gray-200/70">
                                    Periode Tahun
                                </th>
                                <th colSpan={5} className="px-3 py-2 text-center border-r border-gray-200 bg-blue-100/70 text-blue-900">Peserta Training</th>
                                <th colSpan={4} className="px-3 py-2 text-center border-r border-gray-200 bg-emerald-100/70 text-emerald-900">Kalkulasi % (Formula / Trainee)</th>
                                <th colSpan={5} className="px-3 py-2 text-center bg-purple-100/70 text-purple-900">Distribusi Posisi Terakhir (Eksis)</th>
                            </tr>
                            <tr className="border-t border-gray-200 text-[10px]">
                                {/* Periode */}
                                <th className="px-2 py-2 text-center border-r border-gray-200">Rekrut</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200">Training</th>
                                <th className="px-2 py-2 text-center border-r border-gray-200 font-extrabold text-gray-900 bg-gray-300/40">
                                    Th. Lulus
                                </th>
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
                                <th className="px-2 py-2 text-center bg-purple-50">Asisten</th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-gray-200 font-medium">
                            {loading ? (
                                <tr>
                                    <td colSpan={21} className="px-4 py-16 text-center text-gray-500">
                                        <RefreshCw className="w-7 h-7 text-[#4B6A34] animate-spin mx-auto mb-2" />
                                        <p className="font-semibold">Mengagregasi data kelulusan & retensi per kelompok tahun...</p>
                                    </td>
                                </tr>
                            ) : sortedData.length === 0 ? (
                                <tr>
                                    <td colSpan={21} className="px-4 py-12 text-center text-gray-400">
                                        <AlertCircle className="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                        <p className="font-semibold text-gray-600">Tidak ada data program yang ditemukan.</p>
                                    </td>
                                </tr>
                            ) : (
                                sortedData.map((row, idx) => {
                                    const targetDrilldownProgram = row.batch_names_str || row.nama_program;

                                    return (
                                        <tr key={row.id || idx} className="hover:bg-gray-50/80 transition-colors">
                                            {/* 1. No */}
                                            <td className="px-3 py-2 text-center text-gray-400 font-mono border-r border-gray-100">
                                                {row.no || idx + 1}
                                            </td>

                                            {/* 4. Nama Program & Badges */}
                                            <td className="px-3 py-2 border-r border-gray-100">
                                                <div className="font-bold text-gray-900 flex items-center gap-1.5 flex-wrap">
                                                    <span>{row.nama_program}</span>
                                                    {row.batch_count > 1 && (
                                                        <span className="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 text-blue-800" title={`Menggabungkan: ${row.batch_names_str}`}>
                                                            {row.batch_count} Batch
                                                        </span>
                                                    )}
                                                </div>
                                                <span className="text-[10px] text-gray-400 block">
                                                    [{row.kode_kategori}] {row.kategori_program}
                                                </span>
                                                {row.batch_names_str && (
                                                    <span className="text-[9.5px] text-gray-500/80 truncate block max-w-xs" title={row.batch_names_str}>
                                                        {row.batch_names_str}
                                                    </span>
                                                )}
                                            </td>

                                            {/* 5, 6, 7. Tahun Rekrut, Training, Lulus */}
                                            <td className="px-2 py-2 text-center text-gray-600 border-r border-gray-100">{row.tahun_rekrut || '-'}</td>
                                            <td className="px-2 py-2 text-center text-gray-600 border-r border-gray-100">{row.tahun_training || '-'}</td>
                                            <td className="px-2 py-2 text-center font-bold text-gray-900 border-r border-gray-100 bg-gray-50/50">{row.tahun_lulus || '-'}</td>

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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'resign', `Resign - ${row.nama_program}`)}
                                                        className="px-2 py-0.5 rounded-full text-xs font-bold text-rose-700 hover:bg-rose-200 bg-rose-100 transition"
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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'eksis', `Eksis/Aktif - ${row.nama_program}`)}
                                                        className="px-2 py-0.5 rounded-full text-xs font-bold text-emerald-800 hover:bg-emerald-200 bg-emerald-100 transition"
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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'pc', `Posisi PC - ${row.nama_program}`)}
                                                        className="font-bold text-purple-700 hover:underline"
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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'gm', `Posisi GM - ${row.nama_program}`)}
                                                        className="font-bold text-purple-700 hover:underline"
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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'mgr', `Posisi MGR - ${row.nama_program}`)}
                                                        className="font-bold text-purple-700 hover:underline"
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
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'askep', `Posisi Askep - ${row.nama_program}`)}
                                                        className="font-bold text-purple-700 hover:underline"
                                                    >
                                                        {row.posisi_askep}
                                                    </button>
                                                ) : <span className="text-gray-300">-</span>}
                                            </td>

                                            {/* 21. Posisi Asisten */}
                                            <td className="px-2 py-2 text-center bg-purple-50/20">
                                                {row.posisi_asisten > 0 ? (
                                                    <button
                                                        type="button"
                                                        onClick={() => openDrilldown(targetDrilldownProgram, 'asisten', `Posisi Asisten - ${row.nama_program}`)}
                                                        className="font-bold text-purple-700 hover:underline"
                                                    >
                                                        {row.posisi_asisten}
                                                    </button>
                                                ) : <span className="text-gray-300">-</span>}
                                            </td>
                                        </tr>
                                    );
                                })
                            )}
                        </tbody>

                        {/* TOTAL ROW */}
                        {sortedData.length > 0 && (
                            <tfoot className="bg-gray-900 text-white font-bold text-[11px] sticky bottom-0 z-20">
                                <tr>
                                    <td colSpan={2} className="px-3 py-3 text-center uppercase tracking-wider border-r border-gray-800">
                                        TOTAL ({summary.total_batches} Kelompok)
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
                                    <td className="px-2 py-3 text-center text-purple-200">
                                        {summary.posisi_asisten_total}
                                    </td>
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
                                className="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
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
                                className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
