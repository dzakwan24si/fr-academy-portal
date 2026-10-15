import { useState, useEffect } from 'react';
import DatePickerModule from "react-multi-date-picker";
import XLSX from 'xlsx-js-style';
import {
  Plus, Search, Edit2, Trash2, X, FileSpreadsheet, ChevronLeft, ChevronRight,
  GraduationCap, Users, Award, Wrench, Layers, BookOpen, Calendar as CalendarIcon, ClipboardList, Download, AlertTriangle,
  Upload, Check, RefreshCw, Info
} from 'lucide-react';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import {
  getAllPelatihan, addPelatihanNonReguler, addPelatihanReguler,
  updatePelatihanNonReguler, deletePelatihanNonReguler,
  updatePelatihanReguler, deletePelatihanReguler, deleteAllPelatihan
} from '@services/trainingApi';

const DatePicker = DatePickerModule.default || DatePickerModule;

const MONTH_NAMES_ID = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
const DAY_NAMES_ID = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const cleanText = (str) => (str || '').replace(/[\u200B-\u200D\uFEFF\u2060]/g, '').trim().toLowerCase();

const groupDatesIntoRanges = (dates) => {
  if (!dates || dates.length === 0) return [];
  const sortedDates = dates.map(d => new Date(d)).sort((a, b) => a - b);
  const ranges = [];
  let currentRange = [sortedDates[0]];

  for (let i = 1; i < sortedDates.length; i++) {
    const prevDate = sortedDates[i - 1];
    const currDate = sortedDates[i];

    const diffTime = Math.abs(currDate - prevDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) {
      if (currentRange.length === 1) {
        currentRange.push(currDate);
      } else {
        currentRange[1] = currDate;
      }
    } else {
      ranges.push(currentRange.map(d => d.toISOString().split('T')[0]));
      currentRange = [currDate];
    }
  }
  ranges.push(currentRange.map(d => d.toISOString().split('T')[0]));
  return ranges;
};

const DataManagement = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [formType, setFormType] = useState('reguler'); // 'non_reguler' or 'reguler'
  const [activeMainTab, setActiveMainTab] = useState('reguler'); // 'reguler' or 'non_reguler'
  const [activeCategory, setActiveCategory] = useState('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [formData, setFormData] = useState({});
  const [isEditMode, setIsEditMode] = useState(false);
  const [editId, setEditId] = useState(null);

  // Filters
  const [filterRegion, setFilterRegion] = useState('Semua Region');
  const [filterSubKategori, setFilterSubKategori] = useState('Semua Sub Kategori');
  const [filterTahun, setFilterTahun] = useState('Semua Tahun');
  const [itemsPerPage, setItemsPerPage] = useState(25);
  const [isDeleteConfirmOpen, setIsDeleteConfirmOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState(null);

  // Calendar Detail Modal State
  const [selectedProgram, setSelectedProgram] = useState(null);
  const [isCalendarModalOpen, setIsCalendarModalOpen] = useState(false);
  const [calendarDate, setCalendarDate] = useState(new Date());

  // Fitur Detail Modal State
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);

  // Import Modal State
  const [isImportModalOpen, setIsImportModalOpen] = useState(false);
  const [importFile, setImportFile] = useState(null);
  const [importLoading, setImportLoading] = useState(false);
  const [importMessage, setImportMessage] = useState(null);

  const handleImportSubmit = async (e) => {
    e.preventDefault();
    if (!importFile) return;

    setImportLoading(true);
    setImportMessage(null);

    try {
      const form = new FormData();
      form.append('file', importFile);

      const response = await fetch('/administrasi/program-batch/import', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'Accept': 'application/json'
        },
        body: form
      });

      const result = await response.json().catch(() => null);

      if (response.ok) {
        setImportMessage({ type: 'success', text: result?.message || 'Data program batch berhasil diimpor.' });
        setImportFile(null);
        await fetchData();
        setTimeout(() => {
          setIsImportModalOpen(false);
          setImportMessage(null);
        }, 1500);
      } else {
        setImportMessage({ type: 'error', text: result?.message || 'Gagal mengimpor file. Pastikan format kolom sesuai.' });
      }
    } catch (err) {
      setImportMessage({ type: 'error', text: 'Terjadi kesalahan saat mengunggah file.' });
    } finally {
      setImportLoading(false);
    }
  };

  useEffect(() => {
    if (selectedProgram?.start && !isNaN(selectedProgram.start)) {
      setCalendarDate(new Date(selectedProgram.start.getFullYear(), selectedProgram.start.getMonth(), 1));
    } else {
      setCalendarDate(new Date());
    }
  }, [selectedProgram]);

  const fetchData = async () => {
    setLoading(true);
    const result = await getAllPelatihan();
    if (result.data) {
      setData(result.data);
    }
    setLoading(false);
  };

  useEffect(() => {
    const loadData = async () => {
      const result = await getAllPelatihan();
      if (result.data) {
        setData(result.data);
      }
      setLoading(false);
    };
    loadData();
  }, []);

  const handleDateChange = (dateObjects) => {
    if (!dateObjects || dateObjects.length === 0) {
      const updates = { tanggal_pelaksanaan: [] };
      if (formType === 'reguler') {
        updates.Mulai_program = null;
        updates.Selesai_program = null;
      } else {
        updates.start_date = null;
        updates.end_date = null;
      }
      setFormData(prev => ({ ...prev, ...updates }));
      return;
    }

    let allDates = [];
    dateObjects.forEach(item => {
      if (Array.isArray(item)) {
        if (item.length === 1) {
          allDates.push(item[0].format("YYYY-MM-DD"));
        } else if (item.length === 2) {
          const start = new Date(item[0].format("YYYY-MM-DD"));
          const end = new Date(item[1].format("YYYY-MM-DD"));
          let current = new Date(start);
          while (current <= end) {
            allDates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
          }
        }
      } else {
        allDates.push(item.format ? item.format("YYYY-MM-DD") : item);
      }
    });

    // Remove duplicates and sort
    const dates = [...new Set(allDates)].sort();

    const updates = { tanggal_pelaksanaan: dates };
    if (formType === 'reguler') {
      updates.Mulai_program = dates[0];
      updates.Selesai_program = dates[dates.length - 1];
    } else {
      updates.start_date = dates[0];
      updates.end_date = dates[dates.length - 1];
    }
    setFormData(prev => ({ ...prev, ...updates }));
  };

  const handleInputChange = (e) => {
    const { name, value, type } = e.target;
    let parsedValue = type === 'number' ? parseInt(value) || 0 : value;

    let updatedData = { ...formData, [name]: parsedValue };

    if (name === 'Nama_Program_reguler') {
      updatedData.Training_Program = parsedValue;
    } else if (name === 'Training_Program') {
      updatedData.Nama_Program_reguler = parsedValue;
    } else if (name === 'Total_orang') {
      updatedData.target_peserta = parsedValue;
    } else if (name === 'target_peserta') {
      updatedData.Total_orang = parsedValue;
    }

    // Auto-calculation logic based on PRD requirement
    if (formType === 'reguler') {
      if (['Total_orang', 'target_peserta', 'Promosi_mandor'].includes(name)) {
        const total = updatedData.Total_orang ?? updatedData.target_peserta ?? 0;
        const promosi = updatedData.Promosi_mandor || 0;
        const freshGraduate = Math.max(0, total - promosi);
        updatedData.Fresh_Graduate = freshGraduate;
      }
    } else {
      if (['peserta_non_staf', 'peserta_ast', 'peserta_askep', 'peserta_mgr', 'peserta_gm', 'peserta_head'].includes(name)) {
        const stafSum = (updatedData.peserta_ast||0) + (updatedData.peserta_askep||0) + (updatedData.peserta_mgr||0) + (updatedData.peserta_gm||0) + (updatedData.peserta_head||0);
        const nonStaf = updatedData.peserta_non_staf || 0;
        updatedData.target_staf = stafSum;
        updatedData.target_non_staf = nonStaf;
        updatedData.target_total = stafSum + nonStaf;
      }
    }

    setFormData(updatedData);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    let result;
    if (formType === 'reguler') {
      const payload = {
        ...formData,
        Training_Program: formData.Training_Program || formData.Nama_Program_reguler || '',
        Nama_Program_reguler: formData.Nama_Program_reguler || formData.Training_Program || '',
        target_peserta: formData.target_peserta ?? formData.Total_orang ?? 0,
        Total_orang: formData.Total_orang ?? formData.target_peserta ?? 0,
      };
      if (isEditMode) result = await updatePelatihanReguler(editId, payload);
      else result = await addPelatihanReguler(payload);
    } else {
      if (isEditMode) result = await updatePelatihanNonReguler(editId, formData);
      else result = await addPelatihanNonReguler(formData);
    }

    if (result && result.error) {
      alert("Error saving data: " + result.error.message);
      return;
    }

    setIsModalOpen(false);
    setFormData({});
    setIsEditMode(false);
    setEditId(null);
    fetchData(); // Refresh data
  };

  const handleEdit = (item) => {
    const isReguler = item.type === 'Reguler';
    setFormType(isReguler ? 'reguler' : 'non_reguler');

    const orig = item.originalData || {};
    const editData = { ...orig };
    if (!isReguler) {
      if (editData.start_date) editData.start_date = editData.start_date.split('T')[0];
      if (editData.end_date) editData.end_date = editData.end_date.split('T')[0];
    } else {
      const titleName = editData.Training_Program || editData.Nama_Program_reguler || item.title || '';
      editData.Training_Program = titleName;
      editData.Nama_Program_reguler = titleName;
      const totalP = editData.target_peserta !== undefined ? editData.target_peserta : (editData.Total_orang !== undefined ? editData.Total_orang : (item.participants || 0));
      editData.target_peserta = totalP;
      editData.Total_orang = totalP;
      if (editData.Mulai_program) editData.Mulai_program = editData.Mulai_program.split('T')[0];
      if (editData.Selesai_program) editData.Selesai_program = editData.Selesai_program.split('T')[0];
      if (editData.jenis_program_reguler) {
        editData.jenis_program_reguler = editData.jenis_program_reguler.replace(/[\u200B-\u200D\uFEFF\u2060]/g, '').trim();
      }
    }

    if (!editData.tanggal_pelaksanaan || editData.tanggal_pelaksanaan.length === 0) {
      const s = isReguler ? editData.Mulai_program : editData.start_date;
      const e = isReguler ? editData.Selesai_program : editData.end_date;
      editData.tanggal_pelaksanaan = [s, e].filter(Boolean);
    }

    setFormData(editData);
    setIsEditMode(true);
    setEditId(isReguler ? (orig.Program_reguler_id || item.id?.replace('reg-', '')) : (orig.id_nonreguler || item.id?.replace('non-reg-', '')));
    setIsModalOpen(true);
  };

  const handleDelete = async (item) => {
    setItemToDelete(item);
    setIsDeleteConfirmOpen(true);
  };

  const confirmDelete = async () => {
    if (!itemToDelete) return;

    const isReguler = itemToDelete.type === 'Reguler';
    if (isReguler) {
      await deletePelatihanReguler(itemToDelete.originalData.Program_reguler_id);
    } else {
      await deletePelatihanNonReguler(itemToDelete.originalData.id_nonreguler);
    }
    setIsDeleteConfirmOpen(false);
    setItemToDelete(null);
    fetchData();
  };

  // 3-Step Confirmation Delete All State & Handlers
  const [isDeleteAllModalOpen, setIsDeleteAllModalOpen] = useState(false);
  const [deleteAllStep, setDeleteAllStep] = useState(1);
  const [isDeletingAll, setIsDeletingAll] = useState(false);

  const deleteStepWarnings = [
    {
      title: "Peringatan 1 dari 3: Konfirmasi Hapus",
      desc: `Apakah Anda yakin ingin menghapus SELURUH data Program Training (${activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non Reguler'})? Seluruh batch dan data pelatihan yang tersimpan akan dihapus dari sistem.`,
      confirmBtn: "Lanjutkan (1/3)",
      btnColor: "bg-red-600 hover:bg-red-700"
    },
    {
      title: "Peringatan 2 dari 3: Data Tidak Dapat Dipulihkan",
      desc: "PERHATIAN! Tindakan ini TIDAK DAPAT DIBATALKAN. Seluruh daftar program pelatihan, batch, dan target peserta akan hilang permanen dari database. Pastikan Anda telah mengunduh backup (Export Excel / CSV).",
      confirmBtn: "Saya Mengerti Risiko Ini (2/3)",
      btnColor: "bg-red-700 hover:bg-red-800"
    },
    {
      title: "Peringatan 3 dari 3: Konfirmasi Terakhir!",
      desc: `Ini adalah konfirmasi terakhir. Klik tombol di bawah ini untuk mengosongkan tabel ${activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non Reguler'} secara permanen sekarang juga.`,
      confirmBtn: "HAPUS SEMUA DATA SEKARANG (3/3)",
      btnColor: "bg-red-800 hover:bg-red-900"
    }
  ];

  const openDeleteAllModal = () => {
    setDeleteAllStep(1);
    setIsDeleteAllModalOpen(true);
  };

  const closeDeleteAllModal = () => {
    setIsDeleteAllModalOpen(false);
    setDeleteAllStep(1);
  };

  const proceedDeleteAll = async () => {
    if (deleteAllStep < 3) {
      setDeleteAllStep(prev => prev + 1);
    } else {
      setIsDeletingAll(true);
      const res = await deleteAllPelatihan(activeMainTab); // 'reguler' or 'non_reguler'
      setIsDeletingAll(false);
      setIsDeleteAllModalOpen(false);
      setDeleteAllStep(1);
      if (res.error) {
        alert("Gagal menghapus data: " + res.error.message);
      } else {
        fetchData();
      }
    }
  };

  // Dynamic counts for category cards
  const regulerTotalCount = data.filter(item => item.type === 'Reguler').length;
  const regulerStafCount = data.filter(item =>
    item.type === 'Reguler' && cleanText(item.originalData?.jenis_program_reguler).includes('staf')
  ).length;
  const regulerMandorCount = data.filter(item =>
    item.type === 'Reguler' && cleanText(item.originalData?.jenis_program_reguler).includes('mandor')
  ).length;

  const nonRegulerTotalCount = data.filter(item => item.type === 'Non-Reguler').length;
  const nonRegulerManagerialCount = data.filter(item =>
    item.type === 'Non-Reguler' && cleanText(item.originalData?.kategori_pelatihan).includes('managerial')
  ).length;
  const nonRegulerTechnicalCount = data.filter(item =>
    item.type === 'Non-Reguler' && cleanText(item.originalData?.kategori_pelatihan).includes('technical')
  ).length;

  const regulerCategories = [
    { id: 'all', title: 'Semua Reguler', icon: Layers, count: regulerTotalCount },
    { id: 'staf', title: 'Training Reguler - Staf', icon: GraduationCap, count: regulerStafCount },
    { id: 'mandor', title: 'Training Reguler - Mandor', icon: Users, count: regulerMandorCount },
  ];

  const nonRegulerCategories = [
    { id: 'all', title: 'Semua Non Reguler', icon: Layers, count: nonRegulerTotalCount },
    { id: 'managerial', title: 'Managerial and Leadership', icon: Award, count: nonRegulerManagerialCount },
    { id: 'technical', title: 'Technical', icon: Wrench, count: nonRegulerTechnicalCount },
  ];

  const currentCategories = activeMainTab === 'reguler' ? regulerCategories : nonRegulerCategories;

  const filteredData = data.filter(item => {
    // 1. Filter by Main Tab (Reguler vs Non-Reguler)
    const isRegulerTab = activeMainTab === 'reguler';
    if (isRegulerTab && item.type !== 'Reguler') return false;
    if (!isRegulerTab && item.type !== 'Non-Reguler') return false;

    // 2. Filter by Category Card
    if (activeCategory !== 'all') {
      if (isRegulerTab) {
        const jenis = cleanText(item.originalData?.jenis_program_reguler);
        if (activeCategory === 'staf' && !jenis.includes('staf')) return false;
        if (activeCategory === 'mandor' && !jenis.includes('mandor')) return false;
      } else {
        const kat = cleanText(item.originalData?.kategori_pelatihan);
        if (activeCategory === 'managerial' && !kat.includes('managerial')) return false;
        if (activeCategory === 'technical' && !kat.includes('technical')) return false;
      }
    }

    // 3. Filter by Search Query
    const search = (searchTerm || '').trim().toLowerCase();
    if (search) {
      const matchesSearch =
        (item.title || '').toLowerCase().includes(search) ||
        (item.location || '').toLowerCase().includes(search) ||
        (item.trainer || '').toLowerCase().includes(search) ||
        (item.subCategory || '').toLowerCase().includes(search) ||
        (item.originalData?.kategori_pelatihan || '').toLowerCase().includes(search) ||
        (item.originalData?.jenis_program_reguler || '').toLowerCase().includes(search) ||
        String(item.originalData?.Batch || '').toLowerCase().includes(search);
      if (!matchesSearch) return false;
    }

    // 4. Filter by Region
    if (filterRegion !== 'Semua Region' && item.region !== filterRegion) return false;

    // 5. Filter by Sub Kategori (Non-Reguler)
    if (!isRegulerTab && filterSubKategori !== 'Semua Sub Kategori' && item.subCategory !== filterSubKategori) {
      return false;
    }

    // 6. Filter by Tahun
    if (filterTahun !== 'Semua Tahun') {
      let itemYear = item.tahun || item.originalData?.tahun;
      if (!itemYear) {
        if (isRegulerTab) {
          const mulai = item.originalData?.Mulai_program || item.start;
          if (mulai) itemYear = new Date(mulai).getFullYear();
        } else {
          const start = item.originalData?.start_date || item.start;
          if (start) itemYear = new Date(start).getFullYear();
          const end = item.originalData?.end_date || item.end;
          if (!itemYear && end) itemYear = new Date(end).getFullYear();
        }
      }
      if (Number(itemYear) !== Number(filterTahun)) return false;
    }

    return true;
  }).sort((a, b) => {
    if (activeMainTab === 'reguler') {
      const order = ['FAT', 'MAT', 'TAT', 'DMT', 'AAT', 'PMT', 'PKT'];
      const getPrefixIdx = (title) => {
        const prefix = (title || '').split(' ')[0].toUpperCase();
        const idx = order.indexOf(prefix);
        return idx !== -1 ? idx : 999;
      };
      const idxA = getPrefixIdx(a.title);
      const idxB = getPrefixIdx(b.title);
      if (idxA !== idxB) return idxA - idxB;
      return (a.title || '').localeCompare(b.title || '');
    } else {
      const orderNonReg = ['SOFTSKILL', 'ESTATE', 'MILL', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI'];
      const getSubIdx = (subCat) => {
        const sub = (subCat || '').toUpperCase();
        const idx = orderNonReg.findIndex(o => sub.includes(o));
        return idx !== -1 ? idx : 999;
      };
      const idxA = getSubIdx(a.subCategory);
      const idxB = getSubIdx(b.subCategory);
      if (idxA !== idxB) return idxA - idxB;
      return (b.id || '').localeCompare(a.id || '');
    }
  });

  const uniqueRegions = [...new Set(
    data
      .filter(item => (activeMainTab === 'reguler' ? item.type === 'Reguler' : item.type === 'Non-Reguler'))
      .map(item => item.region)
      .filter(Boolean)
  )];

  const uniqueSubKategories = [...new Set(
    data
      .filter(item => item.type === 'Non-Reguler')
      .map(item => item.subCategory)
      .filter(sub => sub && sub !== '-')
  )];

  const uniqueYears = [...new Set(
    data
      .map(item => {
        let yr = item.tahun || item.originalData?.tahun;
        if (!yr) {
          if (item.type === 'Reguler') {
            const mulai = item.originalData?.Mulai_program || item.start;
            if (mulai) yr = new Date(mulai).getFullYear();
          } else {
            const start = item.originalData?.start_date || item.start;
            if (start) yr = new Date(start).getFullYear();
            const end = item.originalData?.end_date || item.end;
            if (!yr && end) yr = new Date(end).getFullYear();
          }
        }
        return yr ? Number(yr) : null;
      })
      .filter(Boolean)
  )].sort((a, b) => b - a);

  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedData = filteredData.slice(startIndex, startIndex + itemsPerPage);

  // Metric Cards Summary Calculations
  const totalProgram = filteredData.length;
  const totalBatch = filteredData.reduce((acc, curr) => {
    const orig = curr.originalData || {};
    return acc + (Number(orig.TARGET_BATCH ?? curr.batchCount ?? orig.total_batch) || 1);
  }, 0);
  const totalPeserta = filteredData.reduce((acc, curr) => {
    const orig = curr.originalData || {};
    return acc + (Number(orig.TARGET_PESERTA ?? curr.participants ?? orig.target_total ?? orig.target_peserta) || 0);
  }, 0);

  // Calendar Modal Helpers
  const getCalendarDays = (date) => {
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDayOfMonth = new Date(year, month, 1);
    const startWeekday = firstDayOfMonth.getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const cells = [];
    for (let i = startWeekday - 1; i >= 0; i--) {
      cells.push({ day: daysInPrevMonth - i, inMonth: false, date: new Date(year, month - 1, daysInPrevMonth - i) });
    }
    for (let d = 1; d <= daysInMonth; d++) {
      cells.push({ day: d, inMonth: true, date: new Date(year, month, d) });
    }
    let nextDay = 1;
    while (cells.length < 42) {
      cells.push({ day: nextDay, inMonth: false, date: new Date(year, month + 1, nextDay) });
      nextDay++;
    }
    return cells;
  };

  const isDateInRange = (date) => {
    if (date.getDay() === 0) return false;

    const dates = selectedProgram?.tanggal_pelaksanaan;
    if (!dates || !Array.isArray(dates) || dates.length === 0) return false;

    const targetTime = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();

    return dates.some(d => {
      const pDate = new Date(d);
      return new Date(pDate.getFullYear(), pDate.getMonth(), pDate.getDate()).getTime() === targetTime;
    });
  };

  const handlePrevMonth = () => setCalendarDate(prev => new Date(prev.getFullYear(), prev.getMonth() - 1, 1));
  const handleNextMonth = () => setCalendarDate(prev => new Date(prev.getFullYear(), prev.getMonth() + 1, 1));

  // --- Fitur Detail Helpers ---
  const getMonthlyDayCounts = (dates) => {
    const counts = Array(12).fill(0);
    const uniqueDates = new Set();

    (Array.isArray(dates) ? dates : []).forEach(dateValue => {
      const date = dateValue instanceof Date ? dateValue : new Date(dateValue);
      if (Number.isNaN(date.getTime()) || date.getUTCDay() === 0) return;

      const dateKey = date.toISOString().split('T')[0];
      if (uniqueDates.has(dateKey)) return;

      uniqueDates.add(dateKey);
      counts[date.getUTCMonth()] += 1;
    });

    return counts;
  };

  // Detail totals calculation for current filtered dataset
  const detailTotals = filteredData.reduce((acc, item) => {
    const originalData = item.originalData || {};
    acc.batch += Number(originalData.TARGET_BATCH ?? item.batchCount ?? originalData.total_batch ?? 1);
    acc.participants += Number(originalData.TARGET_PESERTA ?? item.participants ?? originalData.target_total ?? originalData.target_peserta ?? 0);
    acc.promosiMandor += Number(originalData.Promosi_mandor || 0);
    acc.freshGraduate += Number(originalData.Fresh_Graduate || 0);
    acc.trainingDays += Number(originalData.Total_training_days || 0);
    acc.jamPelajaran += Number(originalData.jam_pelajaran || 0);
    acc.hariPelatihan += Number(originalData.hari_pelatihan || 0);
    acc.tdTotal += Number(originalData.TD_total || 0);
    return acc;
  }, {
    batch: 0,
    participants: 0,
    promosiMandor: 0,
    freshGraduate: 0,
    trainingDays: 0,
    jamPelajaran: 0,
    hariPelatihan: 0,
    tdTotal: 0
  });

  const exportDetailToPdf = () => {
    const document = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a3' });
    const activeCatObj = currentCategories.find(c => c.id === activeCategory);
    const categoryTitle = `${activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non Reguler'} - ${activeCatObj?.title || 'Semua'}`;

    if (activeMainTab === 'reguler') {
      const headers = [
        'Nama Program', 'Jenis Program', 'Region', 'Target Batch', 'Target Peserta', 'Lokasi',
        ...MONTH_NAMES_ID.map(month => month.slice(0, 3)),
        'Jlh Hari', 'Total Training Days', 'Promosi Mandor', 'Fresh Graduated'
      ];
      const rows = filteredData.map(item => {
        const monthlyCounts = getMonthlyDayCounts(item.tanggal_pelaksanaan);
        const jumlahHari = monthlyCounts.reduce((total, count) => total + count, 0);
        const originalData = item.originalData || {};
        return [
          item.title || '-', item.type || '-', item.region || 'Nasional', originalData.total_batch ?? (item.batchCount || 1),
          item.participants || 0, item.location || '-', ...monthlyCounts, jumlahHari,
          originalData.Total_training_days || 0, originalData.Promosi_mandor || 0,
          originalData.Fresh_Graduate || 0
        ];
      });

      rows.push([
        'TOTAL', '-', '-', detailTotals.batch, detailTotals.participants, '-',
        ...Array(12).fill(''), '', detailTotals.trainingDays, detailTotals.promosiMandor, detailTotals.freshGraduate
      ]);

      document.setFontSize(16);
      document.text(categoryTitle, 14, 14);
      document.setFontSize(9);
      document.text(`Detail seluruh program - Region: ${filterRegion}`, 14, 20);

      autoTable(document, {
        head: [headers],
        body: rows,
        startY: 25,
        theme: 'grid',
        styles: { fontSize: 7, cellPadding: 1.5, halign: 'center', valign: 'middle' },
        headStyles: { fillColor: [34, 197, 94], textColor: [15, 23, 42], fontStyle: 'bold' },
        columnStyles: { 0: { halign: 'left', cellWidth: 40 }, 5: { cellWidth: 28 } },
        didParseCell: dataCell => {
          if (dataCell.section === 'body' && dataCell.column.index >= 6 && dataCell.column.index <= 17 && Number(dataCell.cell.raw) > 0) {
            dataCell.cell.styles.fillColor = [220, 252, 231];
            dataCell.cell.styles.textColor = [21, 128, 61];
          }
        }
      });
    } else {
      // Non-Reguler PDF
      const headers = [
        'Nama Program', 'Kategori', 'Sub Kategori', 'Region', 'Target Batch', 'Target Peserta', 'Lokasi',
        ...MONTH_NAMES_ID.map(month => month.slice(0, 3)),
        'Jlh Hari', 'Jam', 'Hari', 'TD Total'
      ];
      const rows = filteredData.map(item => {
        const monthlyCounts = getMonthlyDayCounts(item.tanggal_pelaksanaan);
        const jumlahHari = monthlyCounts.reduce((total, count) => total + count, 0);
        const originalData = item.originalData || {};
        return [
          item.title || '-', originalData.kategori_pelatihan || '-', item.subCategory || '-', item.region || 'Nasional',
          originalData.total_batch ?? (item.batchCount || 1), item.participants || 0, item.location || '-',
          ...monthlyCounts, jumlahHari, originalData.jam_pelajaran || 0, originalData.hari_pelatihan || 0, originalData.TD_total || 0
        ];
      });

      rows.push([
        'TOTAL', '-', '-', '-', detailTotals.batch, detailTotals.participants, '-',
        ...Array(12).fill(''), '', detailTotals.jamPelajaran, detailTotals.hariPelatihan, detailTotals.tdTotal
      ]);

      document.setFontSize(16);
      document.text(categoryTitle, 14, 14);
      document.setFontSize(9);
      document.text(`Detail seluruh program non reguler - Region: ${filterRegion}`, 14, 20);

      autoTable(document, {
        head: [headers],
        body: rows,
        startY: 25,
        theme: 'grid',
        styles: { fontSize: 7, cellPadding: 1.5, halign: 'center', valign: 'middle' },
        headStyles: { fillColor: [34, 197, 94], textColor: [15, 23, 42], fontStyle: 'bold' },
        columnStyles: { 0: { halign: 'left', cellWidth: 40 }, 6: { cellWidth: 28 } },
        didParseCell: dataCell => {
          if (dataCell.section === 'body' && dataCell.column.index >= 7 && dataCell.column.index <= 18 && Number(dataCell.cell.raw) > 0) {
            dataCell.cell.styles.fillColor = [220, 252, 231];
            dataCell.cell.styles.textColor = [21, 128, 61];
          }
        }
      });
    }

    const filename = `${categoryTitle.replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '')}-detail.pdf`;
    document.save(filename);
  };

  const exportAllToExcel = () => {
    const workbook = XLSX.utils.book_new();

    const createSheet = (rows, headerOrder) => {
      const headers = headerOrder || [...new Set(rows.flatMap(row => Object.keys(row)))];
      const sheet = XLSX.utils.json_to_sheet(rows, { header: headers });
      const range = XLSX.utils.decode_range(sheet['!ref'] || 'A1:A1');
      const border = {
        top: { style: 'thin', color: { rgb: 'D3D3D3' } },
        bottom: { style: 'thin', color: { rgb: 'D3D3D3' } },
        left: { style: 'thin', color: { rgb: 'D3D3D3' } },
        right: { style: 'thin', color: { rgb: 'D3D3D3' } }
      };

      for (let rowIndex = range.s.r; rowIndex <= range.e.r; rowIndex += 1) {
        for (let columnIndex = range.s.c; columnIndex <= range.e.c; columnIndex += 1) {
          const cellAddress = XLSX.utils.encode_cell({ r: rowIndex, c: columnIndex });
          if (!sheet[cellAddress]) sheet[cellAddress] = { t: 's', v: '' };
          sheet[cellAddress].s = {
            border,
            alignment: { vertical: 'center', wrapText: true, horizontal: 'center' },
            ...(rowIndex === 0 ? {
              fill: { fgColor: { rgb: '008000' } },
              font: { bold: true, color: { rgb: 'FFFFFF' }, sz: 11 }
            } : {})
          };
        }
      }

      sheet['!cols'] = headers.map(header => ({
        wch: Math.min(Math.max(header.length + 3, 14), 30)
      }));
      sheet['!rows'] = [{ hpt: 24 }];
      return sheet;
    };

    const regulerRows = data.filter(item => item.type === 'Reguler').map(item => {
      const orig = item.originalData || {};
      const dates = Array.isArray(orig.tanggal_pelaksanaan) ? orig.tanggal_pelaksanaan.join(', ') : (orig.tanggal_pelaksanaan || '');
      return {
        'Program Pelatihan': orig.Training_Program || item.title || '',
        'Batch': orig.Batch || '',
        'Kategori Reguler': orig.jenis_program_reguler || '',
        'Region': orig.Region || item.region || '',
        'Lokasi Training': orig.Lokasi_training || item.location || '',
        'Mulai Program': orig.Mulai_program || '',
        'Selesai Program': orig.Selesai_program || '',
        'Tanggal Pelaksanaan': dates,
        'Jumlah Hari': orig.jumlah_hari || 0,
        'Promosi Mandor': orig.Promosi_mandor || 0,
        'Fresh Graduate': orig.Fresh_Graduate || 0,
        'Target Peserta': orig.Total_orang || orig.target_peserta || item.participants || 0,
        'Total Training Days': orig.Total_training_days || 0,
        'Uang Saku': orig.Uang_saku || 0,
      };
    });

    const nonRegulerRows = data.filter(item => item.type === 'Non-Reguler').map(item => {
      const orig = item.originalData || {};
      const dates = Array.isArray(orig.tanggal_pelaksanaan) ? orig.tanggal_pelaksanaan.join(', ') : (orig.tanggal_pelaksanaan || '');
      return {
        'Program Pelatihan': orig.jenis_pelatihan || item.title || '',
        'Kategori Pelatihan': orig.kategori_pelatihan || '',
        'Sub Kategori': orig.sub_kategori || item.subCategory || '',
        'Target Batch': orig.total_batch ?? item.batchCount ?? 0,
        'Type Training': orig.type_training || '',
        'Region': orig.region || item.region || '',
        'Lokasi Training': orig.lokasi_training || item.location || '',
        'Trainer': orig.trainer || item.trainer || '',
        'Tanggal Pelaksanaan': dates,
        'Total Hari': orig.total_hari || 0,
        'Total Jam': orig.total_jam || 0,
        'Target Peserta': orig.target_total || item.participants || 0,
        'Total Training Days': orig.td_total || 0,
      };
    });

    XLSX.utils.book_append_sheet(workbook, createSheet(regulerRows), 'Program Reguler');
    XLSX.utils.book_append_sheet(workbook, createSheet(nonRegulerRows), 'Program Non-Reguler');
    XLSX.writeFile(workbook, 'semua-data-program-training.xlsx');
  };

  const exportAllToCsv = () => {
    const isReguler = activeMainTab === 'reguler';
    const currentList = filteredData;
    const records = currentList.map(item => {
      const orig = item.originalData || {};
      if (isReguler) {
        return {
          'Tipe': 'Reguler',
          'Program Pelatihan': orig.Training_Program || item.title || '',
          'Batch': orig.Batch || '',
          'Kategori Reguler': orig.jenis_program_reguler || '',
          'Region': orig.Region || item.region || '',
          'Lokasi Training': orig.Lokasi_training || item.location || '',
          'Hari Training': `${orig.jumlah_hari || 0} Hari`,
          'Promosi Mandor': orig.Promosi_mandor || 0,
          'Fresh Graduate': orig.Fresh_Graduate || 0,
          'Target Peserta': orig.Total_orang || orig.target_peserta || item.participants || 0,
        };
      } else {
        return {
          'Tipe': 'Non-Reguler',
          'Program Pelatihan': orig.jenis_pelatihan || item.title || '',
          'Kategori Pelatihan': orig.kategori_pelatihan || '',
          'Sub Kategori': orig.sub_kategori || item.subCategory || '',
          'Target Batch': orig.total_batch ?? item.batchCount ?? 0,
          'Region': orig.region || item.region || '',
          'Lokasi Training': orig.lokasi_training || item.location || '',
          'Trainer': orig.trainer || item.trainer || '',
          'Total Jam / Hari': `${orig.total_jam || 0} Jam / ${orig.total_hari || 0} Hari`,
          'Target Peserta': orig.target_total || item.participants || 0,
        };
      }
    });

    if (records.length === 0) {
      alert('Tidak ada data untuk diexport.');
      return;
    }

    const headers = Object.keys(records[0]);
    const escapeCsvValue = value => `"${String(value ?? '').replace(/"/g, '""')}"`;
    const csv = [headers.map(escapeCsvValue), ...records.map(record => headers.map(header => escapeCsvValue(record[header])))]
      .map(row => row.join(','))
      .join('\r\n');
    const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `data-${activeMainTab}-training.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
  };

  const openAddModal = () => {
    const isReg = activeMainTab === 'reguler';
    setFormType(isReg ? 'reguler' : 'non_reguler');
    if (isReg) {
      setFormData({
        jenis_program_reguler: activeCategory === 'mandor' ? 'Training Reguler - Mandor' : 'Training Reguler - Staf'
      });
    } else {
      setFormData({
        kategori_pelatihan: activeCategory === 'technical' ? 'Technical' : 'Managerial and Leadership'
      });
    }
    setIsEditMode(false);
    setEditId(null);
    setIsModalOpen(true);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
      {/* Top Header & Action Buttons */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl font-bold text-slate-800">Manajemen Data Program Training</h1>
            <div className="bg-white border border-slate-200 px-3.5 py-1 rounded-full flex items-center gap-2 shadow-xs">
              <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              <span className="font-semibold text-slate-700 text-xs">Tahun Aktif: {new Date().getFullYear()}</span>
            </div>
          </div>
          <p className="text-slate-500 text-sm mt-1">Kelola dan pantau program pelatihan reguler dan non-reguler FR Academy.</p>
        </div>
        <div className="flex flex-wrap items-center gap-3">
          <button
            type="button"
            onClick={() => {
              setImportFile(null);
              setImportMessage(null);
              setIsImportModalOpen(true);
            }}
            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-2xs cursor-pointer"
          >
            <Upload size={17} className="text-[#4B6A34]" />
            <span>Import Data</span>
          </button>
          <a
            href={`/administrasi/program-batch/template?type=${activeMainTab === 'reguler' ? 'reguler' : 'non_reguler'}`}
            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition shadow-2xs cursor-pointer"
            title={`Download Template Excel ${activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non-Reguler'}`}
          >
            <Download size={17} className="text-[#4B6A34]" />
            <span>Template Excel</span>
          </a>
          <button
            onClick={exportAllToExcel}
            className="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2 text-sm"
          >
            <FileSpreadsheet size={17} /> Export Excel
          </button>
          <button
            onClick={openAddModal}
            className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2 text-sm cursor-pointer"
          >
            <Plus size={17} /> Tambah Data
          </button>
          <button
            onClick={openDeleteAllModal}
            className="border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 px-3.5 py-2 rounded-lg font-medium transition-colors flex items-center gap-1.5 text-sm cursor-pointer shadow-2xs"
            title={`Hapus semua data program ${activeMainTab === 'reguler' ? 'reguler' : 'non-reguler'}`}
          >
            <Trash2 size={16} /> Hapus Semua
          </button>
        </div>
      </div>

      {/* Primary Tab Switcher (Reguler, Non Reguler Soft Skill, Non Reguler Teknikal, Semua Non Reguler) */}
      <div className="flex flex-wrap items-center gap-2 p-1.5 bg-slate-200/70 rounded-xl w-fit border border-slate-300/60 shadow-inner">
        <button
          type="button"
          onClick={() => {
            setActiveMainTab('reguler');
            setActiveCategory('all');
            setFilterRegion('Semua Region');
            setFilterSubKategori('Semua Sub Kategori');
            setCurrentPage(1);
          }}
          className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${
            activeMainTab === 'reguler'
              ? 'bg-white text-green-700 shadow-sm ring-1 ring-slate-200'
              : 'text-slate-600 hover:text-slate-900'
          }`}
        >
          <GraduationCap size={17} />
          <span>Reguler</span>
          <span className={`text-xs px-2 py-0.5 rounded-full font-bold ${
            activeMainTab === 'reguler' ? 'bg-green-100 text-green-800' : 'bg-slate-300 text-slate-700'
          }`}>
            {regulerTotalCount}
          </span>
        </button>

        <button
          type="button"
          onClick={() => {
            setActiveMainTab('non_reguler');
            setActiveCategory('managerial');
            setFilterRegion('Semua Region');
            setFilterSubKategori('Semua Sub Kategori');
            setCurrentPage(1);
          }}
          className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${
            activeMainTab === 'non_reguler' && activeCategory === 'managerial'
              ? 'bg-white text-amber-700 shadow-sm ring-1 ring-slate-200'
              : 'text-slate-600 hover:text-slate-900'
          }`}
        >
          <Award size={17} />
          <span>Non Reguler - Soft Skill</span>
          <span className={`text-xs px-2 py-0.5 rounded-full font-bold ${
            activeMainTab === 'non_reguler' && activeCategory === 'managerial' ? 'bg-amber-100 text-amber-800' : 'bg-slate-300 text-slate-700'
          }`}>
            {nonRegulerManagerialCount}
          </span>
        </button>

        <button
          type="button"
          onClick={() => {
            setActiveMainTab('non_reguler');
            setActiveCategory('technical');
            setFilterRegion('Semua Region');
            setFilterSubKategori('Semua Sub Kategori');
            setCurrentPage(1);
          }}
          className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${
            activeMainTab === 'non_reguler' && activeCategory === 'technical'
              ? 'bg-white text-blue-700 shadow-sm ring-1 ring-slate-200'
              : 'text-slate-600 hover:text-slate-900'
          }`}
        >
          <Wrench size={17} />
          <span>Non Reguler - Teknikal</span>
          <span className={`text-xs px-2 py-0.5 rounded-full font-bold ${
            activeMainTab === 'non_reguler' && activeCategory === 'technical' ? 'bg-blue-100 text-blue-800' : 'bg-slate-300 text-slate-700'
          }`}>
            {nonRegulerTechnicalCount}
          </span>
        </button>

        <button
          type="button"
          onClick={() => {
            setActiveMainTab('non_reguler');
            setActiveCategory('all');
            setFilterRegion('Semua Region');
            setFilterSubKategori('Semua Sub Kategori');
            setCurrentPage(1);
          }}
          className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${
            activeMainTab === 'non_reguler' && activeCategory === 'all'
              ? 'bg-white text-green-700 shadow-sm ring-1 ring-slate-200'
              : 'text-slate-600 hover:text-slate-900'
          }`}
        >
          <Layers size={17} />
          <span>Semua Non Reguler</span>
          <span className={`text-xs px-2 py-0.5 rounded-full font-bold ${
            activeMainTab === 'non_reguler' && activeCategory === 'all' ? 'bg-green-100 text-green-800' : 'bg-slate-300 text-slate-700'
          }`}>
            {nonRegulerTotalCount}
          </span>
        </button>
      </div>

      {/* Category Cards Grid (Styled identically to reference image) */}
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        {currentCategories.map(cat => {
          const isActive = activeCategory === cat.id;
          const Icon = cat.icon;
          return (
            <button
              key={cat.id}
              onClick={() => {
                setActiveCategory(cat.id);
                setCurrentPage(1);
              }}
              className={`text-left p-5 rounded-2xl border transition-all relative ${
                isActive
                  ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600'
                  : 'border-slate-200 bg-white hover:border-slate-300 shadow-sm'
              }`}
            >
              <div className="flex justify-between items-start mb-5">
                <div className={`w-11 h-11 rounded-full flex items-center justify-center transition-colors ${
                  isActive ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'
                }`}>
                  <Icon size={20} />
                </div>
                {isActive && <div className="w-2.5 h-2.5 rounded-full bg-green-600"></div>}
              </div>
              <h3 className={`font-bold text-base leading-snug ${isActive ? 'text-slate-800' : 'text-slate-700'}`}>
                {cat.title}
              </h3>
              <div className="flex justify-between items-center mt-1.5">
                <p className="text-xs text-slate-400 font-normal">Kategori Program</p>
                <span className={`text-xs font-semibold px-2 py-0.5 rounded-full ${
                  isActive ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600'
                }`}>
                  {cat.count} Data
                </span>
              </div>
            </button>
          );
        })}
      </div>

      {/* 3 Metric Summary Cards (Identical to Kalender Training) */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-5">
          <div className="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
            <BookOpen size={24} />
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">Total Program</p>
            <p className="text-2xl font-extrabold text-slate-800 leading-none">{totalProgram}</p>
            <p className="text-xs text-emerald-600 mt-1.5 font-medium flex items-center gap-1">
              <span className="w-3 h-3 flex items-center justify-center bg-emerald-100 rounded-full"><span className="text-[8px]">✓</span></span> {activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non-Reguler'} ({new Date().getFullYear()})
            </p>
          </div>
        </div>

        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-5">
          <div className="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
            <Layers size={24} />
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">Target Batch / Tahun</p>
            <p className="text-2xl font-extrabold text-slate-800 leading-none">{totalBatch}</p>
            <p className="text-xs text-blue-600 mt-1.5 font-medium flex items-center gap-1">
              <span>↻</span> Total rencana realisasi batch
            </p>
          </div>
        </div>

        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-5">
          <div className="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
            <Users size={24} />
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">Target Peserta / Tahun</p>
            <p className="text-2xl font-extrabold text-slate-800 leading-none">{totalPeserta} <span className="text-sm font-semibold text-slate-400">org</span></p>
            <p className="text-xs text-purple-600 mt-1.5 font-medium flex items-center gap-1">
              <span>↗</span> Jumlah kuota peserta
            </p>
          </div>
        </div>
      </div>

      {/* Data Table Card with Filter Toolbar */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div className="p-4 border-b border-slate-200 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-slate-50">
          <div className="flex flex-wrap items-center gap-3">
            <div className="relative w-full sm:w-72">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={17} />
              <input
                type="text"
                placeholder={activeMainTab === 'reguler' ? "Cari pelatihan reguler..." : "Cari pelatihan non reguler..."}
                value={searchTerm}
                onChange={(e) => { setSearchTerm(e.target.value); setCurrentPage(1); }}
                className="w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all shadow-2xs"
              />
            </div>

            <select
              value={filterRegion}
              onChange={(e) => { setFilterRegion(e.target.value); setCurrentPage(1); }}
              className="border border-slate-300 rounded-lg px-3 py-2 text-slate-700 text-sm font-medium outline-none focus:border-green-500 w-full sm:w-44 bg-white shadow-2xs cursor-pointer"
            >
              <option value="Semua Region">Semua Region</option>
              {uniqueRegions.map((reg, idx) => (
                <option key={idx} value={reg}>{reg}</option>
              ))}
            </select>

            <select
              value={filterTahun}
              onChange={(e) => { setFilterTahun(e.target.value); setCurrentPage(1); }}
              className="border border-slate-300 rounded-lg px-3 py-2 text-slate-700 text-sm font-medium outline-none focus:border-green-500 w-full sm:w-36 bg-white shadow-2xs cursor-pointer"
            >
              <option value="Semua Tahun">Semua Tahun</option>
              {uniqueYears.map((y, idx) => (
                <option key={idx} value={y}>Tahun {y}</option>
              ))}
            </select>

            {activeMainTab === 'non_reguler' && (
              <select
                value={filterSubKategori}
                onChange={(e) => { setFilterSubKategori(e.target.value); setCurrentPage(1); }}
                className="border border-slate-300 rounded-lg px-3 py-2 text-slate-700 text-sm font-medium outline-none focus:border-green-500 w-full sm:w-48 bg-white shadow-2xs cursor-pointer"
              >
                <option value="Semua Sub Kategori">Semua Sub Kategori</option>
                {uniqueSubKategories.map((sub, idx) => (
                  <option key={idx} value={sub}>{sub}</option>
                ))}
              </select>
            )}

            <button
              onClick={() => setIsDetailModalOpen(true)}
              className="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-sm font-bold transition-colors whitespace-nowrap shadow-2xs cursor-pointer"
              title="Lihat Detail Program & Matriks Bulanan"
            >
              <ClipboardList size={16} />
              <span>Lihat Detail</span>
            </button>
          </div>

          <div className="text-xs text-slate-500 font-medium self-end md:self-auto">
            Total Ditemukan: <span className="font-bold text-slate-700">{filteredData.length}</span> baris
          </div>
        </div>

        {/* Table Content: Distinct for Program Reguler and Program Non-Reguler */}
        {activeMainTab === 'reguler' ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-50/70 text-slate-600 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                  <th className="px-4 py-3.5">Program Pelatihan</th>
                  <th className="px-4 py-3.5">Batch</th>
                  <th className="px-4 py-3.5">Kategori Reguler</th>
                  <th className="px-4 py-3.5">Tanggal</th>
                  <th className="px-4 py-3.5">Region</th>
                  <th className="px-4 py-3.5">Lokasi</th>
                  <th className="px-4 py-3.5 text-center">Hari Training</th>
                  <th className="px-4 py-3.5 text-center">Promosi Mandor</th>
                  <th className="px-4 py-3.5 text-center">Fresh Graduate</th>
                  <th className="px-4 py-3.5 text-center">Target Peserta</th>
                  <th className="px-4 py-3.5 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="text-sm divide-y divide-slate-100">
                {loading ? (
                  <tr>
                    <td colSpan="11" className="px-4 py-12 text-center">
                      <div className="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                    </td>
                  </tr>
                ) : paginatedData.length === 0 ? (
                  <tr>
                    <td colSpan="11" className="px-4 py-10 text-center text-slate-500">Tidak ada data program reguler ditemukan.</td>
                  </tr>
                ) : (
                  paginatedData.map((item) => (
                    <tr key={item.id} className="hover:bg-slate-50/70 transition-colors group">
                      <td className="px-4 py-4 font-semibold text-slate-800">{item.title}</td>
                      <td className="px-4 py-4 text-slate-600 font-medium">
                        <span className="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-semibold">
                          {item.originalData?.Batch || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-4">
                        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${
                          cleanText(item.originalData?.jenis_program_reguler).includes('mandor')
                            ? 'bg-amber-50 text-amber-700 border border-amber-200'
                            : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                        }`}>
                          {item.originalData?.jenis_program_reguler || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-slate-600 whitespace-nowrap text-xs">
                        {item.originalData?.start_date || item.originalData?.mulai_program || item.originalData?.Mulai_program
                          ? `${item.start && !isNaN(item.start) ? item.start.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'} - ${item.end && !isNaN(item.end) ? item.end.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'}`
                          : '-'}
                      </td>
                      <td className="px-4 py-4 font-medium">{item.region || '-'}</td>
                      <td className="px-4 py-4">
                        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white shadow-2xs">
                          <span className={`w-1.5 h-1.5 rounded-full ${item.location ? 'bg-green-500' : 'bg-slate-300'}`}></span>
                          {item.location || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-slate-600 text-center font-medium">{item.originalData?.jumlah_hari || 0} Hari</td>
                      <td className="px-4 py-4 text-slate-600 text-center">{item.originalData?.Promosi_mandor ?? 0}</td>
                      <td className="px-4 py-4 text-slate-600 text-center">{item.originalData?.Fresh_Graduate ?? 0}</td>
                      <td className="px-4 py-4 text-slate-800 text-center font-bold">{item.originalData?.Total_orang || item.participants || 0}</td>
                      <td className="px-4 py-4 text-right">
                        <div className="flex justify-end items-center gap-1.5">
                          <button
                            onClick={() => { setSelectedProgram(item); setIsCalendarModalOpen(true); }}
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 text-xs font-bold transition-colors border border-green-200 shadow-2xs"
                            title="Lihat Kalender Jadwal"
                          >
                            <CalendarIcon size={13} /> Jadwal
                          </button>
                          <button onClick={() => handleEdit(item)} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Edit"><Edit2 size={16} /></button>
                          <button onClick={() => handleDelete(item)} className="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus"><Trash2 size={16} /></button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-50/70 text-slate-600 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                  <th className="px-4 py-3.5">Program Pelatihan</th>
                  <th className="px-4 py-3.5">Kategori Pelatihan</th>
                  <th className="px-4 py-3.5">Sub Kategori</th>
                  <th className="px-4 py-3.5 text-center">Target Batch</th>
                  <th className="px-4 py-3.5">Tanggal</th>
                  <th className="px-4 py-3.5">Region</th>
                  <th className="px-4 py-3.5">Lokasi</th>
                  <th className="px-4 py-3.5">Trainer</th>
                  <th className="px-4 py-3.5">Total Jam/Hari</th>
                  <th className="px-4 py-3.5 text-center">Target Peserta</th>
                  <th className="px-4 py-3.5 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="text-sm divide-y divide-slate-100">
                {loading ? (
                  <tr>
                    <td colSpan="11" className="px-4 py-12 text-center">
                      <div className="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                    </td>
                  </tr>
                ) : paginatedData.length === 0 ? (
                  <tr>
                    <td colSpan="11" className="px-4 py-10 text-center text-slate-500">Tidak ada data program non reguler ditemukan.</td>
                  </tr>
                ) : (
                  paginatedData.map((item) => (
                    <tr key={item.id} className="hover:bg-slate-50/70 transition-colors group">
                      <td className="px-4 py-4 font-semibold text-slate-800">{item.title}</td>
                      <td className="px-4 py-4">
                        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${
                          cleanText(item.originalData?.kategori_pelatihan).includes('managerial')
                            ? 'bg-blue-50 text-blue-700 border border-blue-200'
                            : 'bg-purple-50 text-purple-700 border border-purple-200'
                        }`}>
                          {item.originalData?.kategori_pelatihan || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-slate-600 text-sm">
                        {item.subCategory !== '-' && item.subCategory ? (
                          <span className="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-medium">
                            {item.subCategory}
                          </span>
                        ) : <span className="text-slate-400">-</span>}
                      </td>
                      <td className="px-4 py-4 text-center">
                        <span className="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                          {item.originalData?.total_batch ?? item.batchCount ?? 0}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-slate-600 whitespace-nowrap text-xs">
                        {item.originalData?.start_date || item.originalData?.mulai_program || item.originalData?.Mulai_program
                          ? `${item.start && !isNaN(item.start) ? item.start.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'} - ${item.end && !isNaN(item.end) ? item.end.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD'}`
                          : '-'}
                      </td>
                      <td className="px-4 py-4 text-slate-600 font-medium">{item.region || '-'}</td>
                      <td className="px-4 py-4">
                        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-slate-200 text-xs font-medium text-slate-700 bg-white shadow-2xs">
                          <span className={`w-1.5 h-1.5 rounded-full ${item.location ? 'bg-green-500' : 'bg-slate-300'}`}></span>
                          {item.location || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-4 text-slate-600">{item.trainer || '-'}</td>
                      <td className="px-4 py-4 text-slate-600 whitespace-nowrap text-xs">
                        {`${item.originalData?.total_jam || 0} Jam / ${item.originalData?.total_hari || 0} Hari`}
                      </td>
                      <td className="px-4 py-4 text-slate-800 text-center font-bold">{item.participants}</td>
                      <td className="px-4 py-4 text-right">
                        <div className="flex justify-end items-center gap-1.5">
                          <button
                            onClick={() => { setSelectedProgram(item); setIsCalendarModalOpen(true); }}
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 text-xs font-bold transition-colors border border-green-200 shadow-2xs"
                            title="Lihat Kalender Jadwal"
                          >
                            <CalendarIcon size={13} /> Jadwal
                          </button>
                          <button onClick={() => handleEdit(item)} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Edit"><Edit2 size={16} /></button>
                          <button onClick={() => handleDelete(item)} className="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus"><Trash2 size={16} /></button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        <div className="p-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-sm text-slate-500 bg-white gap-4">
          <div className="flex items-center gap-3">
            <span>Showing {filteredData.length === 0 ? 0 : startIndex + 1} to {Math.min(startIndex + itemsPerPage, filteredData.length)} of {filteredData.length} records</span>
            <select
              value={itemsPerPage}
              onChange={(e) => {
                setItemsPerPage(Number(e.target.value));
                setCurrentPage(1);
              }}
              className="border border-slate-300 rounded px-2 py-1 text-slate-700 text-sm font-medium outline-none focus:border-blue-500 bg-slate-50 cursor-pointer"
            >
              <option value={10}>10</option>
              <option value={25}>25</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
            </select>
          </div>

          <div className="flex items-center gap-1">
            <button
              onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
              disabled={currentPage === 1 || loading}
              className="w-8 h-8 flex items-center justify-center rounded text-slate-400 hover:text-slate-700 disabled:opacity-30 transition-colors">
              <ChevronLeft size={16} />
            </button>

            <div className="flex items-center gap-1 mx-1">
              {Array.from({ length: totalPages }).map((_, i) => {
                if (totalPages > 7) {
                  if (i === 0 || i === totalPages - 1 || (i >= currentPage - 2 && i <= currentPage)) {
                    return (
                      <button
                        key={i}
                        onClick={() => setCurrentPage(i + 1)}
                        className={`w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors ${currentPage === i + 1 ? 'bg-[#0095ff] text-white' : 'text-slate-600 hover:bg-slate-100'}`}
                      >
                        {i + 1}
                      </button>
                    );
                  }
                  if (i === 1 && currentPage > 3) return <span key={i} className="px-1 text-slate-400">...</span>;
                  if (i === totalPages - 2 && currentPage < totalPages - 2) return <span key={i} className="px-1 text-slate-400">...</span>;
                  return null;
                }

                return (
                  <button
                    key={i}
                    onClick={() => setCurrentPage(i + 1)}
                    className={`w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors ${currentPage === i + 1 ? 'bg-[#0095ff] text-white' : 'text-slate-600 hover:bg-slate-100'}`}
                  >
                    {i + 1}
                  </button>
                );
              })}
            </div>

            <button
              onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
              disabled={currentPage === totalPages || totalPages === 0 || loading}
              className="w-8 h-8 flex items-center justify-center rounded text-slate-400 hover:text-slate-700 disabled:opacity-30 transition-colors">
              <ChevronRight size={16} />
            </button>
          </div>
        </div>
      </div>

      {/* Form Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div className="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col animate-in fade-in zoom-in-95 duration-200">
            <div className="p-5 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-2xl">
              <h2 className="text-xl font-bold text-slate-800">{isEditMode ? 'Edit Data Pelatihan' : 'Tambah Data Pelatihan'}</h2>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
            </div>

            <div className="flex-1 overflow-y-auto p-6">
              <form id="trainingForm" onSubmit={handleSubmit} className="space-y-6">

                <div className="flex gap-4 p-1 bg-slate-100 rounded-lg w-max">
                  <button
                    type="button"
                    onClick={() => { setFormType('non_reguler'); setFormData({}); }}
                    className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${formType === 'non_reguler' ? 'bg-white shadow-sm text-green-700' : 'text-slate-600 hover:text-slate-900'}`}
                  >
                    Non-Reguler
                  </button>
                  <button
                    type="button"
                    onClick={() => { setFormType('reguler'); setFormData({}); }}
                    className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${formType === 'reguler' ? 'bg-white shadow-sm text-green-700' : 'text-slate-600 hover:text-slate-900'}`}
                  >
                    Reguler
                  </button>
                </div>

                {formType === 'non_reguler' ? (
                  <>
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Jenis Pelatihan</label>
                        <input required type="text" name="jenis_pelatihan" value={formData.jenis_pelatihan || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Tipe Training</label>
                        <select required name="type_training" value={formData.type_training || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Tipe</option>
                          <option value="Workshop">Workshop</option>
                          <option value="W & M">W & M</option>
                          <option value="Onsite">Onsite</option>
                          <option value="Online">Online</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Region</label>
                        <select required name="region" value={formData.region || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Region</option>
                          <option value="Riau">Riau</option>
                          <option value="Kalbar">Kalbar</option>
                          <option value="Kaltim FR">Kaltim FR</option>
                          <option value="Corporate">Corporate</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Trainer</label>
                        <input
                          type="text"
                          list="trainer-options"
                          name="trainer"
                          value={formData.trainer || ''}
                          onChange={handleInputChange}
                          placeholder="Pilih atau ketik nama trainer"
                          className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white"
                        />
                        <datalist id="trainer-options">
                          <option value="Eksternal" />
                          <option value="Internal" />
                          <option value="Eksternal & Internal" />
                        </datalist>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Lokasi Training</label>
                        <input type="text" name="lokasi_training" value={formData.lokasi_training || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Kategori Pelatihan</label>
                        <select required name="kategori_pelatihan" value={formData.kategori_pelatihan || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Kategori Pelatihan</option>
                          <option value="Managerial and Leadership">Managerial and Leadership</option>
                          <option value="Technical">Technical</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Sub Kategori</label>
                        <select required name="sub_kategori" value={formData.sub_kategori || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Sub Kategori</option>
                          <option value="Estate">Estate</option>
                          <option value="Mill">Mill</option>
                          <option value="Traksi">Traksi</option>
                          <option value="Administrasi">Administrasi</option>
                          <option value="Downstream">Downstream</option>
                          <option value="Softskill">Softskill</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Target Batch</label>
                        <input type="number" name="TARGET_BATCH" value={formData.TARGET_BATCH !== undefined ? formData.TARGET_BATCH : ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Realisasi Batch</label>
                        <input type="number" name="realisasi_batch" value={formData.realisasi_batch !== undefined ? formData.realisasi_batch : (formData.total_batch !== undefined ? formData.total_batch : '')} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Total Hari</label>
                        <input type="number" name="total_hari" value={formData.total_hari || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Total Jam / Hari</label>
                        <input type="number" name="total_jam" value={formData.total_jam || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Tahun Program</label>
                        <input type="number" name="tahun" placeholder="e.g. 2026" value={formData.tahun || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div className="col-span-2">
                        <label className="block text-sm font-medium text-slate-700 mb-1">Tanggal Pelaksanaan (Multi)</label>
                        <DatePicker
                          multiple
                          range
                          value={groupDatesIntoRanges(formData.tanggal_pelaksanaan)}
                          onChange={handleDateChange}
                          format="YYYY-MM-DD"
                          placeholder="Pilih rentang/beberapa tanggal..."
                          inputClass="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                          containerClassName="w-full"
                        />
                      </div>
                    </div>

                    <div className="border-t border-slate-200 pt-4 mt-4">
                      <h4 className="font-medium text-slate-800 mb-3">Peserta</h4>
                      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">Non-Staf</label>
                          <input type="number" name="peserta_non_staf" value={formData.peserta_non_staf || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">AST</label>
                          <input type="number" name="peserta_ast" value={formData.peserta_ast || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">Askep</label>
                          <input type="number" name="peserta_askep" value={formData.peserta_askep || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">Manager</label>
                          <input type="number" name="peserta_mgr" value={formData.peserta_mgr || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">Grup Manager</label>
                          <input type="number" name="peserta_gm" value={formData.peserta_gm || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-500 mb-1">Head</label>
                          <input type="number" name="peserta_head" value={formData.peserta_head || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div className="sm:col-span-2">
                          <label className="block text-xs text-slate-500 mb-1 font-bold">Target Total (Auto)</label>
                          <input type="number" readOnly value={formData.target_total || ''} className="w-full p-2 border border-slate-200 bg-slate-50 text-slate-500 rounded-lg font-bold" />
                        </div>
                      </div>
                    </div>
                  </>
                ) : (
                  <>
                    <div className="grid grid-cols-2 gap-4">
                      <div className="col-span-2">
                        <label className="block text-sm font-medium text-slate-700 mb-1">Nama Program Reguler</label>
                        <input required type="text" name="Nama_Program_reguler" value={formData.Nama_Program_reguler || formData.Training_Program || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Batch</label>
                        <input type="text" name="Batch" value={formData.Batch || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Lokasi Training</label>
                        <input type="text" name="Lokasi_training" value={formData.Lokasi_training || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Kategori Reguler</label>
                        <select required name="jenis_program_reguler" value={formData.jenis_program_reguler || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Kategori Reguler...</option>
                          <option value="Training Reguler - Staf">Training Reguler - Staf</option>
                          <option value="Training Reguler - Mandor">Training Reguler - Mandor</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Region</label>
                        <select name="Region" value={formData.Region || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none bg-white">
                          <option value="">Pilih Region</option>
                          <option value="Riau">Riau</option>
                          <option value="Kalbar">Kalbar</option>
                          <option value="Kaltim FR">Kaltim FR</option>
                          <option value="Corporate">Corporate</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Tahun Program</label>
                        <input type="number" name="tahun" placeholder="e.g. 2026" value={formData.tahun || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                      </div>
                      <div className="col-span-2">
                        <label className="block text-sm font-medium text-slate-700 mb-1">Tanggal Pelaksanaan (Multi)</label>
                        <DatePicker
                          multiple
                          range
                          value={groupDatesIntoRanges(formData.tanggal_pelaksanaan)}
                          onChange={handleDateChange}
                          format="YYYY-MM-DD"
                          placeholder="Pilih rentang/beberapa tanggal..."
                          inputClass="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                          containerClassName="w-full"
                        />
                      </div>
                    </div>

                    <div className="border-t border-slate-200 pt-4 mt-4 bg-blue-50/50 p-4 rounded-lg">
                      <h4 className="font-medium text-blue-800 mb-3 text-sm">Kalkulasi Otomatis (Total - Promosi Mandor = Fresh Graduate)</h4>
                      <div className="grid grid-cols-3 gap-4">
                        <div>
                          <label className="block text-xs text-slate-600 mb-1">Total Orang</label>
                          <input type="number" name="Total_orang" value={formData.Total_orang !== undefined ? formData.Total_orang : (formData.target_peserta || '')} onChange={handleInputChange} className="w-full p-2 border border-blue-200 rounded-lg" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-600 mb-1">Promosi Mandor</label>
                          <input type="number" name="Promosi_mandor" value={formData.Promosi_mandor || ''} onChange={handleInputChange} className="w-full p-2 border border-blue-200 rounded-lg" />
                        </div>
                        <div>
                          <label className="block text-xs text-blue-600 font-bold mb-1">Fresh Graduate (Auto)</label>
                          <input type="number" readOnly value={formData.Fresh_Graduate || ''} className="w-full p-2 border border-blue-200 bg-blue-100 text-blue-800 rounded-lg font-bold" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-600 mb-1">Total Hari</label>
                          <input type="number" name="jumlah_hari" value={formData.jumlah_hari || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-600 mb-1">Durasi (Bulan)</label>
                          <input type="number" name="jumlah_bulan_training" value={formData.jumlah_bulan_training || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div>
                          <label className="block text-xs text-slate-600 mb-1">Total Training Days</label>
                          <input type="number" name="Total_training_days" value={formData.Total_training_days || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                        <div className="col-span-3">
                          <label className="block text-xs text-slate-600 mb-1">Uang Saku</label>
                          <input type="number" name="Uang_saku" value={formData.Uang_saku || ''} onChange={handleInputChange} className="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" />
                        </div>
                      </div>
                    </div>
                  </>
                )}
              </form>
            </div>

            <div className="p-6 border-t border-slate-200 bg-slate-50 rounded-b-xl flex justify-end gap-3">
              <button
                type="button"
                onClick={() => setIsModalOpen(false)}
                className="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors"
              >
                Batal
              </button>
              <button type="submit" form="trainingForm" className="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-sm transition-colors">
                {isEditMode ? 'Simpan Perubahan' : 'Simpan Data'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Confirmation Dialog */}
      {isDeleteConfirmOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg p-6 max-w-sm mx-4">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <Trash2 size={20} className="text-red-600" />
              </div>
              <h3 className="text-lg font-semibold text-slate-900">Konfirmasi Penghapusan</h3>
            </div>

            <p className="text-slate-600 mb-6">
              Apakah kamu yakin ingin menghapus <span className="font-semibold text-slate-900">"{itemToDelete?.title}"</span>?
            </p>

            <div className="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
              <p className="text-sm text-amber-800">
                <span className="font-semibold">Perhatian:</span> Data yang dihapus tidak dapat dikembalikan.
              </p>
            </div>

            <div className="flex justify-end gap-3">
              <button
                onClick={() => {
                  setIsDeleteConfirmOpen(false);
                  setItemToDelete(null);
                }}
                className="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors"
              >
                Tidak
              </button>
              <button
                onClick={confirmDelete}
                className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors"
              >
                Ya, Hapus
              </button>
            </div>
          </div>
        </div>
      )}

      {/* 3-Step Confirmation Delete All Dialog */}
      {isDeleteAllModalOpen && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-red-100 animate-in fade-in zoom-in duration-150">
            {/* Modal Top Bar */}
            <div className="p-4 bg-red-50 border-b border-red-100 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                  <AlertTriangle size={18} />
                </div>
                <div>
                  <span className="text-[10px] font-extrabold uppercase tracking-wider bg-red-200 text-red-800 px-2 py-0.5 rounded-full">
                    LANGKAH {deleteAllStep} / 3
                  </span>
                </div>
              </div>
              <button
                type="button"
                onClick={closeDeleteAllModal}
                disabled={isDeletingAll}
                className="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-white transition-colors cursor-pointer"
              >
                <X size={18} />
              </button>
            </div>

            {/* Modal Body Content */}
            <div className="p-6 space-y-4">
              <div className="flex items-center gap-1.5 justify-center py-1">
                {[1, 2, 3].map(step => (
                  <div
                    key={step}
                    className={`h-2 rounded-full transition-all ${
                      step < deleteAllStep
                        ? 'w-6 bg-red-400'
                        : step === deleteAllStep
                        ? 'w-8 bg-red-600'
                        : 'w-2 bg-slate-200'
                    }`}
                  />
                ))}
              </div>

              <div className="text-center space-y-2">
                <h3 className="text-base font-extrabold text-slate-800">
                  {deleteStepWarnings[deleteAllStep - 1]?.title}
                </h3>
                <p className="text-xs text-slate-600 leading-relaxed max-w-md mx-auto">
                  {deleteStepWarnings[deleteAllStep - 1]?.desc}
                </p>
              </div>

              <div className="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2.5 text-xs text-amber-800">
                <AlertTriangle size={16} className="mt-0.5 shrink-0 text-amber-600" />
                <span>Sistem membutuhkan 3 kali konfirmasi beruntun demi keamanan data operasional.</span>
              </div>
            </div>

            {/* Modal Actions */}
            <div className="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
              <button
                type="button"
                onClick={closeDeleteAllModal}
                disabled={isDeletingAll}
                className="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer"
              >
                Batalkan
              </button>
              <button
                type="button"
                onClick={proceedDeleteAll}
                disabled={isDeletingAll}
                className={`px-5 py-2.5 text-white text-xs font-bold rounded-lg transition-colors shadow-xs cursor-pointer ${
                  deleteStepWarnings[deleteAllStep - 1]?.btnColor
                }`}
              >
                {isDeletingAll ? 'Sedang Menghapus...' : deleteStepWarnings[deleteAllStep - 1]?.confirmBtn}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Detail Program & Kalender Rencana Pelaksanaan (Sama persis Kalender Training) */}
      {isCalendarModalOpen && selectedProgram && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div className="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[95vh] flex flex-col animate-in fade-in zoom-in-95 duration-200 overflow-hidden">

            {/* Header */}
            <div className="p-6 border-b border-slate-100 flex justify-between items-start bg-[#fcfcf9]">
              <div>
                <span className={`inline-flex px-3 py-1 rounded-full text-[11px] font-bold ${selectedProgram.type === 'Reguler' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'}`}>
                  {selectedProgram.type}
                </span>
                <h2 className="text-xl font-extrabold text-slate-800 mt-3 leading-tight">{selectedProgram.title}</h2>
                <p className="text-sm text-slate-500 mt-1">Detail Rencana Pelaksanaan Training</p>
              </div>
              <button onClick={() => setIsCalendarModalOpen(false)} className="text-slate-400 hover:text-slate-700 p-2"><X size={20} /></button>
            </div>

            {/* Body */}
            <div className="flex-1 overflow-y-auto p-6 space-y-6 bg-white">

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Informasi Umum */}
                <div className="bg-slate-50/50 p-5 rounded-xl border border-slate-200">
                  <h3 className="font-bold text-slate-800 flex items-center gap-2 mb-4 text-sm border-b border-slate-100 pb-3">
                    <span className="text-green-600 font-bold border border-green-600 rounded-full w-4 h-4 flex items-center justify-center text-[10px]">i</span> Informasi Umum
                  </h3>
                  <div className="space-y-3 text-sm">
                    <div className="flex items-center"><span className="text-slate-500 w-36">Region:</span><span className="font-bold text-slate-800">{selectedProgram.region || 'Nasional'}</span></div>
                    <div className="flex items-center"><span className="text-slate-500 w-36">Trainer:</span><span className="font-bold text-slate-800">{selectedProgram.trainer || 'Internal'}</span></div>
                    <div className="flex items-center"><span className="text-slate-500 w-36">Sub Kategori:</span><span className="font-bold text-slate-800">{selectedProgram.subCategory || '-'}</span></div>
                    <div className="flex items-center"><span className="text-slate-500 w-36">Lokasi Training:</span><span className="font-bold text-slate-800">{selectedProgram.location || '-'}</span></div>
                    {selectedProgram.type === 'Reguler' ? (
                      <>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Batch:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.Batch || '-'}</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Kategori Reguler:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.jenis_program_reguler || '-'}</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Total Hari:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.jumlah_hari || 0} Hari</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Durasi Program:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.jumlah_bulan_training || 0} Bulan</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Total Training Days:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.Total_training_days || 0}</span></div>
                      </>
                    ) : (
                      <>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Kategori Pelatihan:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.kategori_pelatihan || '-'}</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Tipe Training:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.type_training || '-'}</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Total Jam / Hari:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.total_jam || 0} Jam / {selectedProgram.originalData?.total_hari || 0} Hari</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Total Training Days:</span><span className="font-bold text-slate-800">{selectedProgram.originalData?.td_total || 0}</span></div>
                        <div className="flex items-center"><span className="text-slate-500 w-36">Target Batch:</span><span className="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{selectedProgram.originalData?.total_batch ?? 0} Batch</span></div>
                      </>
                    )}
                  </div>
                </div>

                {/* Target & Breakdown Peserta */}
                <div className="bg-slate-50/50 p-5 rounded-xl border border-slate-200">
                  <h3 className="font-bold text-slate-800 flex items-center gap-2 mb-4 text-sm border-b border-slate-100 pb-3">
                    <Users size={16} className="text-green-600" /> Target & Breakdown Peserta
                  </h3>
                  {selectedProgram.type === 'Reguler' ? (
                    <div className="grid grid-cols-2 gap-3 text-center">
                      <div className="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs"><p className="text-[10px] text-slate-500 mb-1">Promosi Mandor</p><p className="font-bold text-slate-800">{selectedProgram.originalData?.Promosi_mandor || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs"><p className="text-[10px] text-slate-500 mb-1">Fresh Graduate</p><p className="font-bold text-slate-800">{selectedProgram.originalData?.Fresh_Graduate || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs col-span-2 text-left px-4 flex justify-between items-center"><span className="text-[11px] text-slate-500 font-medium">Estimasi Uang Saku</span><span className="font-bold text-slate-800">{new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selectedProgram.originalData?.Uang_saku || 0)}</span></div>
                      <div className="bg-green-50/50 border-2 border-green-200 rounded-xl p-3 shadow-2xs col-span-2"><p className="text-[10px] text-green-700 font-extrabold mb-1">Total Target Peserta</p><p className="font-bold text-green-700 text-base">{selectedProgram.participants}</p></div>
                    </div>
                  ) : (
                    <div className="grid grid-cols-4 gap-2 text-center">
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Non Staff</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_non_staf || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Assistant</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_ast || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Askep</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_askep || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Manager</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_mgr || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">GM</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_gm || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Head</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.peserta_head || 0}</p></div>
                      <div className="bg-white border border-slate-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-slate-500 mb-0.5">Staff</p><p className="font-bold text-slate-800 text-sm">{selectedProgram.originalData?.target_staf || 0}</p></div>
                      <div className="bg-green-50/50 border-2 border-green-200 rounded-xl p-2 shadow-2xs"><p className="text-[10px] text-green-700 font-extrabold mb-0.5">Target</p><p className="font-bold text-green-700 text-sm">{selectedProgram.participants}</p></div>
                    </div>
                  )}
                </div>
              </div>

              {/* Kalender Rencana Pelaksanaan */}
              <div>
                <div className="flex items-center justify-between mb-4 mt-2">
                  <h3 className="font-bold text-slate-800 flex items-center gap-2 text-sm">
                    <CalendarIcon size={16} className="text-green-600" /> Kalender Rencana Pelaksanaan
                  </h3>
                  <div className="flex items-center gap-3">
                    <button
                      onClick={handlePrevMonth}
                      className="w-7 h-7 flex items-center justify-center border border-slate-200 rounded-md bg-white hover:bg-slate-50 text-slate-600"
                    >
                      <ChevronLeft size={14} />
                    </button>
                    <span className="text-sm font-bold text-slate-700 w-36 text-center">
                      {MONTH_NAMES_ID[calendarDate.getMonth()]} {calendarDate.getFullYear()}
                    </span>
                    <button
                      onClick={handleNextMonth}
                      className="w-7 h-7 flex items-center justify-center border border-slate-200 rounded-md bg-white hover:bg-slate-50 text-slate-600"
                    >
                      <ChevronRight size={14} />
                    </button>
                  </div>
                </div>

                <div className="border border-slate-200 rounded-xl overflow-hidden">
                  <div className="grid grid-cols-7 bg-slate-50 border-b border-slate-200">
                    {DAY_NAMES_ID.map(day => (
                      <div key={day} className="py-2 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        {day}
                      </div>
                    ))}
                  </div>
                  <div className="grid grid-cols-7">
                    {getCalendarDays(calendarDate).map((cell, idx) => {
                      const active = cell.inMonth && isDateInRange(cell.date);
                      return (
                        <div
                          key={idx}
                          className={`min-h-16 p-2 border-b border-r border-slate-100 flex flex-col items-center justify-start ${(idx + 1) % 7 === 0 ? 'border-r-0' : ''} ${!cell.inMonth ? 'bg-slate-50/40' : 'bg-white'}`}
                        >
                          <span
                            className={`w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold ${
                              active
                                ? 'bg-green-600 text-white'
                                : cell.inMonth ? 'text-slate-700' : 'text-slate-300'
                            }`}
                          >
                            {cell.day}
                          </span>
                          {active && (
                            <span className="mt-1 text-[9px] font-semibold text-green-700 text-center leading-tight">
                              Training
                            </span>
                          )}
                        </div>
                      );
                    })}
                  </div>
                </div>
              </div>

            </div>

            {/* Footer */}
            <div className="p-5 border-t border-slate-100 bg-white flex justify-end">
              <button onClick={() => setIsCalendarModalOpen(false)} className="bg-green-700 hover:bg-green-800 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-2xs">
                Tutup Detail
              </button>
            </div>

          </div>
        </div>
      )}

      {/* Detail Seluruh Program Modal with Monthly Matrix & PDF Export */}
      {isDetailModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-2 sm:p-3">
          <div className="bg-white rounded-2xl shadow-xl w-full h-[98vh] flex flex-col overflow-hidden">
            {/* Modal Header */}
            <div className="p-4 border-b border-slate-100 flex justify-between items-start bg-[#fcfcf9]">
              <div>
                <span className={`inline-flex px-3 py-1 rounded-full text-[11px] font-bold ${
                  activeMainTab === 'reguler' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'
                }`}>
                  {activeMainTab === 'reguler' ? 'Program Reguler' : 'Program Non Reguler'}
                </span>
                <h2 className="text-xl font-extrabold text-slate-800 mt-2 leading-tight">
                  {currentCategories.find(category => category.id === activeCategory)?.title || 'Semua Program'}
                </h2>
                <p className="text-sm text-slate-500 mt-1">
                  Detail Seluruh Program {activeMainTab === 'reguler' ? 'Reguler' : 'Non Reguler'} & Matriks Bulanan
                </p>
              </div>
              <button
                onClick={() => setIsDetailModalOpen(false)}
                className="text-slate-400 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100 transition-colors"
                aria-label="Tutup detail"
              >
                <X size={20} />
              </button>
            </div>

            {/* Modal Body: Table Matrix */}
            <div className="flex-1 overflow-auto px-3 sm:px-4 pb-3 sm:pb-4 pt-0">
              {activeMainTab === 'reguler' ? (
                <table className="w-full table-fixed text-left border-collapse border border-slate-300 text-[12px] leading-tight">
                  <thead>
                    <tr className="text-slate-900 font-bold uppercase tracking-wider text-[11px]">
                      <th className="w-[14%] px-2 py-3 sticky top-0 left-0 bg-green-500 z-30 break-words border-r border-b border-slate-400">Nama Program</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Batch</th>
                      <th className="w-[7%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Kategori</th>
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Region</th>
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Target Batch</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Target Peserta</th>
                      <th className="w-[8%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Lokasi</th>
                      {MONTH_NAMES_ID.map(month => (
                        <th key={month} className="w-[3%] px-0.5 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">
                          {month.slice(0, 3)}
                        </th>
                      ))}
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Jlh Hari</th>
                      <th className="w-[7%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Training Days</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Promosi Mandor</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-b border-slate-400">Fresh Graduate</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredData.map((item, index) => {
                      const monthlyCounts = getMonthlyDayCounts(item.tanggal_pelaksanaan);
                      const jumlahHari = monthlyCounts.reduce((total, count) => total + count, 0);
                      const originalData = item.originalData || {};
                      return (
                        <tr key={item.id || index} className="hover:bg-slate-50 border-b border-slate-300">
                          <td className="px-2 py-3 sticky left-0 bg-white font-bold text-slate-800 break-words border-r border-slate-300 z-10">{item.title}</td>
                          <td className="px-1 py-3 text-center font-medium text-slate-700 break-words border-r border-slate-300">{originalData.Batch ? `Batch ${originalData.Batch}` : '-'}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{originalData.jenis_program_reguler || '-'}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{item.region || 'Nasional'}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{originalData.total_batch ?? (item.batchCount || 1)}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{item.participants || 0}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{item.location || '-'}</td>
                          {monthlyCounts.map((count, monthIndex) => (
                            <td
                              key={monthIndex}
                              className={`px-0.5 py-3 text-center font-bold border-r border-slate-300 ${
                                count > 0 ? 'bg-green-100 text-green-700' : 'text-slate-800'
                              }`}
                            >
                              {count}
                            </td>
                          ))}
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{jumlahHari}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{originalData.Total_training_days || 0}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{originalData.Promosi_mandor || 0}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words">{originalData.Fresh_Graduate || 0}</td>
                        </tr>
                      );
                    })}
                    {/* Total Row */}
                    <tr className="bg-green-100 font-extrabold text-green-900 border-t-2 border-green-600 sticky bottom-0 z-20">
                      <td className="px-2 py-3.5 sticky left-0 bg-green-100 z-30 border-r border-green-300 text-center uppercase">Total</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.batch}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.participants}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      {Array.from({ length: 12 }).map((_, index) => (
                        <td key={`total-month-${index}`} className="px-0.5 py-3.5 text-center border-r border-green-300"></td>
                      ))}
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.trainingDays}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.promosiMandor}</td>
                      <td className="px-1 py-3.5 text-center">{detailTotals.freshGraduate}</td>
                    </tr>
                  </tbody>
                </table>
              ) : (
                <table className="w-full table-fixed text-left border-collapse border border-slate-300 text-[12px] leading-tight">
                  <thead>
                    <tr className="text-slate-900 font-bold uppercase tracking-wider text-[11px]">
                      <th className="w-[14%] px-2 py-3 sticky top-0 left-0 bg-green-500 z-30 break-words border-r border-b border-slate-400">Nama Program</th>
                      <th className="w-[8%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Kategori</th>
                      <th className="w-[7%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Sub Kategori</th>
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Region</th>
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Target Batch</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Target Peserta</th>
                      <th className="w-[7%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Lokasi</th>
                      {MONTH_NAMES_ID.map(month => (
                        <th key={month} className="w-[3%] px-0.5 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">
                          {month.slice(0, 3)}
                        </th>
                      ))}
                      <th className="w-[5%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Jlh Hari</th>
                      <th className="w-[4%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Jam</th>
                      <th className="w-[4%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-r border-b border-slate-400">Hari</th>
                      <th className="w-[6%] px-1 py-3 sticky top-0 bg-green-500 z-20 text-center break-words border-b border-slate-400">TD Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredData.map((item, index) => {
                      const monthlyCounts = getMonthlyDayCounts(item.tanggal_pelaksanaan);
                      const jumlahHari = monthlyCounts.reduce((total, count) => total + count, 0);
                      const originalData = item.originalData || {};
                      return (
                        <tr key={item.id || index} className="hover:bg-slate-50 border-b border-slate-300">
                          <td className="px-2 py-3 sticky left-0 bg-white font-bold text-slate-800 break-words border-r border-slate-300 z-10">{item.title}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{originalData.kategori_pelatihan || '-'}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{item.subCategory || '-'}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{item.region || 'Nasional'}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{originalData.total_batch ?? (item.batchCount || 1)}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{item.participants || 0}</td>
                          <td className="px-1 py-3 text-center text-slate-600 break-words border-r border-slate-300">{item.location || '-'}</td>
                          {monthlyCounts.map((count, monthIndex) => (
                            <td
                              key={monthIndex}
                              className={`px-0.5 py-3 text-center font-bold border-r border-slate-300 ${
                                count > 0 ? 'bg-green-100 text-green-700' : 'text-slate-800'
                              }`}
                            >
                              {count}
                            </td>
                          ))}
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words border-r border-slate-300">{jumlahHari}</td>
                          <td className="px-1 py-3 text-center font-medium text-slate-700 break-words border-r border-slate-300">{originalData.jam_pelajaran || 0}</td>
                          <td className="px-1 py-3 text-center font-medium text-slate-700 break-words border-r border-slate-300">{originalData.hari_pelatihan || 0}</td>
                          <td className="px-1 py-3 text-center font-bold text-slate-800 break-words">{originalData.TD_total || 0}</td>
                        </tr>
                      );
                    })}
                    {/* Total Row */}
                    <tr className="bg-green-100 font-extrabold text-green-900 border-t-2 border-green-600 sticky bottom-0 z-20">
                      <td className="px-2 py-3.5 sticky left-0 bg-green-100 z-30 border-r border-green-300 text-center uppercase">Total</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.batch}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.participants}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      {Array.from({ length: 12 }).map((_, index) => (
                        <td key={`total-month-${index}`} className="px-0.5 py-3.5 text-center border-r border-green-300"></td>
                      ))}
                      <td className="px-1 py-3.5 text-center border-r border-green-300"></td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.jamPelajaran}</td>
                      <td className="px-1 py-3.5 text-center border-r border-green-300">{detailTotals.hariPelatihan}</td>
                      <td className="px-1 py-3.5 text-center">{detailTotals.tdTotal}</td>
                    </tr>
                  </tbody>
                </table>
              )}
            </div>

            {/* Modal Footer */}
            <div className="p-4 sm:p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
              <button
                onClick={exportDetailToPdf}
                className="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-2xs cursor-pointer"
              >
                <Download size={16} />
                Export PDF
              </button>
              <button
                onClick={() => setIsDetailModalOpen(false)}
                className="bg-slate-700 hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-2xs cursor-pointer"
              >
                Tutup Detail
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Import Excel / CSV Modal */}
      {isImportModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in">
          <div className="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden">
            <div className="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
              <div className="flex items-center gap-2.5">
                <div className="w-9 h-9 rounded-xl bg-emerald-50 text-[#4B6A34] flex items-center justify-center">
                  <Upload size={18} />
                </div>
                <div>
                  <h3 className="font-bold text-slate-900 text-sm">Import Data Program & Batch</h3>
                  <p className="text-xs text-slate-500">Mendukung format file Excel (.xlsx) atau CSV (.csv)</p>
                </div>
              </div>
              <button
                type="button"
                onClick={() => setIsImportModalOpen(false)}
                className="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer"
              >
                <X size={14} />
              </button>
            </div>

            <form onSubmit={handleImportSubmit} className="p-6 space-y-4">
              {importMessage && (
                <div className={`p-3.5 rounded-xl border text-xs font-medium flex items-center gap-2 ${
                  importMessage.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600'
                }`}>
                  {importMessage.type === 'success' ? <Check size={16} className="shrink-0" /> : <AlertTriangle size={16} className="shrink-0" />}
                  <span>{importMessage.text}</span>
                </div>
              )}

              {/* Template Download Option */}
              <div className="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div className="flex items-center gap-2.5">
                  <div className="w-8 h-8 rounded-lg bg-emerald-100/70 text-[#4B6A34] flex items-center justify-center shrink-0">
                    <FileSpreadsheet size={17} />
                  </div>
                  <div>
                    <p className="text-xs font-bold text-slate-800">Template Excel Import</p>
                    <p className="text-[11px] text-slate-500">
                      Unduh template resmi agar susunan kolom sesuai.
                    </p>
                  </div>
                </div>
                <div className="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
                  <a
                    href={`/administrasi/program-batch/template?type=${activeMainTab === 'reguler' ? 'reguler' : 'non_reguler'}`}
                    className="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#4B6A34] bg-white hover:bg-emerald-50 border border-emerald-200 rounded-lg transition shadow-2xs cursor-pointer"
                  >
                    <Download size={13} />
                    <span>Template {activeMainTab === 'reguler' ? 'Reguler' : 'Non-Reguler'}</span>
                  </a>
                  <a
                    href={`/administrasi/program-batch/template?type=${activeMainTab === 'reguler' ? 'non_reguler' : 'reguler'}`}
                    className="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-[11px] font-semibold text-slate-500 hover:text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition cursor-pointer"
                    title={`Unduh alternatif template ${activeMainTab === 'reguler' ? 'Non-Reguler' : 'Reguler'}`}
                  >
                    <span>{activeMainTab === 'reguler' ? 'Non-Reguler' : 'Reguler'}</span>
                  </a>
                </div>
              </div>

              {/* File Input */}
              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1.5">Pilih File Excel / CSV <span className="text-red-500">*</span></label>
                <div className="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-[#4B6A34] transition-colors bg-slate-50/50">
                  <FileSpreadsheet size={32} className="text-slate-400 mx-auto mb-2" />
                  <input
                    type="file"
                    accept=".csv,.xlsx,.txt"
                    required
                    onChange={(e) => {
                      setImportFile(e.target.files[0] || null);
                      setImportMessage(null);
                    }}
                    className="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#4B6A34] hover:file:bg-emerald-100 cursor-pointer"
                  />
                  <p className="text-[11px] text-slate-400 mt-2">Maksimal ukuran file: 20MB (.xlsx, .csv)</p>
                </div>
              </div>

              {/* Info Format */}
              <div className="bg-emerald-50/70 border border-emerald-100 rounded-xl p-3.5 flex items-start gap-3">
                <Info size={16} className="text-[#4B6A34] shrink-0 mt-0.5" />
                <div className="text-[11px] text-slate-600 space-y-1">
                  <p className="font-semibold text-[#4B6A34]">Format Program Reguler & Non-Reguler:</p>
                  <p className="text-slate-500">Sistem otomatis mendeteksi kolom program reguler (Batch, Mulai/Selesai Program, Promosi Mandor, Fresh Graduate) maupun non-reguler (Jenis Pelatihan, Total Batch, Trainer).</p>
                </div>
              </div>

              {/* Footer */}
              <div className="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button
                  type="button"
                  onClick={() => setIsImportModalOpen(false)}
                  className="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={importLoading || !importFile}
                  className="px-5 py-2 text-xs font-bold text-white bg-[#4B6A34] rounded-xl hover:bg-[#3d572b] disabled:opacity-50 transition-colors shadow-xs flex items-center gap-1.5 cursor-pointer"
                >
                  {importLoading ? <RefreshCw size={14} className="animate-spin" /> : <Upload size={14} />}
                  <span>{importLoading ? 'Mengimpor...' : 'Mulai Import Data'}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

    </div>
  );
};

export default DataManagement;
