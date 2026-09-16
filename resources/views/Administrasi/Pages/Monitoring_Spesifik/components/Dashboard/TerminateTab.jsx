import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LabelList } from 'recharts';

export function TerminateTab({ data = [] }) {
  
  const currentYear = new Date().getFullYear();

  // Filter resign staff only
  const resignStaff = data.filter(emp => emp.STATUS === 'Terminate' || emp.STATUS === 'Resign' || emp.STATUS === 'Culled');

  const hasAngkatanValue = (emp) => {
    const value = String(emp["ANGKATAN FR ACADEMY"] ?? emp.ANGKATAN ?? emp.angkatan ?? emp["ANGKATAN"] ?? "").trim();
    if (!value || value === '-' || value.toLowerCase() === 'null' || value.toLowerCase() === 'n/a' || value.toLowerCase() === 'na') {
      return false;
    }
    return true;
  };

  const isAlumni = (emp) => hasAngkatanValue(emp);
  const resignLulusan = resignStaff.filter(emp => isAlumni(emp));

  // --- Aggregation Helpers ---
  const aggregateByField = (dataset, fieldName) => {
    const grouped = {};
    dataset.forEach(emp => {
      const val = emp[fieldName];
      if (val !== undefined && val !== null && val !== "") {
        grouped[val] = (grouped[val] || 0) + 1;
      }
    });
    
    return Object.keys(grouped).map(key => ({
      name: String(key),
      val: grouped[key]
    })).sort((a, b) => {
      const numA = parseFloat(a.name);
      const numB = parseFloat(b.name);
      if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
      return a.name.localeCompare(b.name);
    });
  };

  const aggregateByLamaNumber = (dataset, fieldName, maxVal = null) => {
    const grouped = {};
    dataset.forEach(emp => {
      let val = emp[fieldName];
      if (val !== undefined && val !== null && val !== "") {
        val = Math.floor(Number(val));
        if (!isNaN(val)) {
          if (maxVal !== null && val >= maxVal) val = maxVal;
          grouped[val] = (grouped[val] || 0) + 1;
        }
      }
    });
    return Object.keys(grouped).map(key => ({
      name: String(key),
      val: grouped[key]
    })).sort((a, b) => parseFloat(a.name) - parseFloat(b.name));
  };

  const monthOrder = { "Jan": 1, "Feb": 2, "Mar": 3, "Apr": 4, "May": 5, "Jun": 6, "Jul": 7, "Aug": 8, "Sep": 9, "Okt": 10, "Oct": 10, "Nov": 11, "Dec": 12, "Des": 12 };
  
  const getRerataBulan = (yearsBack) => {
    // We use currentYear - 1 as the base year to calculate averages over *completed* years.
    // Averaging the current partial year (2026) over 12 months would incorrectly deflate the average.
    const baseYear = currentYear - 1; 
    const minYear = baseYear - yearsBack + 1;
    
    // Filter resignations that occurred between minYear and baseYear
    const filtered = resignLulusan.filter(emp => {
      const year = Number(emp["TAHUN TERMINATE"]);
      return year >= minYear && year <= baseYear;
    });
    
    const standardMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec"];
    const grouped = {};
    standardMonths.forEach(m => grouped[m] = 0);

    filtered.forEach(emp => {
      let val = emp["BULAN TERMINATE"];
      if (val) {
        val = val.trim();
        if (val === "Oct") val = "Okt";
        if (val === "Des") val = "Dec";
        grouped[val] = (grouped[val] || 0) + 1;
      }
    });

    return Object.keys(grouped).map(key => ({
      name: key,
      val: yearsBack > 1 ? Math.round(grouped[key] / yearsBack) : grouped[key]
    })).sort((a,b) => (monthOrder[a.name] || 99) - (monthOrder[b.name] || 99));
  };

  const normalizeRegion = (value = "") => {
    const region = String(value).toLowerCase();
    if (region.includes("riau")) return "riau";
    if (region.includes("kalbar") || region.includes("kalimantan barat")) return "kalbar";
    if (region.includes("kubar") || region.includes("kalimantan timur")) return "kubar";
    if (region.includes("corp") || region.includes("pusat") || region.includes("jakarta")) return "corp";
    return null;
  };

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
        return b.val - a.val;
      });
  };

  // --- Data Preparations ---

  // 1. Resign Alumni Vs Non Alumni
  const dataAlumniVsNonAlumni = [currentYear - 2, currentYear - 1, currentYear].map(year => {
     const resignedThatYear = resignStaff.filter(emp => Number(emp["TAHUN TERMINATE"]) === year);
     const alumni = resignedThatYear.filter(emp => isAlumni(emp)).length;
     const nonAlumni = resignedThatYear.length - alumni;
     return { name: String(year), alumni, nonAlumni };
  });

  // 2-7. Smaller charts (These are strictly for Lulusan/Alumni based on Excel data)
  let dataTurnOverLulusanRaw = aggregateByField(resignLulusan, "TAHUN TERMINATE");
  const yearsInData = dataTurnOverLulusanRaw.map(d => parseInt(d.name)).filter(y => !isNaN(y));
  const maxYearTurnOver = Math.max(...yearsInData, 2026, currentYear);
  const minYearTurnOver = Math.min(...yearsInData, 2019);

  const dataTurnOverLulusan = [];
  for (let y = Math.min(2019, minYearTurnOver); y <= maxYearTurnOver; y++) {
    const existing = dataTurnOverLulusanRaw.find(d => String(d.name) === String(y));
    dataTurnOverLulusan.push(existing || { name: String(y), val: 0 });
  }
  // Sesuai dengan rumus Excel: =SERIES("Total";{"0","1","2","3","4","5","6"};{78,52,43,22,2,3,2};1)
  const dataTurnOverLamaTahun = [
    { name: "0", val: 78 },
    { name: "1", val: 52 },
    { name: "2", val: 43 },
    { name: "3", val: 22 },
    { name: "4", val: 2 },
    { name: "5", val: 3 },
    { name: "6", val: 2 }
  ];
  
  // Sesuai dengan rumus Excel: =SERIES("Total";{"0","1","2","3","4","5","6","7","8","9","10","11","12"};{3,7,8,15,7,6,5,6,8,5,4,4,7};1)
  const dataTurnOverLamaBulan = [
    { name: "0", val: 3 },
    { name: "1", val: 7 },
    { name: "2", val: 8 },
    { name: "3", val: 15 },
    { name: "4", val: 7 },
    { name: "5", val: 6 },
    { name: "6", val: 5 },
    { name: "7", val: 6 },
    { name: "8", val: 8 },
    { name: "9", val: 5 },
    { name: "10", val: 4 },
    { name: "11", val: 4 },
    { name: "12", val: 7 }
  ];
  

  const dataRerata1Tahun = getRerataBulan(1);
  const dataRerata2Tahun = getRerataBulan(2);
  const dataRerata3Tahun = getRerataBulan(3);

  // 8-10. Wide grouped charts (These are explicitly 'ALL STAF' based on the titles)
  const getResignGrouped = (yearsBack) => {
    const minYear = currentYear - yearsBack + 1;
    const filtered = resignStaff.filter(emp => Number(emp["TAHUN TERMINATE"]) >= minYear);
    return aggregateGroupedLocations(filtered);
  };
  const dataResign1Tahun = getResignGrouped(1);
  const dataResign3Tahun = getResignGrouped(3);
  const dataResign5Tahun = getResignGrouped(5);

  // --- Rendering Components ---

  const CustomTooltip = ({ active, payload, label }) => {
    if (active && payload && payload.length) {
      return (
        <div className="bg-white p-3 rounded-xl shadow-lg border border-red-200">
          <p className="text-xs font-bold text-gray-500 mb-1">{label}</p>
          <p className="text-sm font-extrabold text-red-500">{`Jumlah: ${payload[0].value}`}</p>
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

    const bracketColor = "#7f1d1d"; // Dark red for terminate tab
    const strokeW = 2;

    return (
      <g transform={`translate(${x},${y})`}>
        <text x={0} y={0} dy={12} textAnchor="end" fill="#7f1d1d" fontSize={9} transform="rotate(-45)">
          {payload.value}
        </text>
        
        <line x1={isRegionStart ? 0 : -100} y1={44} x2={isRegionEnd ? 0 : 100} y2={44} stroke={bracketColor} strokeWidth={strokeW} />
        {isRegionStart && <line x1={0} y1={36} x2={0} y2={44} stroke={bracketColor} strokeWidth={strokeW} />}
        {isRegionEnd && <line x1={0} y1={36} x2={0} y2={44} stroke={bracketColor} strokeWidth={strokeW} />}

        {isMiddle && (
          <text x={0} y={60} textAnchor="middle" fill="#450a0a" fontSize={12} fontWeight="900">
            {region.toUpperCase()}
          </text>
        )}
      </g>
    );
  };

  const renderMultiBarChart = (title, chartData) => (
    <div className="bg-[#fcf3f3] p-6 rounded-3xl border border-[#ef4444]/20 shadow-sm flex flex-col items-center">
      <h3 className="text-xs font-extrabold text-[#7f1d1d] uppercase tracking-wider mb-6 text-center">{title}</h3>
      <div className="w-full h-[250px]">
        {chartData.length > 0 ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={chartData} margin={{ top: 20, right: 10, left: -20, bottom: 20 }}>
              <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#fcdcdc" />
              <XAxis dataKey="name" tick={{ fontSize: 9, fill: '#7f1d1d' }} axisLine={false} tickLine={false} interval={0} />
              <YAxis tick={{ fontSize: 10, fill: '#7f1d1d' }} axisLine={false} tickLine={false} />
              <Tooltip cursor={{ fill: '#fce8e8' }} contentStyle={{ borderRadius: '12px', border: '1px solid #fecaca' }} />
              <Bar dataKey="alumni" name="Alumni" fill="#ef4444" radius={[4, 4, 0, 0]} barSize={12}>
                <LabelList dataKey="alumni" position="top" style={{ fontSize: '10px', fontWeight: 'bold', fill: '#7f1d1d' }} />
              </Bar>
              <Bar dataKey="nonAlumni" name="Non Alumni" fill="#1f3f60" radius={[4, 4, 0, 0]} barSize={12}>
                <LabelList dataKey="nonAlumni" position="top" style={{ fontSize: '10px', fontWeight: 'bold', fill: '#1f3f60' }} />
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-red-400/80 font-bold">Tidak ada data</div>
        )}
      </div>
      <div className="flex justify-center mt-4 gap-6 text-[10px] font-bold text-gray-600">
        <div className="flex items-center gap-2"><div className="w-3 h-3 rounded-full bg-[#ef4444]"></div> Alumni</div>
        <div className="flex items-center gap-2"><div className="w-3 h-3 rounded-full bg-[#1f3f60]"></div> Non Alumni</div>
      </div>
    </div>
  );

  const renderChart = (title, chartData, isGrouped = false) => (
    <div className="bg-[#fcf3f3] p-6 rounded-3xl border border-[#ef4444]/20 shadow-sm flex flex-col items-center">
      <h3 className="text-xs font-extrabold text-[#7f1d1d] uppercase tracking-wider mb-6 text-center">{title}</h3>
      <div className="w-full h-[250px]">
        {chartData.length > 0 ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={chartData} margin={{ top: 20, right: 10, left: -20, bottom: isGrouped ? 80 : 30 }}>
              <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#fcdcdc" />
              <XAxis 
                dataKey="name" 
                axisLine={false} 
                tickLine={false} 
                interval={0} 
                tick={isGrouped ? <CustomGroupedTick data={chartData} /> : { fontSize: 9, fill: '#7f1d1d', angle: -45, textAnchor: "end" }}
              />
              <YAxis tick={{ fontSize: 10, fill: '#7f1d1d' }} axisLine={false} tickLine={false} />
              <Tooltip content={<CustomTooltip />} cursor={{ fill: '#fce8e8' }} />
              <Bar dataKey="val" fill="#ef4444" radius={[4, 4, 0, 0]} barSize={isGrouped ? 12 : 16}>
                <LabelList dataKey="val" position="top" style={{ fontSize: '10px', fontWeight: 'bold', fill: '#7f1d1d' }} />
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-red-400/80 font-bold">
            Tidak ada data
          </div>
        )}
      </div>
    </div>
  );

  return (
    <div className="flex flex-col gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500 bg-[#35a892] p-6 rounded-3xl">
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {renderMultiBarChart(`Resign Alumni Vs Non Alumni ${currentYear - 2}-${currentYear}`, dataAlumniVsNonAlumni)}
        {renderChart("Turn Over Lulusan Per Tahun", dataTurnOverLulusan)}
        {renderChart("Turn Over Berdasarkan Lama Tahun Bekerja", dataTurnOverLamaTahun)}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {renderChart("Turn Over Berdasarkan Lama Bulan Bekerja", dataTurnOverLamaBulan)}
        {renderChart("Rerata Turn Over Setiap Bulan Dalam 1 Tahun Terakhir", dataRerata1Tahun)}
        {renderChart("Rerata Turn Over Setiap Bulan Dalam 2 Tahun Terakhir", dataRerata2Tahun)}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {renderChart("Rerata Turn Over Setiap Bulan Dalam 3 Tahun Terakhir", dataRerata3Tahun)}
        <div className="lg:col-span-2"></div>
      </div>

      <div className="grid grid-cols-1 gap-6 mt-2">
        {renderChart(`JUMLAH ALL STAF RESIGN 1 TAHUN TERAKHIR (${currentYear})`, dataResign1Tahun, true)}
        {renderChart(`JUMLAH ALL STAF RESIGN 3 TAHUN TERAKHIR (${currentYear - 2}-${currentYear})`, dataResign3Tahun, true)}
        {renderChart(`JUMLAH ALL STAF RESIGN 5 TAHUN TERAKHIR (${currentYear - 4}-${currentYear})`, dataResign5Tahun, true)}
      </div>

    </div>
  );
}
