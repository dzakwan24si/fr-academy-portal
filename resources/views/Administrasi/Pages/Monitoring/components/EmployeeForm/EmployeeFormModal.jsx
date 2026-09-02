import { useState, useEffect } from "react";
import { X } from "lucide-react";

export function EmployeeFormModal({ isOpen, onClose, initialData, onSubmit }) {
  const [formData, setFormData] = useState({
    nik: "",
    nama: "",
    join_date: "",
    jabatan: "",
    angkatan: "",
    status: "Eksis",
    lokasi_awal: "",
    region_awal: "Riau",
    lokasi_terakhir: "",
    region_terakhir: "Riau",
    tanggal_terminate: "",
    alasan_resign: "",
  });

  useEffect(() => {
    if (initialData) {
      setFormData(initialData);
    } else {
      setFormData({
        nik: "",
        nama: "",
        join_date: "",
        jabatan: "",
        angkatan: "",
        status: "Eksis",
        lokasi_awal: "",
        region_awal: "Riau",
        lokasi_terakhir: "",
        region_terakhir: "Riau",
        tanggal_terminate: "",
        alasan_resign: "",
      });
    }
  }, [initialData, isOpen]);

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Simulate calculating work duration (lama_bekerja_bulan)
    const joinDate = new Date(formData.join_date);
    const endDate = formData.tanggal_terminate ? new Date(formData.tanggal_terminate) : new Date();
    const diffTime = Math.abs(endDate - joinDate);
    const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
    
    const payload = {
      ...formData,
      id: initialData?.id || Date.now().toString(),
      lama_bekerja_bulan: diffMonths,
    };
    
    console.log("Submitting Data:", payload);
    onSubmit(payload);
    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div className="flex justify-between items-center p-6 border-b">
          <h2 className="text-xl font-bold text-gray-800">
            {initialData ? "Edit Data Karyawan" : "Tambah Data Karyawan"}
          </h2>
          <button onClick={onClose} className="text-gray-500 hover:text-gray-800 transition-colors">
            <X size={24} />
          </button>
        </div>
        
        <div className="p-6 overflow-y-auto flex-1">
          <form id="employeeForm" onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">NIK</label>
                <input required type="text" name="nik" value={formData.nik} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Nama</label>
                <input required type="text" name="nama" value={formData.nama} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Tanggal Join</label>
                <input required type="date" name="join_date" value={formData.join_date} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Jabatan</label>
                <input required type="text" name="jabatan" value={formData.jabatan} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Angkatan</label>
                <input required type="text" name="angkatan" value={formData.angkatan} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Status</label>
                <select name="status" value={formData.status} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none bg-white">
                  <option value="Eksis">Eksis</option>
                  <option value="Terminate">Terminate</option>
                  <option value="Culled">Culled</option>
                </select>
              </div>
            </div>

            <h3 className="font-semibold text-gray-800 pt-4 border-t">Penempatan</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Lokasi Awal</label>
                <input required type="text" name="lokasi_awal" value={formData.lokasi_awal} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Region Awal</label>
                <select name="region_awal" value={formData.region_awal} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none bg-white">
                  <option value="Riau">Riau</option>
                  <option value="Kalbar">Kalbar</option>
                  <option value="Kubar">Kubar</option>
                </select>
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Lokasi Terakhir</label>
                <input required type="text" name="lokasi_terakhir" value={formData.lokasi_terakhir} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
              </div>
              <div className="space-y-1">
                <label className="text-sm font-medium text-gray-700">Region Terakhir</label>
                <select name="region_terakhir" value={formData.region_terakhir} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none bg-white">
                  <option value="Riau">Riau</option>
                  <option value="Kalbar">Kalbar</option>
                  <option value="Kubar">Kubar</option>
                </select>
              </div>
            </div>

            {formData.status !== "Eksis" && (
              <>
                <h3 className="font-semibold text-gray-800 pt-4 border-t">Data Keluar</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-1">
                    <label className="text-sm font-medium text-gray-700">Tanggal Keluar</label>
                    <input type="date" name="tanggal_terminate" value={formData.tanggal_terminate || ""} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
                  </div>
                  <div className="space-y-1">
                    <label className="text-sm font-medium text-gray-700">Alasan</label>
                    <input type="text" name="alasan_resign" value={formData.alasan_resign || ""} onChange={handleChange} className="w-full border p-2 rounded-md focus:ring-2 focus:ring-[#2c8f42] outline-none" />
                  </div>
                </div>
              </>
            )}
          </form>
        </div>
        
        <div className="p-6 border-t bg-gray-50 rounded-b-xl flex justify-end gap-3">
          <button onClick={onClose} className="px-4 py-2 text-gray-600 bg-white border rounded-md hover:bg-gray-100 transition-colors">
            Batal
          </button>
          <button type="submit" form="employeeForm" className="px-4 py-2 bg-[#2c8f42] text-white rounded-md hover:bg-[#237033] transition-colors font-medium">
            Simpan
          </button>
        </div>
      </div>
    </div>
  );
}
