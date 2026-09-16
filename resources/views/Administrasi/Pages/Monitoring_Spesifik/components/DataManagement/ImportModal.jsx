import { useState, useRef } from "react";
import { X, Upload, FileText, CheckCircle2, AlertCircle, Loader2, Database, Info, Download } from "lucide-react";

export function ImportModal({
  isOpen,
  onClose,
  onImport,
  title = "Import Data Excel / CSV",
  description = "Unggah file Excel (.xlsx) atau CSV untuk mengimport data secara massal.",
  expectedColumns = [],
  templateUrl = ""
}) {
  const [file, setFile] = useState(null);
  const [previewRows, setPreviewRows] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [headers, setHeaders] = useState([]);
  const [mode, setMode] = useState("replace"); // 'replace' or 'append'
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState("");
  const fileInputRef = useRef(null);

  if (!isOpen) return null;

  const parseCSVLine = (line) => {
    const result = [];
    let cur = "";
    let inQuotes = false;
    for (let i = 0; i < line.length; i++) {
      const c = line[i];
      if (c === '"') {
        if (inQuotes && line[i + 1] === '"') {
          cur += '"';
          i++;
        } else {
          inQuotes = !inQuotes;
        }
      } else if (c === "," && !inQuotes) {
        result.push(cur.trim());
        cur = "";
      } else {
        cur += c;
      }
    }
    result.push(cur.trim());
    return result;
  };

  const handleFileChange = (e) => {
    const selectedFile = e.target.files?.[0];
    if (!selectedFile) return;

    setErrorMsg("");
    setFile(selectedFile);

    if (selectedFile.name.endsWith(".csv")) {
      const reader = new FileReader();
      reader.onload = (event) => {
        try {
          const text = event.target?.result;
          if (typeof text !== "string") return;

          const lines = text.trim().split(/\r?\n/);
          if (lines.length < 2) {
            setErrorMsg("File CSV kosong atau tidak memiliki baris data.");
            return;
          }

          const parsedHeaders = parseCSVLine(lines[0]);
          setHeaders(parsedHeaders);

          const sample = [];
          let count = 0;
          for (let i = 1; i < lines.length; i++) {
            if (!lines[i].trim()) continue;
            count++;
            if (sample.length < 5) {
              const vals = parseCSVLine(lines[i]);
              const rowObj = {};
              parsedHeaders.forEach((h, idx) => {
                rowObj[h] = vals[idx] || "";
              });
              sample.push(rowObj);
            }
          }

          setPreviewRows(sample);
          setTotalRows(count);
        } catch (err) {
          setErrorMsg("Gagal membaca file CSV: " + err.message);
        }
      };
      reader.readAsText(selectedFile);
    } else {
      // Excel files will be read directly by backend SpreadsheetReader
      setHeaders([]);
      setPreviewRows([]);
      setTotalRows(1);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!file) {
      setErrorMsg("Silakan pilih file Excel / CSV terlebih dahulu.");
      return;
    }

    setIsSubmitting(true);
    setErrorMsg("");

    try {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("mode", mode);

      await onImport(formData, mode);
      onClose();
    } catch (err) {
      console.error("Import error:", err);
      setErrorMsg(err.message || "Gagal mengimport data.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleResetFile = () => {
    setFile(null);
    setPreviewRows([]);
    setTotalRows(0);
    setHeaders([]);
    setErrorMsg("");
    if (fileInputRef.current) {
      fileInputRef.current.value = "";
    }
  };

  return (
    <div className="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-in fade-in duration-200">
      <div className="bg-white rounded-[2rem] shadow-xl w-full max-w-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-300 max-h-[90vh]">
        
        {/* Header */}
        <div className="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
          <div>
            <h2 className="text-xl font-bold text-gray-800 flex items-center gap-2">
              <Upload className="text-[#2c8f42]" size={22} />
              {title}
            </h2>
            <p className="text-sm text-gray-500 font-medium mt-0.5">{description}</p>
          </div>
          <button 
            type="button"
            onClick={onClose}
            className="p-2 hover:bg-gray-200 rounded-full text-gray-500 transition-colors"
          >
            <X size={22} />
          </button>
        </div>

        {/* Content */}
        <form onSubmit={handleSubmit} className="p-8 overflow-y-auto space-y-6 flex-1 custom-scrollbar">
          
          {errorMsg && (
            <div className="p-4 bg-red-50 text-red-600 rounded-xl text-sm flex items-start gap-3 border border-red-100">
              <AlertCircle size={18} className="shrink-0 mt-0.5" />
              <span>{errorMsg}</span>
            </div>
          )}

          {templateUrl && (
            <div className="flex items-center justify-between p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl">
              <div className="flex items-center gap-2.5">
                <FileText className="text-[#2c8f42]" size={18} />
                <span className="text-xs font-bold text-gray-700">Belum memiliki format template?</span>
              </div>
              <a
                href={templateUrl}
                download
                className="px-3.5 py-2 bg-[#2c8f42] text-white hover:bg-[#237535] rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm shrink-0"
              >
                <Download size={15} /> Download Template Excel
              </a>
            </div>
          )}

          {/* Mode Selection */}
          <div>
            <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
              Metode Import:
            </label>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label 
                className={`flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all ${
                  mode === "replace" 
                    ? "bg-[#f0f9f3] border-[#2c8f42] ring-1 ring-[#2c8f42]" 
                    : "bg-gray-50 border-gray-200 hover:bg-gray-100"
                }`}
              >
                <input 
                  type="radio" 
                  name="importMode" 
                  value="replace" 
                  checked={mode === "replace"} 
                  onChange={() => setMode("replace")}
                  className="mt-1 accent-[#2c8f42]"
                />
                <div>
                  <p className="text-xs font-bold text-gray-800">Ganti Semua Data (Replace)</p>
                  <p className="text-[11px] text-gray-500 mt-0.5 leading-tight">
                    Mengosongkan data lama dan mengganti dengan data baru dari file Excel / CSV.
                  </p>
                </div>
              </label>

              <label 
                className={`flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all ${
                  mode === "append" 
                    ? "bg-[#f0f9f3] border-[#2c8f42] ring-1 ring-[#2c8f42]" 
                    : "bg-gray-50 border-gray-200 hover:bg-gray-100"
                }`}
              >
                <input 
                  type="radio" 
                  name="importMode" 
                  value="append" 
                  checked={mode === "append"} 
                  onChange={() => setMode("append")}
                  className="mt-1 accent-[#2c8f42]"
                />
                <div>
                  <p className="text-xs font-bold text-gray-800">Tambahkan Data (Append)</p>
                  <p className="text-[11px] text-gray-500 mt-0.5 leading-tight">
                    Menambahkan data dari file ke daftar yang sudah ada tanpa menghapus.
                  </p>
                </div>
              </label>
            </div>
          </div>

          {/* File Dropzone */}
          <div>
            <label className="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
              Pilih File Excel / CSV:
            </label>

            {!file ? (
              <div 
                onClick={() => fileInputRef.current?.click()}
                className="border-2 border-dashed border-gray-300 hover:border-[#2c8f42] bg-gray-50/50 hover:bg-[#f0f9f3]/40 rounded-2xl p-8 flex flex-col items-center justify-center gap-3 cursor-pointer transition-all"
              >
                <div className="w-12 h-12 rounded-full bg-emerald-50 text-[#2c8f42] flex items-center justify-center">
                  <Upload size={24} />
                </div>
                <div className="text-center">
                  <p className="text-sm font-bold text-gray-700">Klik untuk memilih file Excel / CSV</p>
                  <p className="text-xs text-gray-400 mt-1">Format didukung: .xlsx, .csv</p>
                </div>
              </div>
            ) : (
              <div className="bg-[#f0f9f3] border border-[#2c8f42]/30 rounded-2xl p-4 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-[#2c8f42] text-white flex items-center justify-center shrink-0">
                    <FileText size={20} />
                  </div>
                  <div>
                    <p className="text-sm font-bold text-gray-800">{file.name}</p>
                    <p className="text-xs text-gray-500 mt-0.5">
                      {(file.size / 1024).toFixed(1)} KB {totalRows > 1 && `• ${totalRows} baris data`}
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={handleResetFile}
                  className="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  Ganti File
                </button>
              </div>
            )}

            <input 
              ref={fileInputRef}
              type="file" 
              accept=".csv,text/csv,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
              onChange={handleFileChange} 
              className="hidden"
            />
          </div>

          {/* Data Preview */}
          {previewRows.length > 0 && (
            <div>
              <div className="flex items-center justify-between mb-2">
                <p className="text-xs font-bold text-gray-700 uppercase tracking-wider">
                  Pratinjau Data ({previewRows.length} dari {totalRows} baris):
                </p>
                <span className="text-[11px] text-gray-400 font-medium">Geser untuk melihat kolom</span>
              </div>
              <div className="border border-gray-200 rounded-xl overflow-x-auto max-h-48 custom-scrollbar">
                <table className="w-full text-left text-xs border-collapse whitespace-nowrap">
                  <thead>
                    <tr className="bg-gray-100 text-gray-700 sticky top-0 font-bold border-b border-gray-200">
                      <th className="px-3 py-2 border-r border-gray-200">No</th>
                      {headers.slice(0, 8).map((h, idx) => (
                        <th key={idx} className="px-3 py-2 border-r border-gray-200">{h}</th>
                      ))}
                      {headers.length > 8 && <th className="px-3 py-2">+ {headers.length - 8} kolom lainnya</th>}
                    </tr>
                  </thead>
                  <tbody>
                    {previewRows.map((row, rIdx) => (
                      <tr key={rIdx} className="border-b border-gray-100 even:bg-gray-50">
                        <td className="px-3 py-1.5 text-gray-500 border-r border-gray-200 font-medium">{rIdx + 1}</td>
                        {headers.slice(0, 8).map((h, cIdx) => (
                          <td key={cIdx} className="px-3 py-1.5 text-gray-800 border-r border-gray-200">
                            {row[h] || "-"}
                          </td>
                        ))}
                        {headers.length > 8 && <td className="px-3 py-1.5 text-gray-400">...</td>}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Form Actions */}
          <div className="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              disabled={isSubmitting}
              className="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={isSubmitting || !file}
              className="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#2c8f42] hover:bg-[#237535] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-sm shadow-[#2c8f42]/30"
            >
              {isSubmitting ? <Loader2 size={18} className="animate-spin" /> : <Upload size={18} />}
              {isSubmitting ? "Mengimport Data..." : `Import ${totalRows > 0 ? `${totalRows} Baris` : "Data"}`}
            </button>
          </div>
        </form>

      </div>
    </div>
  );
}
