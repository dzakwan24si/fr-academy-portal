import { useState } from "react";
import { AnalisaTab } from "../components/Dashboard/AnalisaTab";
import { EksisTab } from "../components/Dashboard/EksisTab";
import { TerminateTab } from "../components/Dashboard/TerminateTab";
import { RegionalKpi } from "../components/DataManagement/RegionalKpi";
import { BarChart3, Users, UserMinus, Loader2 } from "lucide-react";
import { useEmployees } from "../hooks/useEmployees";
import { useAngkatan } from "../hooks/useAngkatan";
import { useAfdeling } from "../hooks/useAfdeling";

export default function Dashboard() {
  const [activeTab, setActiveTab] = useState("analisa");
  const [selectedProgram, setSelectedProgram] = useState("Semua");

  const PROGRAM_CATEGORIES = ["Semua", "FAT", "AAT", "PMT", "MAT", "DMT"];

  const getProgramCategory = (text = "") => {
    const str = String(text || "").toUpperCase();
    if (str.includes("FAT") || str.includes("FIELD ASSISTANT") || str.includes("ESTATE CADET") || str.includes("ECT")) {
      return "FAT";
    }
    if (str.includes("AAT") || str.includes("ADMIN ASSISTANT") || str.includes("ADMINISTRATION") || str.includes("ADMIN")) {
      return "AAT";
    }
    if (str.includes("MAT") || str.includes("MILL ASSISTANT")) {
      return "MAT";
    }
    if (str.includes("PMT") || str.includes("PROCESSING") || str.includes("PALM OIL MILL") || str.includes("PABRIK")) {
      return "PMT";
    }
    if (str.includes("DMT") || str.includes("DOWNSTREAM")) {
      return "DMT";
    }
    return "OTHER";
  };

  const matchesEmployeeProgram = (emp, cat) => {
    if (cat === "Semua") return true;
    const angkatan = emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || emp.angkatan || "";
    return (
      getProgramCategory(angkatan) === cat ||
      getProgramCategory(emp.JABATAN) === cat ||
      getProgramCategory(emp.KATEGORI) === cat
    );
  };

  const matchesAngkatanProgram = (item, cat) => {
    if (cat === "Semua") return true;
    const name = item.angkatan || item.Angkatan || "";
    return getProgramCategory(name) === cat;
  };

  // Fetch ALL employees for the dashboard aggregations
  const { data, loading, error } = useEmployees();
  const { data: angkatanData, loading: angkatanLoading, error: angkatanError } = useAngkatan();
  const { data: afdelingData, loading: afdelingLoading, error: afdelingError, updateAfdeling } = useAfdeling();

  const filteredEmployees = (data || []).filter(emp => matchesEmployeeProgram(emp, selectedProgram));
  const filteredAngkatan = (angkatanData || []).filter(item => matchesAngkatanProgram(item, selectedProgram));

  const categoryCounts = PROGRAM_CATEGORIES.reduce((acc, cat) => {
    if (cat === "Semua") {
      acc[cat] = (data || []).length;
    } else {
      acc[cat] = (data || []).filter(emp => matchesEmployeeProgram(emp, cat)).length;
    }
    return acc;
  }, {});

  const tabs = [
    { id: "analisa", label: "Analisa", icon: <BarChart3 size={18} /> },
    { id: "eksis", label: "Eksis", icon: <Users size={18} /> },
    { id: "terminate", label: "Terminate", icon: <UserMinus size={18} /> },
  ];

  return (
    <div className="h-full flex flex-col gap-6 overflow-hidden">
      {/* Header & Controls */}
      <div className="flex flex-col gap-5 shrink-0">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 className="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p className="text-gray-500 text-sm mt-1">Ringkasan data analitik dan statistik karyawan FR Academy.</p>
          </div>

          {/* Tab Navigation */}
          <div className="flex items-center gap-2 bg-white p-1.5 rounded-2xl shadow-sm w-max max-w-full overflow-x-auto border border-gray-100 custom-scrollbar">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap shrink-0 ${
                  activeTab === tab.id
                    ? "bg-[#2c8f42] text-white shadow-md shadow-green-900/20"
                    : "text-gray-500 hover:text-gray-700 hover:bg-gray-50"
                }`}
              >
                {tab.icon}
                {tab.label}
              </button>
            ))}
          </div>
        </div>

        {/* Program Category Filter Pills */}
        <div className="flex items-center gap-2 bg-white p-2.5 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
          <span className="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1 mr-1 shrink-0">Filter Program:</span>
          {PROGRAM_CATEGORIES.map((cat) => {
            const isActive = selectedProgram === cat;
            const count = categoryCounts[cat] || 0;
            return (
              <button
                key={cat}
                type="button"
                onClick={() => setSelectedProgram(cat)}
                className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                  isActive
                    ? "bg-[#2c8f42] text-white shadow-sm shadow-[#2c8f42]/30 ring-2 ring-[#2c8f42]/20"
                    : "bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200/70"
                }`}
              >
                <span>{cat}</span>
                <span
                  className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                    isActive ? "bg-white/20 text-white" : "bg-gray-200/80 text-gray-600"
                  }`}
                >
                  {count}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* Tab Content Area - Scrollable */}
      <div className="flex-1 overflow-y-auto pr-2 pb-8 custom-scrollbar">
        {loading || angkatanLoading || afdelingLoading ? (
           <div className="flex flex-col items-center justify-center h-64 text-gray-400 gap-3">
             <Loader2 size={32} className="animate-spin text-[#2c8f42]" />
             <p className="text-sm font-bold">Memuat dan menghitung data analitik...</p>
           </div>
        ) : error || angkatanError || afdelingError ? (
           <div className="p-6 bg-red-50 text-red-600 rounded-2xl border border-red-100">
             Gagal memuat data: {error || angkatanError || afdelingError}
           </div>
        ) : (
          <>
            {activeTab === "analisa" && (
              <>
                <AnalisaTab data={filteredEmployees} summaryData={filteredAngkatan} />
                <RegionalKpi data={filteredEmployees} summaryData={filteredAngkatan} afdelingData={afdelingData} onUpdateAfdeling={updateAfdeling} />
              </>
            )}
            {activeTab === "eksis" && <EksisTab data={filteredEmployees} />}
            {activeTab === "terminate" && <TerminateTab data={filteredEmployees} />}
          </>
        )}
      </div>
    </div>
  );
}
