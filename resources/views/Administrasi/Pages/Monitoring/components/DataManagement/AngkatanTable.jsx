import { useState } from "react";
import { Search, Edit2, Trash2, Plus, ChevronLeft, ChevronRight, Users, GraduationCap, UserX, UserMinus } from "lucide-react";

export function AngkatanTable({ 
  data, 
  title, 
  description, 
  onAdd, 
  onEdit, 
  onDelete, 
  loading = false 
}) {
  const [searchTerm, setSearchTerm] = useState("");
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [currentPage, setCurrentPage] = useState(1);

  const filteredData = data.filter(item => 
    (item.angkatan?.toLowerCase() || "").includes(searchTerm.toLowerCase())
  );

  const totalPages = Math.ceil(filteredData.length / rowsPerPage);
  const startIndex = (currentPage - 1) * rowsPerPage;
  const paginatedData = filteredData.slice(startIndex, startIndex + rowsPerPage);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= totalPages) {
      setCurrentPage(newPage);
    }
  };

  return (
    <div className="bg-white rounded-[2rem] shadow-sm flex flex-col h-full border border-gray-100/50">
      
      <div className="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-bold text-gray-800">{title}</h2>
          <p className="text-sm text-gray-500 mt-1">{description}</p>
        </div>
        <div className="flex flex-wrap items-center gap-3">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4" />
            <input 
              type="text" 
              placeholder="Cari Angkatan..." 
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                setCurrentPage(1);
              }}
              className="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none w-full md:w-64 transition-all"
            />
          </div>
          
          <button 
            onClick={onAdd}
            className="px-4 py-2 bg-[#2c8f42] text-white rounded-xl hover:bg-[#237535] transition-colors flex items-center gap-2 text-sm font-bold shadow-sm"
          >
            <Plus size={18} /> Tambah Data
          </button>
        </div>
      </div>

      <div className="overflow-x-auto flex-1 min-h-[400px]">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="bg-gray-50/50 border-b border-gray-100">
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Angkatan</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Jumlah Awal</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Lulus</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Resign In Class</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Resign OJT</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tidak Lulus</th>
              <th className="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {loading ? (
              <tr>
                <td colSpan="8" className="py-12 text-center text-gray-500">
                  <div className="flex flex-col items-center justify-center">
                    <div className="w-8 h-8 border-4 border-[#2c8f42] border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p>Memuat data dan mengkalkulasi jumlah...</p>
                  </div>
                </td>
              </tr>
            ) : paginatedData.length > 0 ? (
              paginatedData.map((item, index) => (
                <tr key={item.id_angkatan} className="hover:bg-gray-50/50 transition-colors group">
                  <td className="py-4 px-6 text-sm font-medium text-gray-500">{startIndex + index + 1}</td>
                  <td className="py-4 px-6">
                    <p className="text-sm font-bold text-gray-800">{item.angkatan}</p>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold">
                      <Users size={14} className="text-gray-500" />
                      {item['Jumlah Awal']}
                    </div>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-lg text-sm font-bold border border-green-100">
                      <GraduationCap size={14} className="text-green-600" />
                      {item.lulus_calculated}
                    </div>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-lg text-sm font-bold border border-red-100">
                      <UserX size={14} className="text-red-600" />
                      {item.resign_in_class_calculated ?? 0}
                    </div>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-lg text-sm font-bold border border-red-100">
                      <UserX size={14} className="text-red-600" />
                      {item.resign_ojt_calculated ?? 0}
                    </div>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 rounded-lg text-sm font-bold border border-orange-100">
                      <UserMinus size={14} className="text-orange-600" />
                      {item.tidak_lulus_calculated ?? 0}
                    </div>
                  </td>
                  <td className="py-4 px-6 text-center">
                    <div className="flex items-center justify-center gap-2">
                      <button 
                        onClick={() => onEdit(item)}
                        className="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors"
                        title="Edit Data"
                      >
                        <Edit2 size={16} />
                      </button>
                      <button 
                        onClick={() => {
                          if (window.confirm(`Yakin ingin menghapus Angkatan ${item.angkatan}?\nPeringatan: Ini dapat merusak relasi data jika angkatan ini masih digunakan.`)) {
                            onDelete(item.id_angkatan);
                          }
                        }}
                        className="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors"
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
                <td colSpan="8" className="py-12 text-center text-gray-500">
                  <div className="flex flex-col items-center justify-center">
                    <Search className="h-8 w-8 text-gray-300 mb-3" />
                    <p>Tidak ada data angkatan ditemukan.</p>
                  </div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {!loading && (
        <div className="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
          <div className="flex items-center gap-2">
            <span>Tampilkan</span>
            <select 
              value={rowsPerPage} 
              onChange={(e) => {
                setRowsPerPage(Number(e.target.value));
                setCurrentPage(1);
              }}
              className="bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 outline-none focus:border-[#2c8f42]"
            >
              <option value={5}>5</option>
              <option value={10}>10</option>
              <option value={20}>20</option>
              <option value={50}>50</option>
            </select>
            <span>data per halaman</span>
          </div>

          <div className="flex items-center gap-4">
            <span className="hidden sm:inline">
              Menampilkan {filteredData.length === 0 ? 0 : startIndex + 1} - {Math.min(startIndex + rowsPerPage, filteredData.length)} dari {filteredData.length} data
            </span>
            
            <div className="flex items-center gap-1">
              <button 
                onClick={() => handlePageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className="p-1 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ChevronLeft size={20} />
              </button>
              
              <div className="px-3 py-1 bg-gray-100 rounded-md font-bold text-gray-800">
                {currentPage} <span className="text-gray-400 font-normal">/ {totalPages || 1}</span>
              </div>
              
              <button 
                onClick={() => handlePageChange(currentPage + 1)}
                disabled={currentPage === totalPages || totalPages === 0}
                className="p-1 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ChevronRight size={20} />
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
