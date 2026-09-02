import { useState } from "react";
import { useEmployees } from "../hooks/useEmployees";
import { Search, Database, ChevronLeft, ChevronRight, Plus, Edit2, Trash2 } from "lucide-react";
import { EmployeeFormModal } from "../components/DataManagement/EmployeeFormModal";

export default function DatabaseData() {
  const { data, loading, error, addEmployee, updateEmployee, deleteEmployee } = useEmployees();
  
  // Table States
  const [searchTerm, setSearchTerm] = useState("");
  const [angkatanFilter, setAngkatanFilter] = useState("Semua");
  const [regionFilter, setRegionFilter] = useState("Semua");
  const [statusFilter, setStatusFilter] = useState("Semua");
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [currentPage, setCurrentPage] = useState(1);

  // Modal States
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingEmployee, setEditingEmployee] = useState(null);

  const handleAdd = () => {
    setEditingEmployee(null);
    setIsFormOpen(true);
  };

  const handleEdit = (employee) => {
    setEditingEmployee(employee);
    setIsFormOpen(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm("Apakah Anda yakin ingin menghapus data karyawan ini? Data yang dihapus tidak dapat dikembalikan.")) {
      try {
        await deleteEmployee(id);
      } catch (err) {
        alert("Gagal menghapus data: " + err.message);
      }
    }
  };

  const handleSubmitForm = async (formData) => {
    if (editingEmployee) {
      await updateEmployee(editingEmployee.ID_MONITORING, formData);
    } else {
      await addEmployee(formData);
    }
  };

  if (loading) {
    return <div className="p-8 text-center text-gray-500">Loading data...</div>;
  }

  if (error) {
    return <div className="p-8 text-center text-red-500">Error: {error}</div>;
  }

  // Get unique options
  const angkatanOptions = Array.from(new Set(
    data.map(emp => (emp["ANGKATAN FR ACADEMY"] || "").trim()).filter(Boolean)
  )).sort();
  
  const regionOptions = Array.from(new Set(
    data.map(emp => (emp["REGION TERAKHIR"] || "").trim()).filter(Boolean)
  )).sort();
  
  const statusOptions = Array.from(new Set(
    data.map(emp => (emp["STATUS"] || "").trim()).filter(Boolean)
  )).sort();

  // Filter Data
  const filteredData = data.filter(item => {
    const searchLower = searchTerm.toLowerCase();
    const matchesSearch = (
      (item.NAMA?.toLowerCase() || "").includes(searchLower) ||
      (item.NIK?.toLowerCase() || "").includes(searchLower) ||
      (item["PT TERAKHIR"]?.toLowerCase() || "").includes(searchLower) ||
      (item.STATUS?.toLowerCase() || "").includes(searchLower) ||
      (item.KATEGORI?.toLowerCase() || "").includes(searchLower)
    );
    
    const matchesAngkatan = angkatanFilter === "Semua" ? true : (item["ANGKATAN FR ACADEMY"] || "").trim() === angkatanFilter;
    const matchesRegion = regionFilter === "Semua" ? true : (item["REGION TERAKHIR"] || "").trim() === regionFilter;
    const matchesStatus = statusFilter === "Semua" ? true : (item["STATUS"] || "").trim() === statusFilter;

    return matchesSearch && matchesAngkatan && matchesRegion && matchesStatus;
  });

  // Pagination Logic
  const totalPages = Math.ceil(filteredData.length / rowsPerPage);
  const startIndex = (currentPage - 1) * rowsPerPage;
  const paginatedData = filteredData.slice(startIndex, startIndex + rowsPerPage);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= totalPages) {
      setCurrentPage(newPage);
    }
  };

  return (
    <div className="min-h-full h-auto bg-white rounded-[2rem] shadow-sm flex flex-col border border-gray-100/50">
      
      {/* Table Header & Controls */}
      <div className="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-bold text-gray-800 flex items-center gap-2">
            <Database size={24} className="text-[#2c8f42]" />
            Master Data Karyawan
          </h2>
          <p className="text-sm text-gray-500 mt-1">
            Menampilkan seluruh {data.length} data karyawan dari database mentah.
          </p>
        </div>
        <div className="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 w-full md:w-auto">
          {/* Angkatan Filter */}
          <select
            value={angkatanFilter}
            onChange={(e) => {
              setAngkatanFilter(e.target.value);
              setCurrentPage(1);
            }}
            className="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all cursor-pointer font-medium text-gray-600 w-full sm:w-auto"
          >
            <option value="Semua">Semua Angkatan</option>
            {angkatanOptions.map((opt) => (
              <option key={opt} value={opt}>{opt}</option>
            ))}
          </select>

          {/* Region Filter */}
          <select
            value={regionFilter}
            onChange={(e) => {
              setRegionFilter(e.target.value);
              setCurrentPage(1);
            }}
            className="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all cursor-pointer font-medium text-gray-600 w-full sm:w-auto"
          >
            <option value="Semua">Semua Region</option>
            {regionOptions.map((opt) => (
              <option key={opt} value={opt}>{opt}</option>
            ))}
          </select>

          {/* Status Filter */}
          <select
            value={statusFilter}
            onChange={(e) => {
              setStatusFilter(e.target.value);
              setCurrentPage(1);
            }}
            className="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all cursor-pointer font-medium text-gray-600 w-full sm:w-auto"
          >
            <option value="Semua">Semua Status</option>
            {statusOptions.map((opt) => (
              <option key={opt} value={opt}>{opt}</option>
            ))}
          </select>

          {/* Search */}
          <div className="relative w-full sm:w-auto">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={16} />
            <input
              type="text"
              placeholder="Cari Nama, NRP, PT, Status..."
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                setCurrentPage(1);
              }}
              className="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none w-full sm:w-64 transition-all"
            />
          </div>

          {/* Add Data Button */}
          <button
            onClick={handleAdd}
            className="flex items-center justify-center gap-2 px-4 py-2 bg-[#2c8f42] text-white rounded-xl text-sm font-bold hover:bg-[#237535] transition-colors shadow-md shadow-[#2c8f42]/20 whitespace-nowrap w-full sm:w-auto"
          >
            <Plus size={16} strokeWidth={3} />
            Tambah Data
          </button>
        </div>
      </div>

      {/* Table Content */}
      <div className="flex-1 overflow-auto min-h-[400px]">
        <table className="w-full text-left border-collapse whitespace-nowrap">
          <thead>
            <tr className="bg-gray-50/80 text-gray-500 text-[11px] font-bold uppercase tracking-wider sticky top-0 z-10 backdrop-blur-sm">
              <th className="px-6 py-4 border-b border-gray-100">NIK</th>
              <th className="px-6 py-4 border-b border-gray-100">NAMA</th>
              <th className="px-6 py-4 border-b border-gray-100">JOIN DATE</th>
              <th className="px-6 py-4 border-b border-gray-100">JABATAN</th>
              <th className="px-6 py-4 border-b border-gray-100">LEVEL JABATAN</th>
              <th className="px-6 py-4 border-b border-gray-100">ANGKATAN FR ACADEMY</th>
              <th className="px-6 py-4 border-b border-gray-100">KATEGORI</th>
              <th className="px-6 py-4 border-b border-gray-100">ALUMNI</th>
              <th className="px-6 py-4 border-b border-gray-100">SUKU</th>
              <th className="px-6 py-4 border-b border-gray-100">AGAMA</th>
              <th className="px-6 py-4 border-b border-gray-100">LOKASI TERAKHIR</th>
              <th className="px-6 py-4 border-b border-gray-100">REGION TERAKHIR</th>
              <th className="px-6 py-4 border-b border-gray-100">STATUS</th>
              <th className="px-6 py-4 border-b border-gray-100">DATE TERMINATE</th>
              <th className="px-6 py-4 border-b border-gray-100">BULAN TERMINATE</th>
              <th className="px-6 py-4 border-b border-gray-100">TAHUN TERMINATE</th>
              <th className="px-6 py-4 border-b border-gray-100">LAMA BEKERJA (BULAN)</th>
              <th className="px-6 py-4 border-b border-gray-100">LAMA BEKERJA (TAHUN)</th>
              <th className="px-6 py-4 border-b border-gray-100">LAMA BEKERJA</th>
              <th className="px-6 py-4 border-b border-gray-100">Alasan Resign</th>
              <th className="px-6 py-4 border-b border-gray-100">Detail</th>
              <th className="px-6 py-4 border-b border-gray-100">LOKASI AWAL PENEMPATAN</th>
              <th className="px-6 py-4 border-b border-gray-100">REGION AWAL PENEMPATAN</th>
              <th className="px-6 py-4 border-b border-gray-100">GM</th>
              <th className="px-6 py-4 border-b border-gray-100">MK</th>
              <th className="px-6 py-4 border-b border-gray-100 sticky right-0 bg-gray-50/90 z-20 shadow-[-10px_0_15px_-5px_rgba(0,0,0,0.05)] text-center">AKSI</th>
            </tr>
          </thead>
          <tbody className="text-sm">
            {paginatedData.length > 0 ? (
              paginatedData.map((item, index) => (
                <tr key={index} className="border-b border-gray-50 hover:bg-[#f0f9f3]/40 transition-colors group">
                  <td className="px-6 py-4 text-gray-600 font-medium">{item.NIK || "-"}</td>
                  <td className="px-6 py-4 text-gray-800 font-semibold">{item.NAMA || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["JOIN DATE"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.JABATAN || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LEVEL JABATAN"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["ANGKATAN FR ACADEMY"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.KATEGORI || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.ALUMNI || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.SUKU || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.AGAMA || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LOKASI TERAKHIR"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["REGION TERAKHIR"] || "-"}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2.5 py-1 text-xs font-bold rounded-full ${
                      item.STATUS?.toLowerCase() === 'aktif' ? 'bg-[#eaf4ec] text-[#2c8f42]' :
                      item.STATUS?.toLowerCase() === 'terminate' ? 'bg-[#fdebe9] text-[#e35649]' :
                      item.STATUS?.toLowerCase() === 'resign' ? 'bg-[#fff4e5] text-[#f29339]' :
                      item.STATUS?.toLowerCase() === 'culled' ? 'bg-red-100 text-red-700' :
                      'bg-gray-100 text-gray-600'
                    }`}>
                      {item.STATUS || "-"}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-gray-600">{item["DATE TERMINATE"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["BULAN TERMINATE"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["TAHUN TERMINATE"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LAMA BEKERJA (BULAN)"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LAMA BEKERJA (TAHUN)"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LAMA BEKERJA"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["Alasan Resign"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.Detail || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["LOKASI AWAL PENEMPATAN"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item["REGION AWAL PENEMPATAN"] || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.GM || "-"}</td>
                  <td className="px-6 py-4 text-gray-600">{item.MK || "-"}</td>
                  <td className="px-6 py-4 sticky right-0 bg-white group-hover:bg-[#f0f9f3] transition-colors shadow-[-10px_0_15px_-5px_rgba(0,0,0,0.05)]">
                    <div className="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button 
                        onClick={() => handleEdit(item)}
                        className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Edit Data"
                      >
                        <Edit2 size={16} />
                      </button>
                      <button 
                        onClick={() => handleDelete(item.ID_MONITORING)}
                        className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Hapus Data"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="26" className="px-6 py-12 text-center text-gray-400 bg-gray-50/30">
                  <div className="flex flex-col items-center justify-center gap-2">
                    <Database size={32} className="opacity-20" />
                    <p>Tidak ada data yang ditemukan.</p>
                  </div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination Footer */}
      <div className="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
        <div className="flex items-center gap-4 text-xs text-gray-500 font-medium">
          <div className="flex items-center gap-2">
            <span>Tampilkan</span>
            <select
              value={rowsPerPage}
              onChange={(e) => {
                setRowsPerPage(Number(e.target.value));
                setCurrentPage(1);
              }}
              className="px-2 py-1 bg-white border border-gray-200 rounded-md outline-none focus:ring-1 focus:ring-[#2c8f42]"
            >
              <option value={10}>10</option>
              <option value={30}>30</option>
              <option value={50}>50</option>
              <option value={100}>100</option>
              <option value={150}>150</option>
            </select>
            <span>data per halaman</span>
          </div>
          <div className="hidden sm:block w-px h-4 bg-gray-300"></div>
          <div>
            Menampilkan <span className="font-bold text-gray-700">{filteredData.length > 0 ? startIndex + 1 : 0}</span> - <span className="font-bold text-gray-700">{Math.min(startIndex + rowsPerPage, filteredData.length)}</span> dari <span className="font-bold text-gray-700">{filteredData.length}</span> data
          </div>
        </div>
        
        <div className="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
          <button
            onClick={() => handlePageChange(currentPage - 1)}
            disabled={currentPage === 1}
            className="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30 disabled:hover:bg-transparent transition-colors"
          >
            <ChevronLeft size={16} strokeWidth={2.5} />
          </button>
          
          <div className="px-3 text-xs font-bold text-gray-700 min-w-[3rem] text-center">
            {currentPage} / {totalPages || 1}
          </div>
          
          <button
            onClick={() => handlePageChange(currentPage + 1)}
            disabled={currentPage === totalPages || totalPages === 0}
            className="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30 disabled:hover:bg-transparent transition-colors"
          >
            <ChevronRight size={16} strokeWidth={2.5} />
          </button>
        </div>
      </div>

      {/* Form Modal */}
      <EmployeeFormModal
        isOpen={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        onSubmit={handleSubmitForm}
        initialData={editingEmployee}
      />
    </div>
  );
}
