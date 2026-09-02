import { useState } from "react";
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer
} from "recharts";
import { ChevronDown } from "lucide-react";

export function TurnoverChart({ summaryData = [] }) {
  const [selectedYear, setSelectedYear] = useState("All");

  const chartDataRaw = [...summaryData].sort((a, b) => {
    // Basic sorting by Year in Angkatan name if possible
    const yearA = parseInt((a.angkatan || "").match(/\d{4}/)) || 0;
    const yearB = parseInt((b.angkatan || "").match(/\d{4}/)) || 0;
    if (yearA !== yearB) return yearA - yearB;
    return (a.angkatan || "").localeCompare(b.angkatan || "");
  });

  const availableYears = Array.from(new Set(chartDataRaw.map(d => {
    const match = (d.angkatan || "").match(/\d{4}/);
    return match ? match[0] : null;
  }).filter(Boolean))).sort((a, b) => b - a);

  const chartData = chartDataRaw.filter(d => {
    if (selectedYear === "All") return true;
    const match = (d.angkatan || "").match(/\d{4}/);
    return match && match[0] === selectedYear;
  });

  return (
    <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h3 className="text-lg font-bold text-gray-800">Tingkat Kelulusan Peserta per Angkatan</h3>
          <p className="text-sm text-gray-500 mt-1">Status akhir peserta Academy</p>
        </div>

        <div className="relative">
          <select
            className="appearance-none flex items-center gap-2 text-sm text-gray-600 border border-gray-200 pl-4 pr-9 py-1.5 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2ecc71] bg-white cursor-pointer"
            value={selectedYear}
            onChange={(e) => setSelectedYear(e.target.value)}
          >
            <option value="All">Semua angkatan</option>
            {availableYears.map(year => (
              <option key={year} value={year}>Tahun {year}</option>
            ))}
          </select>
          <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 pointer-events-none" />
        </div>
      </div>

      <div className="flex-1 min-h-[350px] w-full">
        {chartData.length > 0 ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart
              data={chartData}
              margin={{ top: 20, right: 10, left: -20, bottom: 5 }}
            >
              <CartesianGrid vertical={false} stroke="#f1f5f9" />
              <XAxis dataKey="angkatan" axisLine={false} tickLine={false} tick={{ fill: '#94a3b8', fontSize: 12 }} dy={10} />
              <YAxis axisLine={false} tickLine={false} tick={{ fill: '#94a3b8', fontSize: 12 }} dx={-10} />

              <Tooltip
                cursor={{ fill: 'transparent' }}
                contentStyle={{ borderRadius: '12px', border: '1px solid #f8f9fa', boxShadow: '0 4px 20px -2px rgba(0, 0, 0, 0.05)', padding: '12px 16px' }}
                labelStyle={{ fontWeight: '600', color: '#1e293b', marginBottom: '8px' }}
                itemStyle={{ fontSize: '13px', padding: '2px 0' }}
              />
              <Legend
                verticalAlign="top"
                align="right"
                iconType="circle"
                iconSize={8}
                wrapperStyle={{ paddingBottom: '24px', fontSize: '13px', color: '#64748b' }}
              />

              <Bar dataKey="lulus_calculated" name="Lulus" stackId="a" fill="#2EAD67" radius={[0, 0, 4, 4]} barSize={28} />
              <Bar dataKey="culled_calculated" name="Culled" stackId="a" fill="#F0A23A" radius={[0, 0, 0, 0]} barSize={28} />
              <Bar dataKey="tidak_lulus_calculated" name="Tidak Lulus Evaluasi" stackId="a" fill="#A7B0B0" radius={[4, 4, 0, 0]} barSize={28} />
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="flex h-full items-center justify-center text-gray-400">
            Tidak ada data untuk tahun {selectedYear}
          </div>
        )}
      </div>
    </div>
  );
}
