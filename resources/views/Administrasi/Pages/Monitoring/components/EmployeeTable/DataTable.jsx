import { useState, useMemo } from "react";
import { Search, Plus, Edit2 } from "lucide-react";

export function DataTable({ data, statusFilter, onAdd, onEdit }) {
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [filterAngkatan, setFilterAngkatan] = useState("");
  const [filterRegion, setFilterRegion] = useState("");
  const itemsPerPage = 5;

  // Filter Data
  const filteredData = useMemo(() => {
    return data.filter((item) => {
      if (statusFilter && item.status !== statusFilter) return false;
      if (filterAngkatan && item.angkatan !== filterAngkatan) return false;
      if (filterRegion && item.region_terakhir !== filterRegion) return false;
      
      if (search) {
        const searchLower = search.toLowerCase();
        return (
          item.nama.toLowerCase().includes(searchLower) ||
          item.nik.toLowerCase().includes(searchLower)
        );
      }
      return true;
    });
  }, [data, search, statusFilter, filterAngkatan, filterRegion]);

  // Pagination
  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  const currentData = filteredData.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  // Extract unique options for filters
  const uniqueAngkatan = [...new Set(data.map((item) => item.angkatan))];
  const uniqueRegion = [...new Set(data.map((item) => item.region_terakhir))];

  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      {/* Header & Controls */}
      <div className="p-6 border-b border-gray-100 space-y-4">
        <div className="flex flex-col sm:flex-row justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search className="h-5 w-5 text-gray-400" />
            </div>
            <input
              type="text"
              placeholder="Cari NIK atau Nama..."
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
                setCurrentPage(1);
              }}
              className="pl-10 pr-4 py-2 w-full border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2c8f42] focus:border-transparent transition-all"
            />
          </div>
          <button
            onClick={onAdd}
            className="flex items-center gap-2 bg-[#2c8f42] hover:bg-[#237033] text-white px-4 py-2 rounded-lg transition-colors font-medium"
          >
            <Plus size={18} />
            <span>Tambah Data</span>
          </button>
        </div>

        <div className="flex gap-4">
          <select
            value={filterAngkatan}
            onChange={(e) => {
              setFilterAngkatan(e.target.value);
              setCurrentPage(1);
            }}
            className="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42]"
          >
            <option value="">Semua Angkatan</option>
            {uniqueAngkatan.map((a) => (
              <option key={a} value={a}>{a}</option>
            ))}
          </select>
          <select
            value={filterRegion}
            onChange={(e) => {
              setFilterRegion(e.target.value);
              setCurrentPage(1);
            }}
            className="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42]"
          >
            <option value="">Semua Region</option>
            {uniqueRegion.map((r) => (
              <option key={r} value={r}>{r}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm">
              <th className="p-4 font-medium">NIK</th>
              <th className="p-4 font-medium">Nama</th>
              <th className="p-4 font-medium">Jabatan</th>
              <th className="p-4 font-medium">Angkatan</th>
              <th className="p-4 font-medium">Region</th>
              {statusFilter !== "Eksis" && (
                <th className="p-4 font-medium">Tgl Keluar</th>
              )}
              {statusFilter !== "Eksis" && (
                <th className="p-4 font-medium">Alasan</th>
              )}
              <th className="p-4 font-medium text-right">Aksi</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {currentData.length > 0 ? (
              currentData.map((row) => (
                <tr key={row.id} className="hover:bg-gray-50/50 transition-colors">
                  <td className="p-4 text-sm font-medium text-gray-900">{row.nik}</td>
                  <td className="p-4 text-sm text-gray-700">{row.nama}</td>
                  <td className="p-4 text-sm text-gray-600">{row.jabatan}</td>
                  <td className="p-4 text-sm text-gray-600">
                    <span className="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                      {row.angkatan}
                    </span>
                  </td>
                  <td className="p-4 text-sm text-gray-600">{row.region_terakhir}</td>
                  {statusFilter !== "Eksis" && (
                    <td className="p-4 text-sm text-red-600">{row.tanggal_terminate || "-"}</td>
                  )}
                  {statusFilter !== "Eksis" && (
                    <td className="p-4 text-sm text-gray-500 max-w-xs truncate" title={row.alasan_resign}>
                      {row.alasan_resign || "-"}
                    </td>
                  )}
                  <td className="p-4 text-right">
                    <button
                      onClick={() => onEdit(row)}
                      className="text-gray-400 hover:text-[#0a4239] transition-colors p-2 rounded-md hover:bg-gray-100"
                      title="Edit Data"
                    >
                      <Edit2 size={16} />
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={10} className="p-8 text-center text-gray-500">
                  Tidak ada data yang ditemukan.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="p-4 border-t border-gray-100 flex items-center justify-between">
          <p className="text-sm text-gray-500">
            Menampilkan {(currentPage - 1) * itemsPerPage + 1} - {Math.min(currentPage * itemsPerPage, filteredData.length)} dari {filteredData.length} data
          </p>
          <div className="flex gap-1">
            <button
              onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
              disabled={currentPage === 1}
              className="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Prev
            </button>
            <button
              onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
              disabled={currentPage === totalPages}
              className="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
