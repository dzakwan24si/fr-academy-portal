import { X, User, Briefcase, MapPin, AlertCircle } from "lucide-react";

export function EmployeeDetailModal({ employee, onClose }) {
  if (!employee) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200">
      <div className="bg-white rounded-[2rem] shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-in slide-in-from-bottom-4 duration-300">
        
        {/* Header */}
        <div className="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <div className="flex items-center gap-4">
            <div className="h-12 w-12 bg-[#eaf4ec] text-[#2c8f42] rounded-full flex items-center justify-center font-bold text-xl">
              {employee.NAMA ? employee.NAMA.charAt(0) : "?"}
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-800">{employee.NAMA}</h2>
              <p className="text-sm text-gray-500 font-medium">{employee.NIK} • {employee.JABATAN}</p>
            </div>
          </div>
          <button 
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-gray-700 transition-colors"
          >
            <X size={24} />
          </button>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {/* Informasi Pribadi */}
            <div className="space-y-4">
              <h3 className="text-sm font-bold text-[#2c8f42] uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-2">
                <User size={16} /> Data Pribadi
              </h3>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-xs text-gray-400 mb-1">Suku</p>
                  <p className="text-sm font-medium text-gray-800">{employee.SUKU || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Agama</p>
                  <p className="text-sm font-medium text-gray-800">{employee.AGAMA || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Alumni</p>
                  <p className="text-sm font-medium text-gray-800">{employee.ALUMNI || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Kategori</p>
                  <p className="text-sm font-medium text-gray-800">{employee.KATEGORI || "-"}</p>
                </div>
              </div>
            </div>

            {/* Riwayat Pekerjaan */}
            <div className="space-y-4">
              <h3 className="text-sm font-bold text-[#2c8f42] uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-2">
                <Briefcase size={16} /> Riwayat Pekerjaan
              </h3>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-xs text-gray-400 mb-1">Join Date</p>
                  <p className="text-sm font-medium text-gray-800">{employee["JOIN DATE"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Level Jabatan</p>
                  <p className="text-sm font-medium text-gray-800">{employee["LEVEL JABATAN"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Angkatan FR Academy</p>
                  <p className="text-sm font-medium text-gray-800">{employee["ANGKATAN FR ACADEMY"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Lama Bekerja</p>
                  <p className="text-sm font-medium text-gray-800">{employee["LAMA BEKERJA"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Nama GM</p>
                  <p className="text-sm font-medium text-gray-800">{employee.GM || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Nama MK</p>
                  <p className="text-sm font-medium text-gray-800">{employee.MK || "-"}</p>
                </div>
              </div>
            </div>

            {/* Riwayat Penempatan */}
            <div className="space-y-4">
              <h3 className="text-sm font-bold text-[#2c8f42] uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-2">
                <MapPin size={16} /> Penempatan
              </h3>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-xs text-gray-400 mb-1">Lokasi Awal</p>
                  <p className="text-sm font-medium text-gray-800">{employee["LOKASI AWAL PENEMPATAN"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Region Awal</p>
                  <p className="text-sm font-medium text-gray-800">{employee["REGION AWAL PENEMPATAN"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Lokasi Terakhir</p>
                  <p className="text-sm font-medium text-gray-800">{employee["LOKASI TERAKHIR"] || "-"}</p>
                </div>
                <div>
                  <p className="text-xs text-gray-400 mb-1">Region Terakhir</p>
                  <p className="text-sm font-medium text-gray-800">{employee["REGION TERAKHIR"] || "-"}</p>
                </div>
              </div>
            </div>

            {/* Informasi Terminasi */}
            {(employee.STATUS === "Terminate" || employee.STATUS === "Culled" || employee.STATUS === "Resign") && (
              <div className="space-y-4 md:col-span-2 bg-red-50/50 p-6 rounded-2xl border border-red-100 mt-4">
                <h3 className="text-sm font-bold text-red-600 uppercase tracking-wider flex items-center gap-2 border-b border-red-200 pb-2">
                  <AlertCircle size={16} /> Data Terminasi / Culled
                </h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                  <div>
                    <p className="text-xs text-red-400 mb-1">Status</p>
                    <p className="text-sm font-bold text-red-700">{employee.STATUS || "-"}</p>
                  </div>
                  <div>
                    <p className="text-xs text-red-400 mb-1">Tanggal Keluar</p>
                    <p className="text-sm font-medium text-gray-800">{employee["DATE TERMINATE"] || "-"}</p>
                  </div>
                  <div className="md:col-span-2">
                    <p className="text-xs text-red-400 mb-1">Alasan</p>
                    <p className="text-sm font-medium text-gray-800">{employee["Alasan Resign"] || "-"}</p>
                  </div>
                  <div className="md:col-span-4 mt-2">
                    <p className="text-xs text-red-400 mb-1">Detail Tambahan</p>
                    <p className="text-sm font-medium text-gray-700 bg-white p-3 rounded-xl border border-red-100">
                      {employee.Detail || "-"}
                    </p>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
