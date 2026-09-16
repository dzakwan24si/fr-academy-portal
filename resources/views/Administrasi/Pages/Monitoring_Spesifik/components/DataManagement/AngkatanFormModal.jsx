import { useState, useEffect } from "react";
import { X, Save, Loader2 } from "lucide-react";

const initialFormState = {
  angkatan: "",
  "Jumlah Awal": "",
  Resign_In_Class: 0,
  Resign_OJT: 0,
  tidak_lulus: 0
};

export function AngkatanFormModal({ isOpen, onClose, onSubmit, initialData }) {
  const [formData, setFormData] = useState(initialFormState);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (initialData) {
      setFormData(initialData);
    } else {
      setFormData(initialFormState);
    }
  }, [initialData, isOpen]);

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value } = e.target;
    const numericFields = ["Jumlah Awal", "Resign_In_Class", "Resign_OJT", "tidak_lulus"];

    let finalValue = value;
    if (numericFields.includes(name)) {
      finalValue = value === "" ? "" : Number(value);
    }

    setFormData((prev) => ({ ...prev, [name]: finalValue }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      if (!formData.angkatan || formData["Jumlah Awal"] === "" || formData["Jumlah Awal"] === null || formData["Jumlah Awal"] === undefined) {
        throw new Error("Nama angkatan dan Jumlah Awal wajib diisi!");
      }

      const normalized = {
        ...formData,
        "Jumlah Awal": Number(formData["Jumlah Awal"] ?? 0),
        Resign_In_Class: Number(formData.Resign_In_Class ?? 0),
        Resign_OJT: Number(formData.Resign_OJT ?? 0),
        tidak_lulus: Number(formData.tidak_lulus ?? 0)
      };

      await onSubmit(normalized);
      onClose();
    } catch (error) {
      console.error("Error submitting form:", error);
      alert("Terjadi kesalahan: " + error.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200">
      <div className="bg-white rounded-[2rem] shadow-xl w-full max-w-md flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
        
        {/* Header */}
        <div className="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
          <div>
            <h2 className="text-xl font-bold text-gray-800">
              {initialData ? "Edit Angkatan" : "Tambah Angkatan Baru"}
            </h2>
            <p className="text-sm text-gray-500 font-medium">Lengkapi master data angkatan</p>
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
              Nama Angkatan <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="angkatan"
              value={formData.angkatan || ""}
              onChange={handleChange}
              placeholder="Cth: Field Assistant - I/2026"
              required
              className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] focus:border-transparent outline-none transition-all"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-gray-600 mb-1">
              Jumlah Awal <span className="text-red-500">*</span>
            </label>
            <input
              type="number"
              name="Jumlah Awal"
              value={formData["Jumlah Awal"] ?? ""}
              onChange={handleChange}
              placeholder="Cth: 35"
              required
              className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
            />
          </div>

          <div className="grid grid-cols-3 gap-3">
            <div>
              <label className="block text-xs font-semibold text-gray-600 mb-1">Resign In Class</label>
              <input
                type="number"
                min="0"
                name="Resign_In_Class"
                value={formData.Resign_In_Class ?? 0}
                onChange={handleChange}
                className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-gray-600 mb-1">Resign OJT</label>
              <input
                type="number"
                min="0"
                name="Resign_OJT"
                value={formData.Resign_OJT ?? 0}
                onChange={handleChange}
                className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-gray-600 mb-1">Tidak Lulus</label>
              <input
                type="number"
                min="0"
                name="tidak_lulus"
                value={formData.tidak_lulus ?? 0}
                onChange={handleChange}
                className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2c8f42] outline-none transition-all"
              />
            </div>
          </div>

          <p className="text-[10px] text-blue-500 mt-2 font-medium bg-blue-50 p-2 rounded-lg">
            ℹ️ Lulus dihitung otomatis dengan rumus: Jumlah Awal - Resign In Class - Resign OJT - Tidak Lulus.
          </p>

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
