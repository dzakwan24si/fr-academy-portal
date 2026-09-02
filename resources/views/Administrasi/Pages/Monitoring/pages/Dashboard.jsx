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

  // Fetch ALL employees for the dashboard aggregations
  const { data, loading, error } = useEmployees();
  const { data: angkatanData, loading: angkatanLoading, error: angkatanError } = useAngkatan();
  const { data: afdelingData, loading: afdelingLoading, error: afdelingError, updateAfdeling } = useAfdeling();

  const tabs = [
    { id: "analisa", label: "Analisa", icon: <BarChart3 size={18} /> },
    { id: "eksis", label: "Eksis", icon: <Users size={18} /> },
    { id: "terminate", label: "Terminate", icon: <UserMinus size={18} /> },
  ];

  return (
    <div className="h-full flex flex-col gap-6 overflow-hidden">
      {/* Header & Tabs */}
      <div className="flex flex-col gap-6 shrink-0">
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
                <AnalisaTab data={data} summaryData={angkatanData} />
                <RegionalKpi data={data} summaryData={angkatanData} afdelingData={afdelingData} onUpdateAfdeling={updateAfdeling} />
              </>
            )}
            {activeTab === "eksis" && <EksisTab data={data} />}
            {activeTab === "terminate" && <TerminateTab data={data} />}
          </>
        )}
      </div>
    </div>
  );
}
