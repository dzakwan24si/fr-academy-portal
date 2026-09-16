import { useState } from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LabelList } from 'recharts';
import { ChevronDown } from 'lucide-react';

export function EksisTab({ data = [] }) {
  const [filterAngkatanRiau, setFilterAngkatanRiau] = useState('All');
  const [filterAngkatanKalbar, setFilterAngkatanKalbar] = useState('All');
  const [filterAngkatanKubar, setFilterAngkatanKubar] = useState('All');
  const [filterAngkatan1T, setFilterAngkatan1T] = useState('All');
  const [filterAngkatan3T, setFilterAngkatan3T] = useState('All');
  const [filterAngkatan5T, setFilterAngkatan5T] = useState('All');
  
  // Filter ONLY Lulusan (Alumni)
  const isAlumni = (kategori) => kategori?.trim().toLowerCase() === 'alumni';
  // Exclude FAT II 2026 from all charts because they are still training
  const allLulusan = data.filter(emp => isAlumni(emp.KATEGORI) && (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim() !== "FAT II 2026");
  const activeLulusan = allLulusan.filter(emp => emp.STATUS === 'Eksis' || emp.STATUS === 'Aktif');

  // Angkatan Options for Penempatan Awal
  const angkatanOptionsAwal = Array.from(new Set(
    allLulusan.map(emp => (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim()).filter(Boolean)
  )).sort();

  // Robust Region Normalizer
  const normalizeRegion = (value = "") => {
    const region = String(value).toLowerCase();
    if (region.includes("riau")) return "riau";
    if (region.includes("kalbar") || region.includes("kalimantan barat")) return "kalbar";
    if (region.includes("kubar") || region.includes("kalimantan timur")) return "kubar";
    if (region.includes("corp") || region.includes("pusat") || region.includes("jakarta")) return "corp";
    return null;
  };

  // 1. TOP CHARTS: Penempatan Awal Lulusan (Semua Lulusan, Lokasi Awal MURNI tanpa fallback)
  const aggregateAwalByRegion = (dataset, regionKeyword, angkatanFilter) => {
    // Filter dataset by angkatan first
    const filteredDataset = dataset.filter(emp => {
      if (angkatanFilter === 'All') return true;
      return (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim() === angkatanFilter;
    });

    const locations = {};
    filteredDataset.forEach(emp => {
      const rawRegion = emp["REGION AWAL PENEMPATAN"];
      if (!rawRegion) return; // Skip if blank in database

      const region = normalizeRegion(rawRegion);
      if (region === regionKeyword.toLowerCase()) {
        const loc = emp["LOKASI AWAL PENEMPATAN"];
        if (!loc) return; // Skip if blank in database
        
        locations[loc] = (locations[loc] || 0) + 1;
      }
    });
    
    return Object.keys(locations).map(key => ({
      name: key,
      val: locations[key]
    })).sort((a, b) => b.val - a.val); // Sort descending
  };

  const dataRiauAwal = aggregateAwalByRegion(allLulusan, "riau", filterAngkatanRiau);
  const dataKalbarAwal = aggregateAwalByRegion(allLulusan, "kalbar", filterAngkatanKalbar);
  const dataKubarAwal = aggregateAwalByRegion(allLulusan, "kubar", filterAngkatanKubar);

  // 2. BOTTOM CHARTS: Lokasi Lulusan Eksis per PT (Aktif, Lokasi Terakhir, Kumulatif berdasar Tahun Angkatan)
  const getYear = (emp) => {
    const angkatanMatch = String(emp["ANGKATAN FR ACADEMY"] || emp.angkatan || "").match(/(?:19|20)\d{2}/);
    if (angkatanMatch) return Number(angkatanMatch[0]);
    if (emp["JOIN DATE"]) return new Date(emp["JOIN DATE"]).getFullYear();
    return new Date().getFullYear();
  };

  const currentYear = new Date().getFullYear();
  
  // Cumulative filters with specific Angkatan filters applied
  const active1TahunRaw = activeLulusan.filter(emp => getYear(emp) >= currentYear);
  const angkatanOptions1T = Array.from(new Set(
    active1TahunRaw.map(emp => (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim()).filter(Boolean)
  )).filter(a => a !== "FAT II 2026").sort();
  
  const active1Tahun = active1TahunRaw.filter(emp => {
    if (filterAngkatan1T === 'All') return true;
    return (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim() === filterAngkatan1T;
  });

  const active3TahunRaw = activeLulusan.filter(emp => getYear(emp) >= currentYear - 2);
  const angkatanOptions3T = Array.from(new Set(
    active3TahunRaw.map(emp => (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim()).filter(Boolean)
  )).filter(a => a !== "FAT II 2026").sort();
  
  const active3Tahun = active3TahunRaw.filter(emp => {
    if (filterAngkatan3T === 'All') return true;
    return (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim() === filterAngkatan3T;
  });

  const active5TahunRaw = activeLulusan.filter(emp => getYear(emp) >= currentYear - 5);
  const angkatanOptions5T = Array.from(new Set(
    active5TahunRaw.map(emp => (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim()).filter(Boolean)
  )).filter(a => a !== "FAT II 2026").sort();
  
  const active5Tahun = active5TahunRaw.filter(emp => {
    if (filterAngkatan5T === 'All') return true;
    return (emp["ANGKATAN FR ACADEMY"] || emp.ANGKATAN || "").trim() === filterAngkatan5T;
  });

  const regionOrder = { "riau": 1, "kalbar": 2, "kubar": 3, "corp": 4 };

  const aggregateGroupedLocations = (dataset) => {
    const locations = {};
    dataset.forEach(emp => {
      const loc = emp["LOKASI TERAKHIR"] || "Unknown";
      const region = normalizeRegion(emp["REGION TERAKHIR"]) || "lainnya";
      
      const key = `${region}_${loc}`;
      if (!locations[key]) {
        locations[key] = { name: loc, region: region, count: 0 };
      }
      locations[key].count += 1;
    });
    
    return Object.values(locations)
      .map(item => ({
        name: item.name,
        val: item.count,
        region: item.region
      }))
      .sort((a, b) => {
        const rA = regionOrder[a.region] || 99;
        const rB = regionOrder[b.region] || 99;
        if (rA !== rB) return rA - rB;
        return b.val - a.val; // descending count within region
      });
  };

  const dataLulusan1Tahun = aggregateGroupedLocations(active1Tahun);
  const dataLulusan3Tahun = aggregateGroupedLocations(active3Tahun);
  const dataLulusan5Tahun = aggregateGroupedLocations(active5Tahun);

  const CustomTooltip = ({ active, payload, label }) => {
    if (active && payload && payload.length) {
      return (
        <div className="bg-white p-3 rounded-xl shadow-lg border border-[#2c8f42]/20">
          <p className="text-xs font-bold text-gray-500 mb-1">{label}</p>
          <p className="text-sm font-extrabold text-[#2c8f42]">{`Jumlah: ${payload[0].value}`}</p>
        </div>
      );
    }
    return null;
  };

  const CustomGroupedTick = (props) => {
    const { x, y, payload, index, data } = props;
    const item = data[index];
    if (!item) return null;

    const region = item.region;
    let start = -1, end = -1;
    for (let i = 0; i < data.length; i++) {
      if (data[i].region === region) {
        if (start === -1) start = i;
        end = i;
      }
    }
    const middleIndex = Math.floor((start + end) / 2);
    const isMiddle = index === middleIndex;
    const isRegionStart = index === start;
    const isRegionEnd = index === end;

    const bracketColor = "#1a5b28"; // Dark forest green (same as the label text) to make it highly visible
    const strokeW = 2; // Thicker lines for clear boundaries

    return (
      <g transform={`translate(${x},${y})`}>
        <text x={0} y={0} dy={12} textAnchor="end" fill="#1a5b28" fontSize={9} transform="rotate(-45)">
          {payload.value}
        </text>
        
        {/* Horizontal bracket line */}
        <line 
          x1={isRegionStart ? 0 : -100} 
          y1={44} 
          x2={isRegionEnd ? 0 : 100} 
          y2={44} 
          stroke={bracketColor} 
          strokeWidth={strokeW} 
        />
        
        {/* Vertical bracket ends (made taller for visibility) */}
        {isRegionStart && (
          <line x1={0} y1={36} x2={0} y2={44} stroke={bracketColor} strokeWidth={strokeW} />
        )}
        {isRegionEnd && (
          <line x1={0} y1={36} x2={0} y2={44} stroke={bracketColor} strokeWidth={strokeW} />
        )}

        {isMiddle && (
          <text x={0} y={60} textAnchor="middle" fill="#0b4a1b" fontSize={12} fontWeight="900">
            {region.toUpperCase()}
          </text>
        )}
      </g>
    );
  };

  const renderChart = (title, chartData, isGrouped = false, filterComponent = null) => (
    <div className="bg-[#f0f9f3] p-6 rounded-3xl border border-[#2c8f42]/20 shadow-sm flex flex-col items-center relative">
      <div className="w-full flex justify-between items-center mb-6">
        <h3 className="text-xs font-extrabold text-[#1a5b28] uppercase tracking-wider text-left flex-1">{title}</h3>
        {filterComponent}
      </div>
      <div className="w-full h-[250px]">
        {chartData.length > 0 ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={chartData} margin={{ top: 20, right: 10, left: -20, bottom: isGrouped ? 80 : 30 }}>
              <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#d1e8d8" />
              <XAxis 
                dataKey="name" 
                axisLine={false} 
                tickLine={false} 
                interval={0} 
                tick={isGrouped ? <CustomGroupedTick data={chartData} /> : { fontSize: 9, fill: '#1a5b28', angle: -45, textAnchor: "end" }}
              />
              <YAxis tick={{ fontSize: 10, fill: '#1a5b28' }} axisLine={false} tickLine={false} />
              <Tooltip content={<CustomTooltip />} cursor={{ fill: '#eaf4ec' }} />
              <Bar dataKey="val" fill="#14756c" radius={[4, 4, 0, 0]} barSize={12}>
                <LabelList dataKey="val" position="top" style={{ fontSize: '10px', fontWeight: 'bold', fill: '#1a5b28' }} />
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-[#14756c]/60 font-bold">
            Tidak ada data
          </div>
        )}
      </div>
    </div>
  );

  return (
    <div className="flex flex-col gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500 bg-[#35a892] p-6 rounded-3xl">
      
      {/* Top Row: Penempatan Awal per Region */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {renderChart(
          "Penempatan Awal Lulusan Region Riau", 
          dataRiauAwal, 
          false,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatanRiau}
                onChange={(e) => setFilterAngkatanRiau(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptionsAwal.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#1a5b28]">
                <ChevronDown size={14} strokeWidth={3} />
              </div>
            </div>
          )
        )}
        {renderChart(
          "Penempatan Awal Lulusan Region Kalbar", 
          dataKalbarAwal, 
          false,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatanKalbar}
                onChange={(e) => setFilterAngkatanKalbar(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptionsAwal.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#1a5b28]">
                <ChevronDown size={14} strokeWidth={3} />
              </div>
            </div>
          )
        )}
        {renderChart(
          "Penempatan Awal Lulusan Region Kubar", 
          dataKubarAwal, 
          false,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatanKubar}
                onChange={(e) => setFilterAngkatanKubar(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptionsAwal.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#1a5b28]">
                <ChevronDown size={14} strokeWidth={3} />
              </div>
            </div>
          )
        )}
      </div>

      {/* Middle Row: Lulusan Eksis 1 Tahun & 3 Tahun */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {renderChart(
          `Lokasi Lulusan Eksis 1 Tahun (${currentYear})`, 
          dataLulusan1Tahun, 
          true,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatan1T}
                onChange={(e) => setFilterAngkatan1T(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptions1T.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-[#1a5b28] pointer-events-none" />
            </div>
          )
        )}
        {renderChart(
          `Lokasi Lulusan Eksis 3 Tahun (${currentYear - 2}-${currentYear})`, 
          dataLulusan3Tahun, 
          true,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatan3T}
                onChange={(e) => setFilterAngkatan3T(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptions3T.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-[#1a5b28] pointer-events-none" />
            </div>
          )
        )}
      </div>

      {/* Bottom Row: Lulusan Eksis 5 Tahun */}
      <div className="grid grid-cols-1 gap-6">
        {renderChart(
          `Lokasi Lulusan Eksis 5 Tahun (${currentYear - 5}-${currentYear})`, 
          dataLulusan5Tahun, 
          true,
          (
            <div className="relative z-10 min-w-[150px]">
              <select 
                className="w-full appearance-none flex items-center gap-2 text-xs text-[#1a5b28] font-bold border border-[#2c8f42]/40 pl-3 pr-8 py-1.5 rounded-full hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#2c8f42] bg-[#eaf4ec] cursor-pointer shadow-sm"
                value={filterAngkatan5T}
                onChange={(e) => setFilterAngkatan5T(e.target.value)}
              >
                <option value="All">Semua Angkatan</option>
                {angkatanOptions5T.map(angkatan => (
                  <option key={angkatan} value={angkatan}>{angkatan}</option>
                ))}
              </select>
              <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-[#1a5b28] pointer-events-none" />
            </div>
          )
        )}
      </div>

    </div>
  );
}
