import React, { useState, useEffect, useMemo, useRef } from 'react';
import {
    Users,
    UserCheck,
    CheckCircle2,
    Briefcase,
    TrendingUp,
    Download,
    Upload,
    Plus,
    PlusCircle,
    Calendar,
    Search,
    Trash2,
    Edit3,
    Eye,
    RefreshCw,
    X,
    ChevronDown,
    ChevronUp,
    ChevronLeft,
    ChevronRight,
    AlertCircle,
    Check,
    FileSpreadsheet,
    Building2,
    Award,
    Activity,
    Globe,
    Layers
} from 'lucide-react';
import {
    ResponsiveContainer,
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    AreaChart,
    Area,
    Cell
} from 'recharts';

export default function RekrutmenApp() {
    const canEditCost = window.canEditCost !== undefined ? window.canEditCost : true;

    // State Data & Loading
    const [data, setData] = useState([]);
    const [summary, setSummary] = useState(null);
    const [funnel, setFunnel] = useState([]);
    const [chartData, setChartData] = useState({ by_location: [], by_year: [], by_metode: [] });
    const [filterOptions, setFilterOptions] = useState({ locations: [], years: [], programs: [], methods: ['Online', 'Offline'] });
    const [pagination, setPagination] = useState({ total: 0, per_page: 25, current_page: 1, last_page: 1 });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [notification, setNotification] = useState(null);

    // Filters
    const [selectedLocations, setSelectedLocations] = useState([]);
    const [selectedMetode, setSelectedMetode] = useState('all'); // 'all' | 'Online' | 'Offline'
    const [selectedYear, setSelectedYear] = useState('all');
    const [selectedProgram, setSelectedProgram] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');
    const [sortBy, setSortBy] = useState('no');
    const [sortOrder, setSortOrder] = useState('asc');
    const [perPage, setPerPage] = useState(25);
    const [currentPage, setCurrentPage] = useState(1);

    // Modals
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [isDetailOpen, setIsDetailOpen] = useState(false);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [isImportOpen, setIsImportOpen] = useState(false);
    const [isImportResultOpen, setIsImportResultOpen] = useState(false);
    const [importSummary, setImportSummary] = useState(null);

    // Year Navigation & Tambah Tahun State
    const yearTabsRef = useRef(null);
    const [isTambahTahunOpen, setIsTambahTahunOpen] = useState(false);
    const [tambahTahunInput, setTambahTahunInput] = useState('');

    const sortedYears = useMemo(() => {
        const raw = filterOptions.years || [];
        const numYears = raw.map(y => parseInt(y, 10)).filter(n => !isNaN(n));
        if (selectedYear !== 'all' && !numYears.includes(parseInt(selectedYear, 10)) && !isNaN(parseInt(selectedYear, 10))) {
            numYears.push(parseInt(selectedYear, 10));
        }
        numYears.sort((a, b) => a - b);
        return Array.from(new Set(numYears));
    }, [filterOptions.years, selectedYear]);

    const scrollYearTabs = (direction) => {
        if (yearTabsRef.current) {
            yearTabsRef.current.scrollBy({ left: direction === 'left' ? -240 : 240, behavior: 'smooth' });
        }
    };

    const handleTambahTahunSubmit = (e) => {
        e.preventDefault();
        const y = parseInt(tambahTahunInput, 10);
        if (!y || isNaN(y)) return;
        if (!filterOptions.years.includes(y) && !filterOptions.years.includes(String(y))) {
            setFilterOptions(prev => ({
                ...prev,
                years: [...(prev.years || []), y].sort((a, b) => a - b)
            }));
        }
        setSelectedYear(String(y));
        setCurrentPage(1);
        setIsTambahTahunOpen(false);
        setTambahTahunInput('');
    };

    // Current Item for Edit/Detail/Delete
    const [selectedItem, setSelectedItem] = useState(null);

    // Form State
    const [formData, setFormData] = useState({
        no: '',
        lokasi_rekrut: '',
        metode_rekrutmen: 'Offline',
        program: '',
        tanggal: new Date().toISOString().split('T')[0],
        tahun: new Date().getFullYear(),
        jumlah_peserta_hadir: 0,
        jumlah_lolos_psikotest: 0,
        jumlah_hadir_interview_awal: 0,
        jumlah_lolos_interview_awal: 0,
        jumlah_lolos_interview_user: 0,
        jumlah_ikut_mcu: 0,
        jumlah_lolos_mcu: 0,
        join_fr: 0,
    });
    const [formErrors, setFormErrors] = useState({});

    // Import State
    const [importFile, setImportFile] = useState(null);
    const [importMode, setImportMode] = useState('append'); // 'append' | 'replace'
    const [importPreview, setImportPreview] = useState([]);
    const [importLoading, setImportLoading] = useState(false);

    // Get CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Show flash notification helper
    const notify = (message, type = 'success') => {
        setNotification({ message, type });
        setTimeout(() => setNotification(null), 4500);
    };

    // Fetch data from API
    const fetchData = async () => {
        setLoading(true);
        setError(null);
        try {
            const params = new URLSearchParams();
            if (selectedLocations.length > 0) {
                selectedLocations.forEach(loc => params.append('lokasi[]', loc));
            }
            if (selectedMetode !== 'all') params.append('metode_rekrutmen', selectedMetode);
            if (selectedYear !== 'all') params.append('tahun', selectedYear);
            if (selectedProgram !== 'all') params.append('program', selectedProgram);
            if (searchQuery.trim()) params.append('search', searchQuery.trim());
            params.append('sort_by', sortBy);
            params.append('sort_order', sortOrder);
            params.append('per_page', perPage);
            params.append('page', currentPage);

            const res = await fetch(`/administrasi/rekrutmen/api/data?${params.toString()}`);
            const result = await res.json();

            if (result.status === 'success') {
                setData(result.data || []);
                setSummary(result.summary || null);
                setFunnel(result.funnel || []);
                setChartData(result.charts || { by_location: [], by_year: [], by_metode: [] });
                setFilterOptions(result.filters || { locations: [], years: [], programs: [], methods: ['Online', 'Offline'] });
                setPagination(result.pagination || { total: 0, per_page: 25, current_page: 1, last_page: 1 });
            } else {
                setError(result.message || 'Gagal memuat data');
            }
        } catch (err) {
            setError('Terjadi kesalahan jaringan saat mengambil data.');
        } finally {
            setLoading(false);
        }
    };

    // Trigger fetch on filter or pagination changes
    useEffect(() => {
        fetchData();
    }, [selectedLocations, selectedMetode, selectedYear, selectedProgram, searchQuery, sortBy, sortOrder, perPage, currentPage]);

    // Format Percent Helper
    const formatPercent = (decimal) => {
        if (decimal === null || decimal === undefined) return '0.00%';
        const num = typeof decimal === 'string' ? parseFloat(decimal) : decimal;
        return `${(num * 100).toFixed(2)}%`;
    };

    // Handle Column Sorting
    const handleSort = (columnKey) => {
        if (sortBy === columnKey) {
            setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(columnKey);
            setSortOrder('asc');
        }
        setCurrentPage(1);
    };

    // Location filter toggle helper
    const toggleLocationFilter = (loc) => {
        setCurrentPage(1);
        if (loc === 'all') {
            setSelectedLocations([]);
            return;
        }
        if (selectedLocations.includes(loc)) {
            setSelectedLocations(selectedLocations.filter(l => l !== loc));
        } else {
            setSelectedLocations([...selectedLocations, loc]);
        }
    };

    // Toggle Online Filter
    const toggleOnlineOnly = () => {
        setCurrentPage(1);
        if (selectedMetode === 'Online') {
            setSelectedMetode('all');
        } else {
            setSelectedMetode('Online');
        }
    };

    // Reset all filters
    const handleResetFilters = () => {
        setSelectedLocations([]);
        setSelectedMetode('all');
        setSelectedYear('all');
        setSelectedProgram('all');
        setSearchQuery('');
        setSortBy('no');
        setSortOrder('asc');
        setCurrentPage(1);
    };

    // Open Create Modal
    const openCreate = () => {
        const nextNo = (pagination.total || 0) + 1;
        setFormData({
            no: nextNo,
            lokasi_rekrut: '',
            metode_rekrutmen: 'Offline',
            program: '',
            tanggal: new Date().toISOString().split('T')[0],
            tahun: new Date().getFullYear(),
            jumlah_peserta_hadir: 0,
            jumlah_lolos_psikotest: 0,
            jumlah_hadir_interview_awal: 0,
            jumlah_lolos_interview_awal: 0,
            jumlah_lolos_interview_user: 0,
            jumlah_ikut_mcu: 0,
            jumlah_lolos_mcu: 0,
            join_fr: 0,
        });
        setFormErrors({});
        setIsCreateOpen(true);
    };

    // Open Edit Modal
    const openEdit = (item) => {
        setSelectedItem(item);
        setFormData({
            no: item.no || '',
            lokasi_rekrut: item.lokasi_rekrut || '',
            metode_rekrutmen: item.metode_rekrutmen || 'Offline',
            program: item.program || '',
            tanggal: item.tanggal || '',
            tahun: item.tahun || new Date().getFullYear(),
            jumlah_peserta_hadir: item.jumlah_peserta_hadir || 0,
            jumlah_lolos_psikotest: item.jumlah_lolos_psikotest || 0,
            jumlah_hadir_interview_awal: item.jumlah_hadir_interview_awal || 0,
            jumlah_lolos_interview_awal: item.jumlah_lolos_interview_awal || 0,
            jumlah_lolos_interview_user: item.jumlah_lolos_interview_user || 0,
            jumlah_ikut_mcu: item.jumlah_ikut_mcu || 0,
            jumlah_lolos_mcu: item.jumlah_lolos_mcu || 0,
            join_fr: item.join_fr || 0,
        });
        setFormErrors({});
        setIsEditOpen(true);
    };

    // Open Detail Modal
    const openDetail = (item) => {
        setSelectedItem(item);
        setIsDetailOpen(true);
    };

    // Open Delete Modal
    const openDelete = (item) => {
        setSelectedItem(item);
        setIsDeleteOpen(true);
    };

    // Calculate Form Live % Lulus
    const computedFormLulus = useMemo(() => {
        const hadir = parseInt(formData.jumlah_peserta_hadir, 10) || 0;
        const join = parseInt(formData.join_fr, 10) || 0;
        if (hadir <= 0) return '0.00%';
        return `${((join / hadir) * 100).toFixed(2)}%`;
    }, [formData.jumlah_peserta_hadir, formData.join_fr]);

    // Handle Form Submit (Create / Edit)
    const handleFormSubmit = async (e) => {
        e.preventDefault();
        setFormErrors({});

        const hadir = parseInt(formData.jumlah_peserta_hadir, 10) || 0;
        const psiko = parseInt(formData.jumlah_lolos_psikotest, 10) || 0;
        const hadirInt = parseInt(formData.jumlah_hadir_interview_awal, 10) || 0;
        const lolosInt = parseInt(formData.jumlah_lolos_interview_awal, 10) || 0;
        const intUser = parseInt(formData.jumlah_lolos_interview_user, 10) || 0;
        const ikutMcu = parseInt(formData.jumlah_ikut_mcu, 10) || 0;
        const lolosMcu = parseInt(formData.jumlah_lolos_mcu, 10) || 0;
        const join = parseInt(formData.join_fr, 10) || 0;

        // Front validation check
        const errors = {};
        if (!formData.lokasi_rekrut.trim()) errors.lokasi_rekrut = 'Lokasi rekrut wajib diisi';
        if (!formData.metode_rekrutmen) errors.metode_rekrutmen = 'Metode rekrutmen wajib dipilih';
        if (!formData.program.trim()) errors.program = 'Program rekrutmen wajib diisi';
        if (!formData.tanggal.trim()) errors.tanggal = 'Tanggal wajib diisi';
        if (!formData.tahun) errors.tahun = 'Tahun wajib diisi';
        if (hadir < 0) errors.jumlah_peserta_hadir = 'Angka tidak boleh negatif';
        if (psiko > hadir) errors.jumlah_lolos_psikotest = `Tidak boleh melebihi peserta hadir (${hadir})`;
        if (hadirInt > hadir) errors.jumlah_hadir_interview_awal = `Tidak boleh melebihi peserta hadir (${hadir})`;
        if (lolosInt > hadirInt && hadirInt > 0) errors.jumlah_lolos_interview_awal = `Tidak boleh melebihi hadir interview (${hadirInt})`;
        if (intUser > hadir) errors.jumlah_lolos_interview_user = `Tidak boleh melebihi peserta hadir (${hadir})`;
        if (ikutMcu > hadir) errors.jumlah_ikut_mcu = `Tidak boleh melebihi peserta hadir (${hadir})`;
        if (lolosMcu > ikutMcu && ikutMcu > 0) errors.jumlah_lolos_mcu = `Tidak boleh melebihi ikut MCU (${ikutMcu})`;
        if (join > hadir) errors.join_fr = `Tidak boleh melebihi peserta hadir (${hadir})`;

        if (Object.keys(errors).length > 0) {
            setFormErrors(errors);
            return;
        }

        try {
            const url = isEditOpen
                ? `/administrasi/rekrutmen/api/${selectedItem.id}`
                : `/administrasi/rekrutmen/api/store`;
            const method = isEditOpen ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const resData = await res.json();

            if (res.ok && resData.status === 'success') {
                notify(resData.message || (isEditOpen ? 'Data berhasil diperbarui!' : 'Data baru berhasil disimpan!'));
                setIsCreateOpen(false);
                setIsEditOpen(false);
                fetchData();
            } else {
                if (resData.errors) {
                    setFormErrors(resData.errors);
                } else {
                    notify(resData.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                }
            }
        } catch (err) {
            notify('Gagal menghubungi server.', 'error');
        }
    };

    // Handle Delete Confirm
    const handleDeleteConfirm = async () => {
        if (!selectedItem) return;
        try {
            const res = await fetch(`/administrasi/rekrutmen/api/${selectedItem.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            const result = await res.json();
            if (res.ok && result.status === 'success') {
                notify(result.message || 'Data berhasil dihapus');
                setIsDeleteOpen(false);
                fetchData();
            } else {
                notify(result.message || 'Gagal menghapus data', 'error');
            }
        } catch (err) {
            notify('Terjadi kesalahan saat menghapus data.', 'error');
        }
    };

    // Handle CSV File Selection and Preview
    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (!file) return;
        setImportFile(file);

        const reader = new FileReader();
        reader.onload = (event) => {
            const text = event.target.result;
            const lines = text.split('\n').filter(l => l.trim().length > 0);
            const previewRows = lines.slice(0, 6).map(l => {
                return l.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g) || l.split(',');
            });
            setImportPreview(previewRows);
        };
        reader.readAsText(file);
    };

    // Handle CSV Import Submit
    const handleImportSubmit = async (e) => {
        e.preventDefault();
        if (!importFile) {
            notify('Silakan pilih file CSV terlebih dahulu', 'error');
            return;
        }

        setImportLoading(true);
        const form = new FormData();
        form.append('file', importFile);
        form.append('mode', importMode);

        try {
            const res = await fetch('/administrasi/rekrutmen/api/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: form,
            });

            const result = await res.json();
            setImportLoading(false);

            if (res.ok && result.status === 'success') {
                setIsImportOpen(false);
                setImportSummary(result);
                setIsImportResultOpen(true);
                setImportFile(null);
                setImportPreview([]);
                fetchData();
            } else {
                setImportSummary(result);
                setIsImportResultOpen(true);
            }
        } catch (err) {
            setImportLoading(false);
            notify('Gagal mengunggah file CSV.', 'error');
        }
    };

    // Handle CSV Export
    const handleExport = () => {
        const params = new URLSearchParams();
        if (selectedLocations.length > 0) {
            selectedLocations.forEach(loc => params.append('lokasi[]', loc));
        }
        if (selectedMetode !== 'all') params.append('metode_rekrutmen', selectedMetode);
        if (selectedYear !== 'all') params.append('tahun', selectedYear);
        if (selectedProgram !== 'all') params.append('program', selectedProgram);
        if (searchQuery.trim()) params.append('search', searchQuery.trim());

        window.location.href = `/administrasi/rekrutmen/api/export?${params.toString()}`;
    };

    // Funnel colors
    const funnelColors = ['#2563EB', '#3B82F6', '#60A5FA', '#10B981', '#059669', '#F59E0B', '#D97706', '#4B6A34'];

    // Method Stats Calculation Helper
    const onlineStats = useMemo(() => {
        return chartData.by_metode?.find(m => m.metode === 'Online') || {
            batch_count: 0, total_peserta: 0, total_join: 0, persen_lulus: 0
        };
    }, [chartData.by_metode]);

    const offlineStats = useMemo(() => {
        return chartData.by_metode?.find(m => m.metode === 'Offline') || {
            batch_count: 0, total_peserta: 0, total_join: 0, persen_lulus: 0
        };
    }, [chartData.by_metode]);

    return (
        <div className="space-y-6 pb-12">
            {/* Flash Notification */}
            {notification && (
                <div className={`fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg border text-sm font-medium transition-all transform animate-in slide-in-from-top-2 ${
                    notification.type === 'error'
                        ? 'bg-red-50 text-red-700 border-red-200'
                        : 'bg-emerald-50 text-emerald-800 border-emerald-200'
                }`}>
                    {notification.type === 'error' ? <AlertCircle className="w-5 h-5 text-red-500" /> : <CheckCircle2 className="w-5 h-5 text-emerald-600" />}
                    <span>{notification.message}</span>
                    <button onClick={() => setNotification(null)} className="ml-2 text-slate-400 hover:text-slate-600">
                        <X className="w-4 h-4" />
                    </button>
                </div>
            )}

            {/* Top Page Header */}
            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2.5">
                            <div className="p-2.5 bg-[#4B6A34]/10 rounded-xl text-[#4B6A34]">
                                <Users className="w-6 h-6" />
                            </div>
                            <div>
                                <h1 className="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Data Rekrutmen Karyawan</h1>
                                <p className="text-sm text-slate-500 mt-0.5">
                                    Kelola data batch rekrutmen Onsite (Per Kota) dan Online, funnel seleksi, serta konversi kelulusan.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex flex-wrap items-center gap-2.5">
                        {canEditCost && (
                            <>
                                <button
                                    onClick={openCreate}
                                    className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#4B6A34] text-white text-sm font-semibold hover:bg-[#3d562a] shadow-xs transition-all cursor-pointer"
                                >
                                    <Plus className="w-4 h-4" />
                                    <span>Tambah Batch</span>
                                </button>
                                <button
                                    onClick={() => setIsImportOpen(true)}
                                    className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition-all cursor-pointer"
                                >
                                    <Upload className="w-4 h-4 text-slate-500" />
                                    <span>Import CSV</span>
                                </button>
                            </>
                        )}
                        <button
                            onClick={handleExport}
                            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-xs transition-all cursor-pointer"
                        >
                            <Download className="w-4 h-4 text-slate-500" />
                            <span>Export Excel</span>
                        </button>
                        <button
                            onClick={fetchData}
                            disabled={loading}
                            className="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all cursor-pointer"
                            title="Refresh Data"
                        >
                            <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin text-[#4B6A34]' : ''}`} />
                        </button>
                    </div>
                </div>
            </div>

            {/* Year Filter Navigation Card */}
            <div className="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-xs">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-2 flex-1 min-w-0">
                        <div className="flex items-center gap-1.5 px-2 text-slate-500 shrink-0">
                            <Calendar className="w-4 h-4 text-[#4B6A34]" />
                            <span className="text-xs font-bold text-slate-900 uppercase tracking-wider">Tahun:</span>
                        </div>

                        {/* Scroll Left Button */}
                        <button
                            type="button"
                            onClick={() => scrollYearTabs('left')}
                            className="p-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition-colors shrink-0 cursor-pointer"
                            title="Scroll ke tahun sebelumnya"
                        >
                            <ChevronLeft className="w-4 h-4" />
                        </button>

                        {/* Horizontal Scrollable List */}
                        <div
                            ref={yearTabsRef}
                            className="flex items-center gap-1.5 overflow-x-auto py-1 px-1 scroll-smooth"
                            style={{ scrollbarWidth: 'thin' }}
                        >
                            <button
                                type="button"
                                onClick={() => { setSelectedYear('all'); setCurrentPage(1); }}
                                className={`px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap cursor-pointer ${
                                    selectedYear === 'all'
                                        ? 'bg-[#4B6A34] text-white shadow-xs'
                                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50 border border-transparent hover:border-slate-200'
                                }`}
                            >
                                Semua Tahun
                            </button>
                            {sortedYears.map((y) => {
                                const isSelected = String(selectedYear) === String(y);
                                return (
                                    <button
                                        key={y}
                                        type="button"
                                        onClick={() => { setSelectedYear(String(y)); setCurrentPage(1); }}
                                        className={`px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap cursor-pointer ${
                                            isSelected
                                                ? 'bg-[#4B6A34] text-white shadow-xs'
                                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50 border border-transparent hover:border-slate-200'
                                        }`}
                                    >
                                        {y}
                                    </button>
                                );
                            })}
                        </div>

                        {/* Scroll Right Button */}
                        <button
                            type="button"
                            onClick={() => scrollYearTabs('right')}
                            className="p-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition-colors shrink-0 cursor-pointer"
                            title="Scroll ke tahun berikutnya"
                        >
                            <ChevronRight className="w-4 h-4" />
                        </button>
                    </div>

                    {/* Tambah Tahun Button */}
                    {canEditCost && (
                        <div className="shrink-0 flex items-center gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    const maxY = sortedYears.length > 0 ? Math.max(...sortedYears) + 1 : new Date().getFullYear();
                                    setTambahTahunInput(String(maxY));
                                    setIsTambahTahunOpen(true);
                                }}
                                className="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#4B6A34] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 flex items-center gap-1.5 transition-colors shadow-2xs cursor-pointer"
                            >
                                <PlusCircle className="w-4 h-4" />
                                <span>+ Tambah Tahun</span>
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Region, Location & Mode Filter Pill Bar */}
            <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div className="flex items-center gap-2">
                        <Building2 className="w-4 h-4 text-[#4B6A34]" />
                        <span className="text-xs font-bold uppercase tracking-wider text-slate-700">Filter Lokasi & Metode Rekrutmen</span>
                        {(selectedLocations.length > 0 || selectedMetode !== 'all') && (
                            <span className="bg-[#4B6A34] text-white text-[11px] font-bold px-2 py-0.5 rounded-full">
                                {selectedLocations.length + (selectedMetode !== 'all' ? 1 : 0)} filter aktif
                            </span>
                        )}
                    </div>
                    {(selectedLocations.length > 0 || selectedMetode !== 'all' || selectedYear !== 'all' || selectedProgram !== 'all' || searchQuery) && (
                        <button
                            onClick={handleResetFilters}
                            className="text-xs text-[#4B6A34] hover:underline font-semibold flex items-center gap-1 cursor-pointer"
                        >
                            <X className="w-3.5 h-3.5" />
                            <span>Reset Semua Filter</span>
                        </button>
                    )}
                </div>

                {/* Quick Selection Chips with Prominent Yellow RECRUITMENT ONLINE Button */}
                <div className="flex flex-wrap items-center gap-2">
                    {/* ALL LOCATIONS BUTTON */}
                    <button
                        onClick={() => toggleLocationFilter('all')}
                        className={`px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                            selectedLocations.length === 0
                                ? 'bg-[#4B6A34] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        }`}
                    >
                        Semua Lokasi
                    </button>

                    {/* PROMINENT YELLOW RECRUITMENT ONLINE BUTTON (As shown in user image) */}
                    <button
                        onClick={toggleOnlineOnly}
                        className={`px-3.5 py-1.5 rounded-lg text-xs font-black tracking-wider uppercase transition-all flex items-center gap-1.5 cursor-pointer border ${
                            selectedMetode === 'Online'
                                ? 'bg-yellow-300 text-black border-yellow-500 shadow-md ring-2 ring-yellow-400 font-extrabold'
                                : 'bg-yellow-100/90 text-yellow-900 border-yellow-300 hover:bg-yellow-200 font-bold'
                        }`}
                        title="Filter hanya data Recruitment Online"
                    >
                        <span>⚡ RECRUITMENT ONLINE</span>
                        {selectedMetode === 'Online' && <Check className="w-3.5 h-3.5 text-black stroke-[3]" />}
                    </button>

                    {/* REGIONAL / CITY LOCATIONS CHIPS */}
                    {filterOptions.locations.map((loc) => {
                        const isSelected = selectedLocations.includes(loc);
                        const isMulti = loc.includes(',');
                        return (
                            <button
                                key={loc}
                                onClick={() => toggleLocationFilter(loc)}
                                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 cursor-pointer ${
                                    isSelected
                                        ? 'bg-[#4B6A34]/15 text-[#4B6A34] border border-[#4B6A34]/30 font-semibold'
                                        : isMulti
                                            ? 'bg-amber-50/70 border border-amber-200 text-amber-900 hover:bg-amber-100'
                                            : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100'
                                }`}
                            >
                                <span>{loc}</span>
                                {isSelected && <Check className="w-3 h-3 text-[#4B6A34]" />}
                            </button>
                        );
                    })}
                </div>

                {/* Secondary Filters (Metode, Tahun, Program, Search) */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-2">
                    {/* METODE REKRUTMEN FILTER (Revisi 2) */}
                    <div>
                        <label className="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Metode Rekrutmen
                        </label>
                        <select
                            value={selectedMetode}
                            onChange={(e) => { setSelectedMetode(e.target.value); setCurrentPage(1); }}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                        >
                            <option value="all">Semua Metode</option>
                            <option value="Online">🌐 Online</option>
                            <option value="Offline">🏢 Offline (Onsite)</option>
                        </select>
                    </div>

                    {/* Tahun Filter */}
                    <div>
                        <label className="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Tahun
                        </label>
                        <select
                            value={selectedYear}
                            onChange={(e) => { setSelectedYear(e.target.value); setCurrentPage(1); }}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                        >
                            <option value="all">Semua Tahun</option>
                            {filterOptions.years.map((y) => (
                                <option key={y} value={y}>{y}</option>
                            ))}
                        </select>
                    </div>

                    {/* Program Filter */}
                    <div>
                        <label className="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Program / Batch
                        </label>
                        <select
                            value={selectedProgram}
                            onChange={(e) => { setSelectedProgram(e.target.value); setCurrentPage(1); }}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none truncate"
                        >
                            <option value="all">Semua Program</option>
                            {filterOptions.programs.map((p) => (
                                <option key={p} value={p}>{p}</option>
                            ))}
                        </select>
                    </div>

                    {/* Live Search */}
                    <div className="md:col-span-2">
                        <label className="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                            Pencarian Cepat
                        </label>
                        <div className="relative">
                            <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => { setSearchQuery(e.target.value); setCurrentPage(1); }}
                                placeholder="Cari program, lokasi, metode, tanggal..."
                                className="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                            />
                            {searchQuery && (
                                <button
                                    onClick={() => setSearchQuery('')}
                                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                >
                                    <X className="w-3.5 h-3.5" />
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* KPI Summary Cards */}
            {summary && (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    {/* Card 1: Peserta Hadir */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peserta Hadir</span>
                            <div className="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <Users className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-slate-900 mt-2">
                            {summary.total_peserta_hadir.toLocaleString('id-ID')}
                        </p>
                        <p className="text-[11px] text-slate-500 mt-1 font-medium">
                            Dari {summary.batch_count} batch terfilter
                        </p>
                    </div>

                    {/* Card 2: Lolos Psikotest */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lolos Psikotest</span>
                            <div className="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                                <CheckCircle2 className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-slate-900 mt-2">
                            {summary.total_lolos_psikotest.toLocaleString('id-ID')}
                        </p>
                        <p className="text-[11px] text-indigo-600 font-semibold mt-1">
                            {summary.total_peserta_hadir > 0
                                ? `${((summary.total_lolos_psikotest / summary.total_peserta_hadir) * 100).toFixed(1)}% dari hadir`
                                : '0%'}
                        </p>
                    </div>

                    {/* Card 3: Lolos Int User */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lolos Int User</span>
                            <div className="p-2 bg-amber-50 text-amber-600 rounded-xl">
                                <Briefcase className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-slate-900 mt-2">
                            {summary.total_lolos_interview_user.toLocaleString('id-ID')}
                        </p>
                        <p className="text-[11px] text-amber-600 font-semibold mt-1">
                            {summary.total_peserta_hadir > 0
                                ? `${((summary.total_lolos_interview_user / summary.total_peserta_hadir) * 100).toFixed(1)}% dari hadir`
                                : '0%'}
                        </p>
                    </div>

                    {/* Card 4: Join FR */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Join FR</span>
                            <div className="p-2 bg-emerald-50 text-emerald-700 rounded-xl">
                                <UserCheck className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-emerald-800 mt-2">
                            {summary.total_join_fr.toLocaleString('id-ID')}
                        </p>
                        <p className="text-[11px] text-emerald-700 font-semibold mt-1">
                            Karyawan baru bergabung
                        </p>
                    </div>

                    {/* Card 5: Overall % Lulus */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/50">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">% Lulus (Akurat)</span>
                            <div className="p-2 bg-[#4B6A34]/15 text-[#4B6A34] rounded-xl">
                                <Award className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-[#4B6A34] mt-2">
                            {summary.overall_pass_rate_pct}%
                        </p>
                        <p className="text-[11px] text-slate-500 font-medium mt-1">
                            Total Join / Total Hadir
                        </p>
                    </div>

                    {/* Card 6: Average % Lulus */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs relative overflow-hidden">
                        <div className="flex items-center justify-between">
                            <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-rata % Batch</span>
                            <div className="p-2 bg-purple-50 text-purple-600 rounded-xl">
                                <TrendingUp className="w-4 h-4" />
                            </div>
                        </div>
                        <p className="text-2xl font-bold text-purple-700 mt-2">
                            {summary.avg_pass_rate_pct}%
                        </p>
                        <p className="text-[11px] text-slate-500 font-medium mt-1">
                            Rata-rata % lulus per batch
                        </p>
                    </div>
                </div>
            )}

            {/* DASHBOARD REVISI 2: Perbandingan Online vs Offline Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {/* ONLINE METRICS CARD */}
                <div className="bg-gradient-to-r from-yellow-50 to-amber-50/60 rounded-2xl border border-yellow-200 p-5 shadow-xs relative">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <span className="px-2.5 py-1 rounded-lg bg-yellow-300 text-black border border-yellow-400 font-black text-xs tracking-wider uppercase">
                                🌐 RECRUITMENT ONLINE
                            </span>
                            <span className="text-xs text-amber-800 font-medium">({onlineStats.batch_count} Batch)</span>
                        </div>
                        <button
                            onClick={() => { setSelectedMetode('Online'); setCurrentPage(1); }}
                            className="text-xs font-bold text-amber-900 hover:underline cursor-pointer"
                        >
                            Filter Online →
                        </button>
                    </div>
                    <div className="grid grid-cols-3 gap-3 mt-4 pt-3 border-t border-yellow-200/70 text-center">
                        <div className="bg-white/80 rounded-xl p-2.5 border border-yellow-200">
                            <p className="text-[11px] font-semibold text-amber-800">Peserta Hadir</p>
                            <p className="text-lg font-bold text-slate-900">{onlineStats.total_peserta.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white/80 rounded-xl p-2.5 border border-yellow-200">
                            <p className="text-[11px] font-semibold text-amber-800">Join FR</p>
                            <p className="text-lg font-bold text-emerald-700">{onlineStats.total_join.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white/80 rounded-xl p-2.5 border border-yellow-200">
                            <p className="text-[11px] font-semibold text-amber-800">% Lulus</p>
                            <p className="text-lg font-bold text-[#4B6A34]">{onlineStats.persen_lulus}%</p>
                        </div>
                    </div>
                </div>

                {/* OFFLINE METRICS CARD */}
                <div className="bg-gradient-to-r from-slate-50 to-blue-50/50 rounded-2xl border border-slate-200 p-5 shadow-xs relative">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <span className="px-2.5 py-1 rounded-lg bg-slate-800 text-white font-bold text-xs tracking-wider uppercase">
                                🏢 RECRUITMENT OFFLINE (ONSITE)
                            </span>
                            <span className="text-xs text-slate-600 font-medium">({offlineStats.batch_count} Batch)</span>
                        </div>
                        <button
                            onClick={() => { setSelectedMetode('Offline'); setCurrentPage(1); }}
                            className="text-xs font-bold text-slate-700 hover:underline cursor-pointer"
                        >
                            Filter Offline →
                        </button>
                    </div>
                    <div className="grid grid-cols-3 gap-3 mt-4 pt-3 border-t border-slate-200 text-center">
                        <div className="bg-white/80 rounded-xl p-2.5 border border-slate-200">
                            <p className="text-[11px] font-semibold text-slate-600">Peserta Hadir</p>
                            <p className="text-lg font-bold text-slate-900">{offlineStats.total_peserta.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white/80 rounded-xl p-2.5 border border-slate-200">
                            <p className="text-[11px] font-semibold text-slate-600">Join FR</p>
                            <p className="text-lg font-bold text-emerald-700">{offlineStats.total_join.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white/80 rounded-xl p-2.5 border border-slate-200">
                            <p className="text-[11px] font-semibold text-slate-600">% Lulus</p>
                            <p className="text-lg font-bold text-[#4B6A34]">{offlineStats.persen_lulus}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Visualizations & Summary Tables Section */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* 1. Selection Funnel Chart */}
                <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs lg:col-span-2">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <h2 className="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                Funnel Seleksi & Tingkat Penyusutan Peserta
                            </h2>
                            <p className="text-xs text-slate-500">
                                Perjalanan kandidat dari tahap kehadiran awal hingga join FR
                            </p>
                        </div>
                        <div className="p-2 bg-slate-100 rounded-lg text-slate-600">
                            <Activity className="w-4 h-4" />
                        </div>
                    </div>

                    <div className="h-72 w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart
                                data={funnel}
                                layout="vertical"
                                margin={{ top: 5, right: 30, left: 60, bottom: 5 }}
                            >
                                <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#E2E8F0" />
                                <XAxis type="number" stroke="#94A3B8" fontSize={11} />
                                <YAxis dataKey="stage" type="category" stroke="#475569" fontSize={11} width={130} />
                                <Tooltip
                                    formatter={(value, name, props) => {
                                        const { payload } = props;
                                        return [
                                            `${value.toLocaleString('id-ID')} orang (${payload.pct_of_total}% dari Hadir, ${payload.pct_of_prev}% dari tahap sebelumnya)`,
                                            'Jumlah'
                                        ];
                                    }}
                                    contentStyle={{ backgroundColor: '#1E293B', borderRadius: '8px', color: '#fff', fontSize: '12px' }}
                                />
                                <Bar dataKey="count" radius={[0, 6, 6, 0]}>
                                    {funnel.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={funnelColors[index % funnelColors.length]} />
                                    ))}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                    </div>

                    {/* Step-by-step conversion pills */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 border-t border-slate-100 mt-2">
                        {funnel.slice(1).map((f, i) => (
                            <div key={i} className="bg-slate-50 rounded-xl p-2.5 border border-slate-100 text-center">
                                <p className="text-[10px] text-slate-500 font-semibold truncate">{f.stage}</p>
                                <p className="text-sm font-bold text-slate-800">{f.count.toLocaleString('id-ID')}</p>
                                <p className="text-[10px] text-[#4B6A34] font-bold">{f.pct_of_total}% hadir</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* 2. REKAP PER METODE REKRUTMEN (Revisi 2) */}
                <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div className="flex items-center justify-between mb-3">
                            <div>
                                <h2 className="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                    Rekap per Metode
                                </h2>
                                <p className="text-xs text-slate-500">Ringkasan Online vs Offline</p>
                            </div>
                            <Globe className="w-4 h-4 text-[#4B6A34]" />
                        </div>

                        <div className="overflow-x-auto border border-slate-100 rounded-xl">
                            <table className="w-full text-xs text-left">
                                <thead className="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th className="py-2.5 px-3">Metode</th>
                                        <th className="py-2.5 px-2 text-center">Batch</th>
                                        <th className="py-2.5 px-2 text-right">Peserta</th>
                                        <th className="py-2.5 px-2 text-right">Join</th>
                                        <th className="py-2.5 px-3 text-right">% Lulus</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {chartData.by_metode?.map((row, idx) => (
                                        <tr key={idx} className="hover:bg-slate-50/80 transition-colors">
                                            <td className="py-2.5 px-3 font-semibold text-slate-800">
                                                {row.metode === 'Online' ? (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-yellow-200 text-yellow-950 border border-yellow-300 font-bold text-[10px]">
                                                        🌐 Online
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold text-[10px]">
                                                        🏢 Offline
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-2.5 px-2 text-center text-slate-600 font-bold">
                                                {row.batch_count}
                                            </td>
                                            <td className="py-2.5 px-2 text-right text-slate-700">
                                                {row.total_peserta.toLocaleString('id-ID')}
                                            </td>
                                            <td className="py-2.5 px-2 text-right font-bold text-emerald-700">
                                                {row.total_join.toLocaleString('id-ID')}
                                            </td>
                                            <td className="py-2.5 px-3 text-right">
                                                <span className="inline-block px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[11px]">
                                                    {row.persen_lulus}%
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                                {summary && (
                                    <tfoot className="bg-slate-900 text-white font-bold border-t-2 border-slate-700">
                                        <tr>
                                            <td className="py-2.5 px-3 text-xs uppercase text-slate-200">TOTAL</td>
                                            <td className="py-2.5 px-2 text-center text-xs text-slate-200">{summary.batch_count}</td>
                                            <td className="py-2.5 px-2 text-right text-xs text-blue-300">{summary.total_peserta_hadir.toLocaleString('id-ID')}</td>
                                            <td className="py-2.5 px-2 text-right text-xs text-emerald-400">{summary.total_join_fr.toLocaleString('id-ID')}</td>
                                            <td className="py-2.5 px-3 text-right text-xs text-emerald-300 font-extrabold">{summary.overall_pass_rate_pct}%</td>
                                        </tr>
                                    </tfoot>
                                )}
                            </table>
                        </div>
                    </div>

                    <div className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400 text-center">
                        * % LULUS dihitung akurat dari (Total Join / Total Peserta)
                    </div>
                </div>
            </div>

            {/* Charts Row 2: Perbandingan Lokasi & Tren Tahunan */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Bar Chart: Peserta vs Join FR per Lokasi */}
                <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <h2 className="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                Peserta Hadir vs Join FR per Lokasi
                            </h2>
                            <p className="text-xs text-slate-500">Perbandingan volume kandidat dan karyawan yang bergabung</p>
                        </div>
                    </div>
                    <div className="h-72 w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart
                                data={chartData.by_location.slice(0, 8)}
                                margin={{ top: 10, right: 10, left: -10, bottom: 30 }}
                            >
                                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#E2E8F0" />
                                <XAxis dataKey="lokasi" stroke="#64748B" fontSize={10} angle={-25} textAnchor="end" />
                                <YAxis stroke="#94A3B8" fontSize={11} />
                                <Tooltip contentStyle={{ backgroundColor: '#1E293B', borderRadius: '8px', color: '#fff', fontSize: '12px' }} />
                                <Legend wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
                                <Bar dataKey="total_peserta" name="Peserta Hadir" fill="#3B82F6" radius={[4, 4, 0, 0]} />
                                <Bar dataKey="total_join" name="Join FR" fill="#4B6A34" radius={[4, 4, 0, 0]} />
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </div>

                {/* Line / Area Chart: Tren Tahunan */}
                <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <h2 className="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                Tren Rekrutmen & % Kelulusan per Tahun
                            </h2>
                            <p className="text-xs text-slate-500">Pertumbuhan kandidat dan efektivitas konversi per tahun</p>
                        </div>
                    </div>
                    <div className="h-72 w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <AreaChart
                                data={chartData.by_year}
                                margin={{ top: 10, right: 10, left: -10, bottom: 10 }}
                            >
                                <defs>
                                    <linearGradient id="colorPeserta" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#3B82F6" stopOpacity={0.4}/>
                                        <stop offset="95%" stopColor="#3B82F6" stopOpacity={0}/>
                                    </linearGradient>
                                    <linearGradient id="colorJoin" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#4B6A34" stopOpacity={0.6}/>
                                        <stop offset="95%" stopColor="#4B6A34" stopOpacity={0}/>
                                    </linearGradient>
                                </defs>
                                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#E2E8F0" />
                                <XAxis dataKey="tahun" stroke="#64748B" fontSize={11} />
                                <YAxis stroke="#94A3B8" fontSize={11} />
                                <Tooltip contentStyle={{ backgroundColor: '#1E293B', borderRadius: '8px', color: '#fff', fontSize: '12px' }} />
                                <Legend wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
                                <Area type="monotone" dataKey="total_peserta" name="Peserta Hadir" stroke="#3B82F6" fillOpacity={1} fill="url(#colorPeserta)" />
                                <Area type="monotone" dataKey="total_join" name="Join FR" stroke="#4B6A34" fillOpacity={1} fill="url(#colorJoin)" />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </div>

            {/* Main CRUD Data Table with METODE REKRUTMEN column and Sticky Total Row */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                {/* Table Header Bar */}
                <div className="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h2 className="text-base font-bold text-slate-900">Tabel Data Rekrutmen</h2>
                        <p className="text-xs text-slate-500">
                            Menampilkan {pagination.total} baris data sesuai kriteria filter
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <span className="text-xs text-slate-500 font-medium">Tampilkan per halaman:</span>
                        <select
                            value={perPage}
                            onChange={(e) => { setPerPage(Number(e.target.value)); setCurrentPage(1); }}
                            className="bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-[#4B6A34]"
                        >
                            <option value={10}>10</option>
                            <option value={25}>25</option>
                            <option value={50}>50</option>
                            <option value={100}>100</option>
                            <option value={-1}>Semua</option>
                        </select>
                    </div>
                </div>

                {/* Table Container with Horizontal Scroll */}
                <div className="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table className="w-full text-xs text-left border-collapse">
                        <thead className="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider sticky top-0 z-20 border-b border-slate-200 select-none">
                            <tr>
                                <th onClick={() => handleSort('no')} className="py-3 px-3 cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center gap-1">
                                        <span>NO</span>
                                        {sortBy === 'no' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('lokasi_rekrut')} className="py-3 px-3 cursor-pointer hover:bg-slate-200 whitespace-nowrap min-w-[130px]">
                                    <div className="flex items-center gap-1">
                                        <span>LOKASI REKRUT</span>
                                        {sortBy === 'lokasi_rekrut' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('metode_rekrutmen')} className="py-3 px-3 cursor-pointer hover:bg-slate-200 whitespace-nowrap min-w-[110px]">
                                    <div className="flex items-center gap-1">
                                        <span>METODE</span>
                                        {sortBy === 'metode_rekrutmen' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('program')} className="py-3 px-3 cursor-pointer hover:bg-slate-200 whitespace-nowrap min-w-[140px]">
                                    <div className="flex items-center gap-1">
                                        <span>PROGRAM</span>
                                        {sortBy === 'program' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('tanggal')} className="py-3 px-3 cursor-pointer hover:bg-slate-200 whitespace-nowrap min-w-[95px]">
                                    <div className="flex items-center gap-1">
                                        <span>TANGGAL</span>
                                        {sortBy === 'tanggal' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('tahun')} className="py-3 px-2 text-center cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-center gap-1">
                                        <span>TAHUN</span>
                                        {sortBy === 'tahun' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_peserta_hadir')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>HADIR</span>
                                        {sortBy === 'jumlah_peserta_hadir' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_lolos_psikotest')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>PSIKOTEST</span>
                                        {sortBy === 'jumlah_lolos_psikotest' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_hadir_interview_awal')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>HADIR INT 1</span>
                                        {sortBy === 'jumlah_hadir_interview_awal' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_lolos_interview_awal')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>LOLOS INT 1</span>
                                        {sortBy === 'jumlah_lolos_interview_awal' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_lolos_interview_user')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>INT USER</span>
                                        {sortBy === 'jumlah_lolos_interview_user' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_ikut_mcu')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>IKUT MCU</span>
                                        {sortBy === 'jumlah_ikut_mcu' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('jumlah_lolos_mcu')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>LOLOS MCU</span>
                                        {sortBy === 'jumlah_lolos_mcu' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('join_fr')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap bg-emerald-50 text-emerald-900">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>JOIN FR</span>
                                        {sortBy === 'join_fr' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th onClick={() => handleSort('persen_lulus')} className="py-3 px-3 text-right cursor-pointer hover:bg-slate-200 whitespace-nowrap bg-emerald-50 text-emerald-900">
                                    <div className="flex items-center justify-end gap-1">
                                        <span>% LULUS</span>
                                        {sortBy === 'persen_lulus' && (sortOrder === 'asc' ? <ChevronUp className="w-3 h-3" /> : <ChevronDown className="w-3 h-3" />)}
                                    </div>
                                </th>
                                <th className="py-3 px-3 text-center whitespace-nowrap min-w-[90px]">AKSI</th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-slate-100 font-medium text-slate-700">
                            {loading ? (
                                <tr>
                                    <td colSpan="16" className="text-center py-12 text-slate-400">
                                        <RefreshCw className="w-6 h-6 animate-spin mx-auto text-[#4B6A34] mb-2" />
                                        <span>Memuat data rekrutmen...</span>
                                    </td>
                                </tr>
                            ) : data.length === 0 ? (
                                <tr>
                                    <td colSpan="16" className="text-center py-12 text-slate-400">
                                        <AlertCircle className="w-8 h-8 mx-auto text-slate-300 mb-2" />
                                        <span>Tidak ada data yang sesuai filter.</span>
                                    </td>
                                </tr>
                            ) : (
                                data.map((row) => (
                                    <tr key={row.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="py-2.5 px-3 font-bold text-slate-900">{row.no}</td>
                                        <td className="py-2.5 px-3 font-semibold text-slate-800">{row.lokasi_rekrut}</td>
                                        <td className="py-2.5 px-3 whitespace-nowrap">
                                            {row.metode_rekrutmen === 'Online' ? (
                                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-yellow-300 text-black border border-yellow-400 font-extrabold text-[10px] tracking-wide shadow-2xs">
                                                    🌐 Online
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px] border border-slate-200">
                                                    🏢 Offline
                                                </span>
                                            )}
                                        </td>
                                        <td className="py-2.5 px-3 text-slate-700 max-w-[180px] truncate" title={row.program}>
                                            {row.program}
                                        </td>
                                        <td className="py-2.5 px-3 text-slate-600 whitespace-nowrap">{row.tanggal}</td>
                                        <td className="py-2.5 px-2 text-center text-slate-600">{row.tahun}</td>
                                        <td className="py-2.5 px-3 text-right font-medium">{row.jumlah_peserta_hadir.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_lolos_psikotest.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_hadir_interview_awal.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_lolos_interview_awal.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_lolos_interview_user.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_ikut_mcu.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right">{row.jumlah_lolos_mcu.toLocaleString('id-ID')}</td>
                                        <td className="py-2.5 px-3 text-right font-bold text-emerald-800 bg-emerald-50/40">
                                            {row.join_fr.toLocaleString('id-ID')}
                                        </td>
                                        <td className="py-2.5 px-3 text-right bg-emerald-50/40">
                                            <span className="inline-block px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                                                {formatPercent(row.persen_lulus)}
                                            </span>
                                        </td>
                                        <td className="py-2.5 px-3 text-center">
                                            <div className="flex items-center justify-center gap-1">
                                                <button
                                                    onClick={() => openDetail(row)}
                                                    className="p-1 rounded-md text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                                    title="Lihat Detail"
                                                >
                                                    <Eye className="w-3.5 h-3.5" />
                                                </button>
                                                <button
                                                    onClick={() => openEdit(row)}
                                                    className="p-1 rounded-md text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer"
                                                    title="Edit Data"
                                                >
                                                    <Edit3 className="w-3.5 h-3.5" />
                                                </button>
                                                <button
                                                    onClick={() => openDelete(row)}
                                                    className="p-1 rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                                                    title="Hapus Data"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>

                        {/* STICKY TOTAL ROW */}
                        {summary && (
                            <tfoot className="bg-slate-900 text-white font-bold sticky bottom-0 z-20 border-t-2 border-slate-700 shadow-lg">
                                <tr>
                                    <td colSpan="6" className="py-3 px-4 text-xs font-bold uppercase tracking-wider text-slate-200">
                                        TOTAL ({summary.batch_count} Data Terfilter)
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-extrabold text-blue-300">
                                        {summary.total_peserta_hadir.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_lolos_psikotest.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_hadir_interview_awal.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_lolos_interview_awal.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_lolos_interview_user.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_ikut_mcu.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-bold text-slate-200">
                                        {summary.total_lolos_mcu.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-extrabold text-emerald-400 bg-slate-800/80">
                                        {summary.total_join_fr.toLocaleString('id-ID')}
                                    </td>
                                    <td className="py-3 px-3 text-right text-xs font-extrabold text-emerald-300 bg-slate-800/80">
                                        <span className="inline-block px-2 py-0.5 rounded bg-emerald-700 text-white font-bold">
                                            {summary.overall_pass_rate_pct}%
                                        </span>
                                    </td>
                                    <td className="py-3 px-3 text-center text-slate-400 text-[10px]">
                                        (Akurat)
                                    </td>
                                </tr>
                            </tfoot>
                        )}
                    </table>
                </div>

                {/* Pagination Controls */}
                {pagination.last_page > 1 && (
                    <div className="p-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
                        <p className="text-xs text-slate-500">
                            Menampilkan halaman <span className="font-bold text-slate-700">{pagination.current_page}</span> dari <span className="font-bold text-slate-700">{pagination.last_page}</span>
                        </p>
                        <div className="flex items-center gap-1.5">
                            <button
                                onClick={() => setCurrentPage(p => Math.max(p - 1, 1))}
                                disabled={pagination.current_page === 1}
                                className="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                            >
                                <ChevronLeft className="w-4 h-4" />
                            </button>
                            {Array.from({ length: Math.min(pagination.last_page, 5) }, (_, i) => {
                                let pageNum = i + 1;
                                if (pagination.last_page > 5 && pagination.current_page > 3) {
                                    pageNum = pagination.current_page - 2 + i;
                                    if (pageNum > pagination.last_page) pageNum = pagination.last_page - (4 - i);
                                }
                                return (
                                    <button
                                        key={pageNum}
                                        onClick={() => setCurrentPage(pageNum)}
                                        className={`w-8 h-8 rounded-lg text-xs font-bold transition-colors cursor-pointer ${
                                            pagination.current_page === pageNum
                                                ? 'bg-[#4B6A34] text-white shadow-xs'
                                                : 'border border-slate-200 text-slate-700 hover:bg-slate-50'
                                        }`}
                                    >
                                        {pageNum}
                                    </button>
                                );
                            })}
                            <button
                                onClick={() => setCurrentPage(p => Math.min(p + 1, pagination.last_page))}
                                disabled={pagination.current_page === pagination.last_page}
                                className="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                            >
                                <ChevronRight className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {/* CREATE / EDIT MODAL */}
            {(isCreateOpen || isEditOpen) && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200">
                        {/* Modal Header */}
                        <div className="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                            <div>
                                <h3 className="text-base font-bold text-slate-900">
                                    {isEditOpen ? 'Edit Data Batch Rekrutmen' : 'Tambah Batch Rekrutmen Baru'}
                                </h3>
                                <p className="text-xs text-slate-500">
                                    Isi data tahapan seleksi dengan valid. % Lulus dihitung otomatis.
                                </p>
                            </div>
                            <button
                                onClick={() => { setIsCreateOpen(false); setIsEditOpen(false); }}
                                className="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Modal Form */}
                        <form onSubmit={handleFormSubmit} className="p-6 space-y-4">
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Lokasi Rekrut <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="Pekanbaru, Medan, dsb"
                                        value={formData.lokasi_rekrut}
                                        onChange={(e) => setFormData({ ...formData, lokasi_rekrut: e.target.value })}
                                        className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                                    />
                                    {formErrors.lokasi_rekrut && <p className="text-[11px] text-red-500 mt-0.5">{formErrors.lokasi_rekrut}</p>}
                                </div>

                                {/* METODE REKRUTMEN (Dropdown/Radio) */}
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Metode Rekrutmen <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        required
                                        value={formData.metode_rekrutmen}
                                        onChange={(e) => setFormData({ ...formData, metode_rekrutmen: e.target.value })}
                                        className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                                    >
                                        <option value="Offline">🏢 Offline (Onsite)</option>
                                        <option value="Online">🌐 Online</option>
                                    </select>
                                    {formErrors.metode_rekrutmen && <p className="text-[11px] text-red-500 mt-0.5">{formErrors.metode_rekrutmen}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Program / Batch <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="Contoh: FAT I & MAT I 2024"
                                        value={formData.program}
                                        onChange={(e) => setFormData({ ...formData, program: e.target.value })}
                                        className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                                    />
                                    {formErrors.program && <p className="text-[11px] text-red-500 mt-0.5">{formErrors.program}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Tanggal <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="YYYY-MM-DD atau teks tanggal"
                                        value={formData.tanggal}
                                        onChange={(e) => setFormData({ ...formData, tanggal: e.target.value })}
                                        className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                                    />
                                    {formErrors.tanggal && <p className="text-[11px] text-red-500 mt-0.5">{formErrors.tanggal}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Tahun <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        required
                                        min="2000"
                                        max="2099"
                                        value={formData.tahun}
                                        onChange={(e) => setFormData({ ...formData, tahun: parseInt(e.target.value, 10) || '' })}
                                        className="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#4B6A34]/30 focus:border-[#4B6A34] outline-none"
                                    />
                                    {formErrors.tahun && <p className="text-[11px] text-red-500 mt-0.5">{formErrors.tahun}</p>}
                                </div>
                            </div>

                            {/* Stage Step Numbers */}
                            <div className="pt-3 border-t border-slate-100">
                                <h4 className="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
                                    Tahapan Seleksi (Jumlah Peserta)
                                </h4>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            1. Peserta Hadir
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_peserta_hadir}
                                            onChange={(e) => setFormData({ ...formData, jumlah_peserta_hadir: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_peserta_hadir && <p className="text-[10px] text-red-500">{formErrors.jumlah_peserta_hadir}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            2. Lolos Psikotest
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_lolos_psikotest}
                                            onChange={(e) => setFormData({ ...formData, jumlah_lolos_psikotest: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_lolos_psikotest && <p className="text-[10px] text-red-500">{formErrors.jumlah_lolos_psikotest}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            3. Hadir Int Awal
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_hadir_interview_awal}
                                            onChange={(e) => setFormData({ ...formData, jumlah_hadir_interview_awal: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_hadir_interview_awal && <p className="text-[10px] text-red-500">{formErrors.jumlah_hadir_interview_awal}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            4. Lolos Int Awal
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_lolos_interview_awal}
                                            onChange={(e) => setFormData({ ...formData, jumlah_lolos_interview_awal: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_lolos_interview_awal && <p className="text-[10px] text-red-500">{formErrors.jumlah_lolos_interview_awal}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            5. Lolos Int User
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_lolos_interview_user}
                                            onChange={(e) => setFormData({ ...formData, jumlah_lolos_interview_user: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_lolos_interview_user && <p className="text-[10px] text-red-500">{formErrors.jumlah_lolos_interview_user}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            6. Ikut MCU
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_ikut_mcu}
                                            onChange={(e) => setFormData({ ...formData, jumlah_ikut_mcu: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_ikut_mcu && <p className="text-[10px] text-red-500">{formErrors.jumlah_ikut_mcu}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-600 mb-1">
                                            7. Lolos MCU
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.jumlah_lolos_mcu}
                                            onChange={(e) => setFormData({ ...formData, jumlah_lolos_mcu: e.target.value })}
                                            className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-xs font-semibold text-slate-800 outline-none focus:border-[#4B6A34]"
                                        />
                                        {formErrors.jumlah_lolos_mcu && <p className="text-[10px] text-red-500">{formErrors.jumlah_lolos_mcu}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-[11px] font-semibold text-emerald-800 mb-1">
                                            8. JOIN FR
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            value={formData.join_fr}
                                            onChange={(e) => setFormData({ ...formData, join_fr: e.target.value })}
                                            className="w-full bg-emerald-50 border border-emerald-300 rounded-xl p-2 text-xs font-bold text-emerald-900 outline-none focus:border-emerald-600"
                                        />
                                        {formErrors.join_fr && <p className="text-[10px] text-red-500">{formErrors.join_fr}</p>}
                                    </div>
                                </div>
                            </div>

                            {/* Live Computed % LULUS Preview */}
                            <div className="bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p className="text-xs font-semibold text-slate-600">% LULUS (Otomatis)</p>
                                    <p className="text-[11px] text-slate-400">Dihitung dari JOIN FR / JUMLAH PESERTA HADIR</p>
                                </div>
                                <span className="text-lg font-extrabold text-[#4B6A34] bg-white px-3 py-1 rounded-lg border border-[#4B6A34]/20 shadow-2xs">
                                    {computedFormLulus}
                                </span>
                            </div>

                            {/* Modal Actions */}
                            <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => { setIsCreateOpen(false); setIsEditOpen(false); }}
                                    className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2 rounded-xl bg-[#4B6A34] text-white text-xs font-bold hover:bg-[#3d562a] shadow-xs cursor-pointer"
                                >
                                    {isEditOpen ? 'Simpan Perubahan' : 'Tambah Batch'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* DETAIL MODAL */}
            {isDetailOpen && selectedItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <div className="flex items-center gap-2">
                                    <span className="text-[11px] font-bold uppercase tracking-wider text-[#4B6A34]">Detail Batch #{selectedItem.no}</span>
                                    {selectedItem.metode_rekrutmen === 'Online' ? (
                                        <span className="px-2 py-0.5 rounded bg-yellow-300 text-black border border-yellow-400 font-extrabold text-[10px]">
                                            🌐 Online
                                        </span>
                                    ) : (
                                        <span className="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px]">
                                            🏢 Offline
                                        </span>
                                    )}
                                </div>
                                <h3 className="text-lg font-bold text-slate-900 mt-1">{selectedItem.program}</h3>
                                <p className="text-xs text-slate-500">{selectedItem.lokasi_rekrut} • {selectedItem.tanggal} ({selectedItem.tahun})</p>
                            </div>
                            <button
                                onClick={() => setIsDetailOpen(false)}
                                className="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Conversion Timeline */}
                        <div className="space-y-2.5">
                            <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wider">Tahapan Seleksi</h4>
                            {[
                                { name: 'Peserta Hadir', count: selectedItem.jumlah_peserta_hadir, color: 'bg-blue-500' },
                                { name: 'Lolos Psikotest', count: selectedItem.jumlah_lolos_psikotest, color: 'bg-indigo-500' },
                                { name: 'Hadir Interview Awal', count: selectedItem.jumlah_hadir_interview_awal, color: 'bg-cyan-500' },
                                { name: 'Lolos Interview Awal', count: selectedItem.jumlah_lolos_interview_awal, color: 'bg-teal-500' },
                                { name: 'Lolos Interview User', count: selectedItem.jumlah_lolos_interview_user, color: 'bg-amber-500' },
                                { name: 'Ikut MCU', count: selectedItem.jumlah_ikut_mcu, color: 'bg-orange-500' },
                                { name: 'Lolos MCU', count: selectedItem.jumlah_lolos_mcu, color: 'bg-emerald-600' },
                                { name: 'JOIN FR (Karyawan Baru)', count: selectedItem.join_fr, color: 'bg-[#4B6A34]' },
                            ].map((step, idx) => {
                                const hadir = selectedItem.jumlah_peserta_hadir || 1;
                                const pct = ((step.count / hadir) * 100).toFixed(1);
                                return (
                                    <div key={idx} className="space-y-1">
                                        <div className="flex justify-between text-xs font-medium">
                                            <span className="text-slate-700">{step.name}</span>
                                            <span className="font-bold text-slate-900">{step.count} orang ({pct}%)</span>
                                        </div>
                                        <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                            <div className={`h-full ${step.color} rounded-full`} style={{ width: `${Math.min(pct, 100)}%` }} />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Overall Result */}
                        <div className="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                            <div>
                                <p className="text-xs font-bold text-emerald-900">% Kelulusan Final</p>
                                <p className="text-[11px] text-emerald-700">Join FR / Peserta Hadir</p>
                            </div>
                            <span className="text-xl font-extrabold text-emerald-800">
                                {formatPercent(selectedItem.persen_lulus)}
                            </span>
                        </div>

                        <div className="pt-2 flex justify-end">
                            <button
                                onClick={() => setIsDetailOpen(false)}
                                className="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-200 cursor-pointer"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* DELETE CONFIRMATION MODAL */}
            {isDeleteOpen && selectedItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div className="flex items-center gap-3 text-red-600">
                            <div className="p-3 bg-red-50 rounded-xl">
                                <Trash2 className="w-6 h-6" />
                            </div>
                            <div>
                                <h3 className="text-base font-bold text-slate-900">Konfirmasi Hapus Data</h3>
                                <p className="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>

                        <p className="text-xs text-slate-600 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data batch <strong className="text-slate-900">{selectedItem.program}</strong> ({selectedItem.lokasi_rekrut} - {selectedItem.metode_rekrutmen})?
                        </p>

                        <div className="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button
                                onClick={() => setIsDeleteOpen(false)}
                                className="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleDeleteConfirm}
                                className="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-xs cursor-pointer"
                            >
                                Ya, Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* CSV IMPORT MODAL */}
            {isImportOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div className="flex items-center gap-2.5">
                                <div className="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                    <FileSpreadsheet className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-slate-900">Import Data dari File CSV</h3>
                                    <p className="text-xs text-slate-500">Upload file Data_Bersih.csv dengan kolom METODE REKRUTMEN (Online/Offline)</p>
                                </div>
                            </div>
                            <button
                                onClick={() => setIsImportOpen(false)}
                                className="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleImportSubmit} className="space-y-4">
                            {/* File Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    Pilih File CSV (.csv)
                                </label>
                                <input
                                    type="file"
                                    accept=".csv,.txt"
                                    required
                                    onChange={handleFileChange}
                                    className="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34]/10 file:text-[#4B6A34] hover:file:bg-[#4B6A34]/20 cursor-pointer border border-slate-200 rounded-xl p-2 bg-slate-50"
                                />
                                <p className="text-[11px] text-slate-400 mt-1">
                                    Pastikan kolom METODE REKRUTMEN hanya berisi nilai "Online" atau "Offline".
                                </p>
                            </div>

                            {/* Import Mode Options */}
                            <div className="space-y-2 pt-2">
                                <label className="block text-xs font-bold text-slate-700">
                                    Opsi Penyimpanan Data:
                                </label>
                                <div className="grid grid-cols-2 gap-3">
                                    <label className={`p-3 rounded-xl border text-xs cursor-pointer transition-all ${
                                        importMode === 'append'
                                            ? 'border-[#4B6A34] bg-[#4B6A34]/5 text-[#4B6A34] font-bold'
                                            : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-medium'
                                    }`}>
                                        <input
                                            type="radio"
                                            name="import_mode"
                                            value="append"
                                            checked={importMode === 'append'}
                                            onChange={() => setImportMode('append')}
                                            className="hidden"
                                        />
                                        <p className="font-bold">Tambahkan (Append)</p>
                                        <p className="text-[11px] opacity-75 font-normal">Data baru ditambahkan ke data saat ini</p>
                                    </label>

                                    <label className={`p-3 rounded-xl border text-xs cursor-pointer transition-all ${
                                        importMode === 'replace'
                                            ? 'border-amber-600 bg-amber-50 text-amber-900 font-bold'
                                            : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-medium'
                                    }`}>
                                        <input
                                            type="radio"
                                            name="import_mode"
                                            value="replace"
                                            checked={importMode === 'replace'}
                                            onChange={() => setImportMode('replace')}
                                            className="hidden"
                                        />
                                        <p className="font-bold text-amber-800">Gantikan Semua (Replace)</p>
                                        <p className="text-[11px] opacity-75 font-normal">Hapus seluruh data lama dan isi dengan CSV baru</p>
                                    </label>
                                </div>
                            </div>

                            {/* Preview rows if selected */}
                            {importPreview.length > 0 && (
                                <div className="space-y-1.5 pt-2">
                                    <span className="text-[11px] font-bold text-slate-600">Preview Data (5 Baris Pertama):</span>
                                    <div className="max-h-32 overflow-auto border border-slate-200 rounded-xl text-[10px] bg-slate-50 p-2 font-mono">
                                        <table className="w-full text-left">
                                            <tbody>
                                                {importPreview.map((row, rIdx) => (
                                                    <tr key={rIdx} className={rIdx === 0 ? 'font-bold text-slate-900 border-b' : 'text-slate-600'}>
                                                        {row.slice(0, 7).map((cell, cIdx) => (
                                                            <td key={cIdx} className="py-1 px-1.5 truncate max-w-[100px]">{cell}</td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setIsImportOpen(false)}
                                    className="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={importLoading || !importFile}
                                    className="px-5 py-2 text-xs font-bold text-white bg-[#4B6A34] hover:bg-[#3d562a] rounded-xl shadow-xs disabled:opacity-50 flex items-center gap-2 cursor-pointer"
                                >
                                    {importLoading && <RefreshCw className="w-3.5 h-3.5 animate-spin" />}
                                    <span>{importLoading ? 'Memproses Import...' : 'Mulai Import'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* IMPORT RESULT SUMMARY MODAL */}
            {isImportResultOpen && importSummary && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div className="flex items-center gap-2.5">
                                <div className={`p-2 rounded-xl ${
                                    importSummary.status === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'
                                }`}>
                                    {importSummary.status === 'success' ? <CheckCircle2 className="w-5 h-5" /> : <AlertCircle className="w-5 h-5" />}
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-slate-900">Hasil Import CSV</h3>
                                    <p className="text-xs text-slate-500">{importSummary.message}</p>
                                </div>
                            </div>
                            <button
                                onClick={() => setIsImportResultOpen(false)}
                                className="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Result Counters */}
                        <div className="grid grid-cols-2 gap-3">
                            <div className="bg-emerald-50 border border-emerald-100 rounded-xl p-3 text-center">
                                <p className="text-xs font-semibold text-emerald-800">Berhasil Diimport</p>
                                <p className="text-2xl font-bold text-emerald-700 mt-1">{importSummary.success_count || 0}</p>
                                <p className="text-[10px] text-emerald-600">baris data</p>
                            </div>
                            <div className="bg-red-50 border border-red-100 rounded-xl p-3 text-center">
                                <p className="text-xs font-semibold text-red-800">Gagal / Dilewati</p>
                                <p className="text-2xl font-bold text-red-700 mt-1">{importSummary.failed_count || 0}</p>
                                <p className="text-[10px] text-red-600">baris bermasalah</p>
                            </div>
                        </div>

                        {/* List of errors if any */}
                        {importSummary.errors && importSummary.errors.length > 0 && (
                            <div className="space-y-1.5 pt-2">
                                <p className="text-xs font-bold text-slate-700">Rincian Baris Gagal:</p>
                                <div className="max-h-40 overflow-y-auto bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs space-y-1">
                                    {importSummary.errors.map((err, i) => (
                                        <div key={i} className="text-red-700 flex items-start gap-1.5">
                                            <span className="font-bold">Baris {err.row}:</span>
                                            <span>{err.reason}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div className="pt-3 flex justify-end">
                            <button
                                onClick={() => setIsImportResultOpen(false)}
                                className="px-5 py-2 bg-[#4B6A34] text-white text-xs font-bold rounded-xl hover:bg-[#3d562a] cursor-pointer"
                            >
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* TAMBAH TAHUN MODAL */}
            {isTambahTahunOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in">
                    <div className="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div className="flex items-center gap-2">
                                <div className="p-2 rounded-xl bg-emerald-50 text-emerald-700">
                                    <Calendar className="w-5 h-5" />
                                </div>
                                <h3 className="text-base font-bold text-slate-900">Tambah Tahun Baru</h3>
                            </div>
                            <button
                                onClick={() => setIsTambahTahunOpen(false)}
                                className="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleTambahTahunSubmit} className="space-y-4">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Masukkan Tahun
                                </label>
                                <input
                                    type="number"
                                    min="2000"
                                    max="2100"
                                    value={tambahTahunInput}
                                    onChange={(e) => setTambahTahunInput(e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-semibold focus:bg-white focus:border-[#4B6A34] focus:ring-2 focus:ring-[#4B6A34]/20 outline-none transition-all"
                                    placeholder="Contoh: 2027"
                                />
                                <p className="text-[11px] text-slate-500 mt-1">
                                    Tahun ini akan ditambahkan ke filter rekrutmen dan dapat langsung dipilih.
                                </p>
                            </div>

                            <div className="flex items-center gap-2 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setIsTambahTahunOpen(false)}
                                    className="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="flex-1 py-2.5 px-4 rounded-xl bg-[#4B6A34] hover:bg-[#3d562a] text-xs font-semibold text-white transition-colors shadow-xs cursor-pointer"
                                >
                                    Terapkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
