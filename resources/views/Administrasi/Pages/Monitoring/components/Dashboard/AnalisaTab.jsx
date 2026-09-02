import { PlacementChart } from "./PlacementChart";
import { TurnoverChart } from "./TurnoverChart";

export function AnalisaTab({ data = [], summaryData = [], culledData = [] }) {
  const toNumber = (value) => {
    const parsed = Number(value ?? 0);
    return Number.isFinite(parsed) ? parsed : 0;
  };

  const activeStaff = data.filter(emp => emp.STATUS === 'Eksis' || emp.STATUS === 'Aktif');
  const resignStaff = data.filter(emp => emp.STATUS === 'Terminate' || emp.STATUS === 'Resign' || emp.STATUS === 'Culled');

  const hasAngkatanValue = (emp) => {
    const value = String(emp["ANGKATAN FR ACADEMY"] ?? emp.ANGKATAN ?? emp.angkatan ?? emp["ANGKATAN"] ?? "").trim();
    if (!value || value === '-' || value.toLowerCase() === 'null' || value.toLowerCase() === 'n/a' || value.toLowerCase() === 'na') {
      return false;
    }
    return true;
  };

  const isAlumni = (emp) => hasAngkatanValue(emp);
  const isNonAlumni = (emp) => !hasAngkatanValue(emp);

  const lulusanAktif = activeStaff.filter(emp => isAlumni(emp));
  const rekrutAktif = activeStaff.filter(emp => isNonAlumni(emp));

  const lulusanResign = resignStaff.filter(emp => isAlumni(emp));
  const rekrutResign = resignStaff.filter(emp => isNonAlumni(emp));

  // The true active and resign counts MUST come directly from the employees table.
  // Using `summaryData` (from Angkatan) is incorrect here because "Lulus" from Angkatan includes people who later resigned.
  const lulusanAktifValue = lulusanAktif.length;
  const rekrutAktifValue = rekrutAktif.length;
  const totalAktifValue = lulusanAktifValue + rekrutAktifValue; // Should equal activeStaff.length (354)

  const lulusanResignValue = lulusanResign.length;
  const rekrutResignValue = rekrutResign.length;
  const totalResignValue = lulusanResignValue + rekrutResignValue; // Should equal resignStaff.length

  const pctLulusanAktif = totalAktifValue ? Math.round((lulusanAktifValue / totalAktifValue) * 100) : 0;
  const pctRekrutAktif = totalAktifValue ? Math.round((rekrutAktifValue / totalAktifValue) * 100) : 0;

  const pctLulusanResign = totalResignValue ? Math.round((lulusanResignValue / totalResignValue) * 100) : 0;
  const pctRekrutResign = totalResignValue ? Math.round((rekrutResignValue / totalResignValue) * 100) : 0;

  const summaryCards = [
    { title: "Jumlah Asst All Region (Aktif)", value: totalAktifValue.toString(), type: "single" },
    { title: "Lulusan All Region (Aktif)", value: lulusanAktifValue.toString(), percentage: `${pctLulusanAktif}%`, type: "split" },
    { title: "Asisten Rekrut All Region (Aktif)", value: rekrutAktifValue.toString(), percentage: `${pctRekrutAktif}%`, type: "split" },
    { title: "Asisten All Region (Resign)", value: totalResignValue.toString(), type: "single", color: "text-red-500" },
    { title: "Lulusan All Region (Resign)", value: lulusanResignValue.toString(), percentage: `${pctLulusanResign}%`, type: "split", color: "text-red-500" },
    { title: "Asisten Rekrut All Region (Resign)", value: rekrutResignValue.toString(), percentage: `${pctRekrutResign}%`, type: "split", color: "text-red-500" },
  ];

  // Helper function for grouping by Region
  const getRegionStats = (dataset) => {
    let riau = 0, kalbar = 0, kubar = 0, corp = 0;
    dataset.forEach(emp => {
      const region = (emp["REGION TERAKHIR"] || "").toLowerCase();
      if (region.includes("riau")) riau++;
      else if (region.includes("kalbar") || region.includes("kalimantan barat")) kalbar++;
      else if (region.includes("kubar") || region.includes("kalimantan timur")) kubar++;
      else if (region.includes("pusat") || region.includes("corp") || region.includes("jakarta")) corp++;
      // fallback or others can be ignored or added to corp depending on logic
    });
    return { riau, kalbar, kubar, corp, total: riau + kalbar + kubar + corp };
  };

  const aktifStats = getRegionStats(activeStaff);
  const resignStats = getRegionStats(resignStaff);

  return (
    <div className="flex flex-col gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500">

      {/* Summary Cards Row */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        {summaryCards.map((card, idx) => (
          <div key={idx} className="bg-[#2c8f42] rounded-2xl p-1 shadow-sm overflow-hidden flex flex-col h-full hover:scale-105 transition-transform cursor-default">
            <div className="bg-[#2c8f42] text-white text-center p-3 text-xs font-bold leading-tight flex-1 flex items-center justify-center">
              {card.title}
            </div>
            <div className="bg-white rounded-xl p-4 flex items-center justify-center gap-4">
              {card.type === "single" ? (
                <span className={`text-3xl font-extrabold ${card.color || 'text-[#2c8f42]'}`}>{card.value}</span>
              ) : (
                <>
                  <span className={`text-2xl font-extrabold ${card.color || 'text-[#2c8f42]'}`}>{card.value}</span>
                  <div className="w-px h-8 bg-gray-200"></div>
                  <span className={`text-2xl font-extrabold ${card.color || 'text-[#2c8f42]'}`}>{card.percentage}</span>
                </>
              )}
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <PlacementChart data={data} />
        <TurnoverChart summaryData={summaryData} />
      </div>

      {/* Detailed Tables Section by Angkatan */}
      <div className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mt-4 overflow-x-auto">
        <div className="min-w-[1200px]">
          <table className="w-full text-center border-collapse text-xs font-medium">
            <thead>
              <tr>
                <th className="bg-gray-800 text-white p-2 border border-gray-400 rounded-tl-xl" rowSpan={2}>Angkatan</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400" rowSpan={2}>Jumlah<br/>Awal</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400" colSpan={2}>Jumlah Culling</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400" rowSpan={2}>Tidak<br/>Lulus</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400" rowSpan={2}>Lulus</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400" rowSpan={2}>%</th>
                <th className="bg-[#0e4475] text-white p-2 border border-gray-400" colSpan={5}>Aktif</th>
                <th className="bg-[#961e1e] text-white p-2 border border-gray-400" colSpan={5}>Resign</th>
                <th className="bg-[#0b5059] text-white p-2 border border-gray-400" rowSpan={2}>Selisih</th>
                <th className="bg-[#0b5059] text-white p-2 border border-gray-400 rounded-tr-xl" rowSpan={2}>% Exist<br/>Per Angkatan</th>
              </tr>
              <tr>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400 text-[10px]">Resign<br/>In Class</th>
                <th className="bg-[#1f3f60] text-white p-2 border border-gray-400 text-[10px]">Resign<br/>OJT</th>
                {/* Aktif Headers */}
                <th className="bg-[#1464a8] text-white p-2 border border-gray-400">Riau</th>
                <th classN  ame="bg-[#1464a8] text-white p-2 border border-gray-400">Kalbar</th>
                <th className="bg-[#1464a8] text-white p-2 border border-gray-400">Kubar</th>
                <th className="bg-[#1464a8] text-white p-2 border border-gray-400">Corp</th>
                <th className="bg-[#1464a8] text-white p-2 border border-gray-400 font-bold">Total</th>
                
                {/* Resign Headers */}
                <th className="bg-[#d93838] text-white p-2 border border-gray-400">Riau</th>
                <th className="bg-[#d93838] text-white p-2 border border-gray-400">Kalbar</th>
                <th className="bg-[#d93838] text-white p-2 border border-gray-400">Kubar</th>
                <th className="bg-[#d93838] text-white p-2 border border-gray-400">Corp</th>
                <th className="bg-[#d93838] text-white p-2 border border-gray-400 font-bold">Total</th>
              </tr>
            </thead>
            <tbody className="text-gray-800 bg-gray-50/50">
              {(() => {
                const normalizeBatchName = (name) => {
                  if (!name) return "";
                  let n = String(name).trim();
                  // Convert long format to abbreviation
                  if (n.match(/Field Assistant/i)) {
                    n = n.replace(/Field Assistant\s*-\s*/i, 'FAT ');
                  } else if (n.match(/Estate Cadet Trainee/i)) {
                    n = n.replace(/Estate Cadet Trainee\s*-\s*/i, 'ECT ');
                  }
                  // Replace slashes with space
                  n = n.replace(/\//g, ' ');
                  // Normalize spaces and convert to uppercase
                  return n.replace(/\s+/g, ' ').toUpperCase();
                };

                const angkatanRows = (summaryData || []).map(angkatanItem => {
                  const batchName = String(angkatanItem.angkatan).trim();
                  const batchNameNormalized = normalizeBatchName(batchName);
                  const jumlahAwal = angkatanItem['Jumlah Awal'] || 0;
                  const lulus = angkatanItem.lulus_calculated ?? angkatanItem.lulus ?? 0;
                  const tidakLulus = angkatanItem.tidak_lulus_calculated ?? Math.max(jumlahAwal - lulus, 0);
                  
                  const activeForBatch = activeStaff.filter(emp => {
                    const empAngkatan = normalizeBatchName(emp["ANGKATAN FR ACADEMY"] || emp["ANGKATAN"] || emp.angkatan);
                    return empAngkatan === batchNameNormalized;
                  });
                  const resignForBatch = resignStaff.filter(emp => {
                    const empAngkatan = normalizeBatchName(emp["ANGKATAN FR ACADEMY"] || emp["ANGKATAN"] || emp.angkatan);
                    return empAngkatan === batchNameNormalized;
                  });
              
                  const aktifStats = getRegionStats(activeForBatch);
                  const resignStats = getRegionStats(resignForBatch);
                  
                  const resignInClass = Number(angkatanItem.Resign_In_Class ?? angkatanItem.resign_in_class ?? angkatanItem.resignInClass ?? angkatanItem.resign_in_class_calculated ?? 0) || 0;
                  const resignOjt = Number(angkatanItem.Resign_OJT ?? angkatanItem.resign_ojt ?? angkatanItem.resignOJT ?? angkatanItem.resign_ojt_calculated ?? 0) || 0;
                  const totalResignIncludingCulled = resignStats.total + resignInClass + resignOjt;
              
                  const selisih = jumlahAwal - (aktifStats.total + totalResignIncludingCulled);
                  const pctExist = jumlahAwal > 0 ? Math.round((aktifStats.total / jumlahAwal) * 100) : 0;
              
                  const pctLulus = jumlahAwal > 0 ? ((lulus / jumlahAwal) * 100).toFixed(2) : 0;

                  return { 
                    batchName, 
                    batchNameNormalized,
                    aktifStats, 
                    resignStats, 
                    selisih, 
                    pctExist, 
                    jumlahAwal, 
                    lulus, 
                    tidakLulus,
                    resignInClass,
                    resignOjt,
                    pctLulus,
                    totalResignIncludingCulled
                  };
                });

                angkatanRows.sort((a, b) => {
                  const getYearAndNumeral = (name) => {
                    const yearMatch = name.match(/\b(20\d{2})\b/);
                    const romanMatch = name.match(/\b(I|II|III|IV|V|VI)\b/);
                    
                    const year = yearMatch ? parseInt(yearMatch[1], 10) : 0;
                    
                    const romanValues = { 'I': 1, 'II': 2, 'III': 3, 'IV': 4, 'V': 5, 'VI': 6 };
                    const roman = romanMatch ? romanValues[romanMatch[1]] : 0;
                    
                    return { year, roman };
                  };
                  
                  const valA = getYearAndNumeral(a.batchName);
                  const valB = getYearAndNumeral(b.batchName);
                  
                  if (valA.year !== valB.year) {
                    return valA.year - valB.year; // Oldest year first
                  }
                  return valA.roman - valB.roman; // I before II
                });

                const grandTotal = {
                  aktif: { riau: 0, kalbar: 0, kubar: 0, corp: 0, total: 0 },
                  resign: { riau: 0, kalbar: 0, kubar: 0, corp: 0, total: 0 },
                  culled: 0,
                  selisih: 0,
                  totalJumlahAwal: 0,
                  totalLulus: 0,
                  totalTidakLulus: 0,
                  totalResignInClass: 0,
                  totalResignOjt: 0
                };

                angkatanRows.forEach(row => {
                  ['riau', 'kalbar', 'kubar', 'corp', 'total'].forEach(key => {
                    grandTotal.aktif[key] += row.aktifStats[key];
                    grandTotal.resign[key] += row.resignStats[key];
                  });
                  grandTotal.culled += (row.resignInClass || 0) + (row.resignOjt || 0);
                  grandTotal.selisih += row.selisih;
                  grandTotal.totalJumlahAwal += row.jumlahAwal;
                  grandTotal.totalLulus += row.lulus;
                  grandTotal.totalTidakLulus += row.tidakLulus;
                  grandTotal.totalResignInClass += row.resignInClass;
                  grandTotal.totalResignOjt += row.resignOjt;
                });

                const grandPctExist = grandTotal.totalJumlahAwal > 0 
                  ? ((grandTotal.aktif.total / grandTotal.totalJumlahAwal) * 100).toFixed(2)
                  : 0;
                  
                const grandPctLulus = grandTotal.totalJumlahAwal > 0 
                  ? ((grandTotal.totalLulus / grandTotal.totalJumlahAwal) * 100).toFixed(2)
                  : 0;

                return (
                  <>
                    {angkatanRows.map((row, idx) => (
                      <tr key={idx} className="hover:bg-gray-100 transition-colors bg-white">
                        <td className="border border-gray-300 p-2 font-bold text-gray-700 bg-gray-50">{row.batchNameNormalized}</td>
                        <td className="border border-gray-300 p-2 font-bold text-gray-700 bg-gray-50/50">{row.jumlahAwal}</td>
                        <td className="border border-gray-300 p-2 text-gray-600">{row.resignInClass}</td>
                        <td className="border border-gray-300 p-2 text-gray-600">{row.resignOjt}</td>
                        <td className="border border-gray-300 p-2 font-bold text-red-600 bg-red-50/30">{row.tidakLulus}</td>
                        <td className="border border-gray-300 p-2 font-bold text-green-700 bg-green-50/30">{row.lulus}</td>
                        <td className="border border-gray-300 p-2 font-bold text-blue-700">{row.pctLulus}</td>
                        
                        {/* Aktif */}
                        <td className="border border-gray-300 p-2">{row.aktifStats.riau}</td>
                        <td className="border border-gray-300 p-2">{row.aktifStats.kalbar}</td>
                        <td className="border border-gray-300 p-2">{row.aktifStats.kubar}</td>
                        <td className="border border-gray-300 p-2">{row.aktifStats.corp}</td>
                        <td className="border border-gray-300 p-2 font-bold bg-blue-50/50">{row.aktifStats.total}</td>
                        {/* Resign */}
                        <td className="border border-gray-300 p-2">{row.resignStats.riau}</td>
                        <td className="border border-gray-300 p-2">{row.resignStats.kalbar}</td>
                        <td className="border border-gray-300 p-2">{row.resignStats.kubar}</td>
                        <td className="border border-gray-300 p-2">{row.resignStats.corp}</td>
                        <td className="border border-gray-300 p-2 font-bold bg-red-50/50" title={`Resign: ${row.resignStats.total}, Culled: ${row.culledForBatch}`}>{row.totalResignIncludingCulled}</td>
                        {/* Summary */}
                        <td className="border border-gray-300 p-2 font-bold">{row.selisih}</td>
                        <td className="border border-gray-300 p-2 font-bold text-[#0b5059]">{row.pctExist}%</td>
                      </tr>
                    ))}
                    <tr className="bg-gray-200/80 font-bold text-gray-800 text-sm">
                      <td className="border border-gray-400 p-3 rounded-bl-xl">Total</td>
                      <td className="border border-gray-400 p-3">{grandTotal.totalJumlahAwal}</td>
                      <td className="border border-gray-400 p-3 text-gray-700">{grandTotal.totalResignInClass}</td>
                      <td className="border border-gray-400 p-3 text-gray-700">{grandTotal.totalResignOjt}</td>
                      <td className="border border-gray-400 p-3 text-red-700">{grandTotal.totalTidakLulus}</td>
                      <td className="border border-gray-400 p-3 text-green-800">{grandTotal.totalLulus}</td>
                      <td className="border border-gray-400 p-3 text-blue-800">{grandPctLulus}</td>
                      
                      {/* Aktif Total */}
                      <td className="border border-gray-400 p-3">{grandTotal.aktif.riau}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.aktif.kalbar}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.aktif.kubar}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.aktif.corp}</td>
                      <td className="border border-gray-400 p-3 bg-blue-100">{grandTotal.aktif.total}</td>
                      {/* Resign Total */}
                      <td className="border border-gray-400 p-3">{grandTotal.resign.riau}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.resign.kalbar}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.resign.kubar}</td>
                      <td className="border border-gray-400 p-3">{grandTotal.resign.corp}</td>
                      <td className="border border-gray-400 p-3 bg-red-100" title={`Resign: ${grandTotal.resign.total}, Culled: ${grandTotal.culled}`}>{grandTotal.resign.total + grandTotal.culled}</td>
                      {/* Summary Total */}
                      <td className="border border-gray-400 p-3">{grandTotal.selisih}</td>
                      <td className="border border-gray-400 p-3 rounded-br-xl text-[#0b5059]">{grandPctExist}%</td>
                    </tr>
                  </>
                );
              })()}
            </tbody>
          </table>
        </div>
      </div>
      
    </div>
  );
}
