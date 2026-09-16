import { useState, useEffect } from "react";
import { X, Save, Loader2 } from "lucide-react";

const initialFormState = {
  nama: "",
  id_angkatan: "",
  kategori_status: "Culled",
  Jenis_culled: "",
  alasan: "",
  fase_program: ""
};

export function CulledFormModal({ isOpen, onClose, onSubmit, initialData, angkatanList = [] }) {
  const [formData, setFormData] = useState(initialFormState);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (initialData) {
      const storedJenis = initialData.Jenis_culled || initialData.jenis_culled || initialData.jenisCulling || "";
      let fase = "";
      const lowerJenis = String(storedJenis || "").toLowerCase();
      if (lowerJenis.includes("ojt")) fase = "OJT";
      else if (lowerJenis.includes("in_class") || lowerJenis.includes("in class") || lowerJenis.includes("in-class")) fase = "In Class";

      if (!fase) {
        const alasanLower = (initialData.alasan || "").toLowerCase();
        if (alasanLower.includes("in class")) fase = "In Class";
        else if (alasanLower.includes("ojt")) fase = "OJT";
      }

      setFormData({
        ...initialData,
        Jenis_culled: storedJenis || (fase === "OJT" ? "Resign_OJT" : fase === "In Class" ? "Resign_In_Class" : ""),
        fase_program: fase
      });
    } else {
      setFormData(initialFormState);
    }
  }, [initialData, isOpen]);

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value } = e.target;
    // id_angkatan is an integer in the database
    let finalValue = value;
    if (name === "id_angkatan") {
      finalValue = value === "" ? "" : Number(value);
    }
    setFormData((prev) => ({ ...prev, [name]: finalValue }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      // Validate
      if (!formData.nama || !formData.id_angkatan || !formData.kategori_status) {
        throw new Error("Nama, Angkatan, dan Kategori Status wajib diisi!");
      }

      const finalJenisCulled = formData.Jenis_culled ||
        (formData.fase_program === "OJT" ? "Resign_OJT" : "Resign_In_Class");

      await onSubmit({
        ...formData,
        Jenis_culled: finalJenisCulled,
        fase_program: finalJenisCulled === "Resign_OJT" ? "OJT" : "In Class"
      });
      onClose();
    } catch (error) {
      console.error("Error submitting form:", error);
      alert("Terjadi kesalahan: " + error.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200">
      <div className="bg-white rounded-[2rem] shadow-xl w-full max-w-xl flex flex-col overflow-hidden animate-in slide-in-from-bottom-4 duration-300">
        
        {/* Header */}
        <div className="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
          <div>
            <h2 className="text-xl font-bold text-gray-800">
              {initialData ? "Edit Data Peserta Culled" : "Tambah Data Peserta Culled"}
            </h2>
            <p className="text-sm text-gray-500 font-medium">Lengkapi informasi peserta pelatihan yang dikeluarkan</p>
          </div>
          <button 
            type="button"
            onClick={onClose}
            className="p-2 hover:bg-gray-200 rounded-full text-gray-500 transition-colors"
          >
            <X size={24} />
          </button>
        </div>

        {/* Form Content */}
        <form onSubmit={handleSubmit} className="p-8 space-y-6">
          
          <div>
            <label className="block text-xs font-semibold text-gray-600 mb-1">
              Nama Lengkap <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="nama"
              value={formData.nama || ""}
              onChange={handleChange}
              required
              className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] focus:border-transparent outline-none transition-all"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-gray-600 mb-1">
              Angkatan <span className="text-red-500">*</span>
            </label>
            <div className="flex items-center gap-2">
              <select
                name="id_angkatan"
                value={formData.id_angkatan || ""}
                onChange={handleChange}
                required
                className="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
              >
                <option value="">-- Pilih Angkatan --</option>
                {angkatanList.map((item) => (
                  <option key={item.id_angkatan} value={item.id_angkatan}>
                    {item.angkatan}
                  </option>
                ))}
              </select>
              <button
                type="button"
                onClick={() => {
                  window.dispatchEvent(new CustomEvent('openAngkatanModal'));
                }}
                className="px-3 py-2 bg-[#eaf4ec] text-[#2c8f42] rounded-lg text-sm font-bold hover:bg-[#d5ecd9] transition-colors border border-[#cbe5d2]"
                title="Tambah Angkatan Baru"
              >
                + Baru
              </button>
            </div>
            <p className="text-[10px] text-gray-400 mt-1">*Jika angkatan tidak ada, klik tombol "+ Baru" untuk menambahkannya.</p>
          </div>

          <div>
            <label className="block text-xs font-semibold text-gray-600 mb-1">
              Kategori Status <span className="text-red-500">*</span>
            </label>
            <select
              name="kategori_status"
              value={formData.kategori_status || ""}
              onChange={handleChange}
              required
              className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
            >
              <option value="Culled">Culled (Kasus / Sakit / Dikeluarkan / Mengundurkan Diri)</option>
              <option value="Tidak Lulus">Tidak Lulus (Gagal Evaluasi)</option>
            </select>
          </div>

          {formData.kategori_status === "Culled" && (
            <div>
              <label className="block text-xs font-semibold text-gray-600 mb-1">
                Jenis Culling <span className="text-red-500">*</span>
              </label>
              <select
                name="Jenis_culled"
                value={formData.Jenis_culled || ""}
                onChange={handleChange}
                required
                className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
              >
                <option value="">-- Pilih Jenis Culling --</option>
                <option value="Resign_In_Class">Resign In Class</option>
                <option value="Resign_OJT">Resign OJT</option>
              </select>
            </div>
          )}

          <div>
            <label className="block text-xs font-semibold text-gray-600 mb-1">
              Alasan Detail
            </label>
            <input
              list="alasan-culled-options"
              name="alasan"
              value={formData.alasan || ""}
              onChange={handleChange}
              placeholder="Pilih dari daftar atau ketik alasan baru..."
              className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] focus:border-transparent outline-none transition-all"
            />
            <datalist id="alasan-culled-options">
              <option value="Tidak Lulus Evaluasi" />
              <option value="Dikeluarkan karena Sakit" />
              <option value="Resign karena Merasa Tidak Cocok" />
              <option value="Resign karena Kabur saat OJT" />
              <option value="Diterima di perusahaan lain" />
              <option value="Keluar saat menunggu alokasi kelulusan" />
            </datalist>
          </div>

          {/* Form Actions */}
          <div className="pt-6 border-t border-gray-100 flex justify-end gap-3 mt-4">
            <button
              type="button"
              onClick={onClose}
              disabled={isSubmitting}
              className="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#2c8f42] hover:bg-[#237535] transition-colors flex items-center gap-2"
            >
              {isSubmitting ? <Loader2 size={18} className="animate-spin" /> : <Save size={18} />}
              {isSubmitting ? "Menyimpan..." : "Simpan Data"}
            </button>
          </div>
        </form>

      </div>
    </div>
  );
}
