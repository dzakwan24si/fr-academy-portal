import { useState, useEffect } from "react";
import { X, Save, Loader2 } from "lucide-react";

export function AfdelingFormModal({ isOpen, onClose, onSubmit, initialData = {} }) {
  const [formData, setFormData] = useState({
    Riau: 0,
    Kalbar: 0,
    Kubar: 0,
    Corp: 0,
  });
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (isOpen) {
      setFormData({
        Riau: initialData.Riau ?? 0,
        Kalbar: initialData.Kalbar ?? 0,
        Kubar: initialData.Kubar ?? 0,
        Corp: initialData.Corp ?? 0,
      });
    }
  }, [isOpen, initialData]);

  if (!isOpen) return null;

  const handleChange = (region, value) => {
    const numValue = value === "" ? "" : Math.max(0, parseInt(value, 10) || 0);
    setFormData((prev) => ({
      ...prev,
      [region]: numValue,
    }));
  };

  const calculatedTotal =
    (Number(formData.Riau) || 0) +
    (Number(formData.Kalbar) || 0) +
    (Number(formData.Kubar) || 0) +
    (Number(formData.Corp) || 0);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      await onSubmit({
        Riau: Number(formData.Riau) || 0,
        Kalbar: Number(formData.Kalbar) || 0,
        Kubar: Number(formData.Kubar) || 0,
        Corp: Number(formData.Corp) || 0,
      });
      onClose();
    } catch (error) {
      console.error("Error submitting afdeling data:", error);
      alert("Terjadi kesalahan: " + (error.message || "Gagal menyimpan"));
    } finally {
      setIsSubmitting(false);
    }
  };

  const regions = [
    { key: "Riau", label: "Riau" },
    { key: "Kalbar", label: "Kalbar" },
    { key: "Kubar", label: "Kubar" },
    { key: "Corp", label: "Corp" },
  ];

  return (
    <div className="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200">
      <div className="bg-white rounded-[2rem] shadow-xl w-full max-w-md flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
        {/* Header */}
        <div className="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
          <div>
            <h2 className="text-xl font-bold text-gray-800">Edit Data Afdeling</h2>
            <p className="text-sm text-gray-500 font-medium">Update total afdeling per region</p>
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
        <form onSubmit={handleSubmit} className="p-8 space-y-5">
          <div className="grid grid-cols-2 gap-4">
            {regions.map(({ key, label }) => (
              <div key={key}>
                <label className="block text-xs font-semibold text-gray-600 mb-1">
                  {label} (Total) <span className="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  min="0"
                  value={formData[key] === "" ? "" : formData[key]}
                  onChange={(e) => handleChange(key, e.target.value)}
                  placeholder="0"
                  required
                  className="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-[#2c8f42] focus:border-transparent outline-none transition-all"
                />
              </div>
            ))}
          </div>

          {/* Total Display Preview */}
          <div className="bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex justify-between items-center">
            <span className="text-xs font-bold text-emerald-800 uppercase tracking-wider">
              Total Keseluruhan Afdeling:
            </span>
            <span className="text-base font-extrabold text-emerald-700">
              {calculatedTotal}
            </span>
          </div>

          {/* Footer Actions */}
          <div className="pt-3 border-t border-gray-100 flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-all"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-6 py-2.5 rounded-xl bg-[#2c8f42] text-white text-sm font-bold hover:bg-[#237335] shadow-md shadow-green-900/20 transition-all flex items-center gap-2 disabled:opacity-50"
            >
              {isSubmitting ? (
                <>
                  <Loader2 size={16} className="animate-spin" />
                  Menyimpan...
                </>
              ) : (
                <>
                  <Save size={16} />
                  Simpan Perubahan
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
