import { useState } from "react";
import { Edit2 } from "lucide-react";
import { AfdelingFormModal } from "./AfdelingFormModal";

const REGIONS = ["Riau", "Kalbar", "Kubar", "Corp"];

const normalizeRegion = (value = "") => {
  const region = String(value).toLowerCase();
  if (region.includes("riau")) return "Riau";
  if (region.includes("kalbar") || region.includes("kalimantan barat")) return "Kalbar";
  if (region.includes("kubar") || region.includes("kalimantan timur")) return "Kubar";
  if (region.includes("corp") || region.includes("pusat") || region.includes("jakarta")) return "Corp";
  return null;
};

const isActive = (employee) => employee.STATUS === "Eksis" || employee.STATUS === "Aktif";
const normalizeText = (value = "") => String(value ?? "").trim().toLowerCase();
const getAlasanValue = (employee) => {
  const keys = [
    "alasan",
    "Alasan",
    "ALASAN",
    "Alasan Resign",
    "ALASAN RESIGN",
    "alasan_resign",
    "alasanResign",
    "alasan resign",
  ];

  for (const key of keys) {
    const value = employee[key];
    if (value !== undefined && value !== null && String(value).trim() !== "") {
      return String(value).trim();
    }
  }

  return "";
};
const isTidakLulus = (employee) => {
  const status = normalizeText(employee.STATUS);
  const reason = normalizeText(getAlasanValue(employee));

  if (status === "tidak lulus") return true;
  if (reason.includes("tidak lulus evaluasi")) return true;
  if (status === "culled" && reason.includes("tidak lulus")) return true;

  return false;
};
const isGraduate = (employee) => Boolean(employee.ALUMNI || employee.KATEGORI?.toLowerCase().includes("alumni"));
const percentageOf = (total, value) => value > 0 ? `${Math.round((total / value) * 100)}%` : "-";
const getProgramName = (employee) => String(employee["ANGKATAN FR ACADEMY"] || employee["ANGKATAN"] || employee.angkatan || "Tidak diketahui").trim() || "Tidak diketahui";

const normalizeBatchName = (name) => {
  if (!name) return "";
  let n = String(name).trim();
  if (n.match(/Field Assistant/i)) n = n.replace(/Field Assistant\s*-\s*/i, 'FAT ');
  else if (n.match(/Estate Cadet Trainee/i)) n = n.replace(/Estate Cadet Trainee\s*-\s*/i, 'ECT ');
  n = n.replace(/\//g, ' ');
  return n.replace(/\s+/g, ' ').toUpperCase();
};

function TrainingKpi({ data = [], summaryData = [], culledData = [] }) {
  const getProgramYear = (value) => {
    const match = String(value || "").match(/(?:19|20)\d{2}/);
    return match ? Number(match[0]) : null;
  };

  const summaryRows = (summaryData || [])
    .map((row) => {
      const rawYear = row.angkatan ?? row.Angkatan ?? row.program ?? row["ANGKATAN FR ACADEMY"] ?? "";
      const year = getProgramYear(rawYear);
      const total = Number(row["Jumlah Awal"] ?? row.jumlah_awal ?? row.total ?? row.Total ?? 0) || 0;
      const lulus = Number(row.lulus_calculated ?? row.lulus ?? row.Lulus ?? 0) || 0;
      const tidakLulus = Number(row.tidak_lulus ?? row.tidakLulus ?? row.tidak_lulus_calculated ?? row["Tidak Lulus"] ?? 0) || 0;
      const resignInClass = Number(row.Resign_In_Class ?? row.resign_in_class ?? row.resignInClass ?? row.resign_in_class_calculated ?? 0) || 0;
      const resignOjt = Number(row.Resign_OJT ?? row.resign_ojt ?? row.resignOJT ?? row.resign_ojt_calculated ?? 0) || 0;

      return {
        year,
        total,
        lulus,
        tidakLulus,
        resignInClass,
        resignOjt,
      };
    })
    .filter((row) => row.year !== null);

  const employeeRows = (data || [])
    .map((employee) => {
      const year = getProgramYear(employee["ANGKATAN FR ACADEMY"] || employee["ANGKATAN"] || employee.angkatan);
      const months = Number(employee["LAMA BEKERJA (BULAN)"]) || 0;
      const isResign = !isActive(employee);

      return {
        year,
        months,
        isResign,
        isTidakLulus: isTidakLulus(employee),
      };
    })
    .filter((row) => row.year !== null);

  const latestProgramYear = summaryRows.length
    ? Math.max(...summaryRows.map((row) => row.year))
    : employeeRows.length
      ? Math.max(...employeeRows.map((row) => row.year))
      : new Date().getFullYear();

  const rows = [1, 2, 3, 4, 5].map((years) => {
    const start = latestProgramYear - years;
    const summaryFiltered = summaryRows.filter((row) => row.year >= start && row.year <= latestProgramYear);
    const employeeFiltered = employeeRows.filter((row) => row.year >= start && row.year <= latestProgramYear);
    
    const total = summaryFiltered.reduce((sum, row) => sum + row.total, 0);
    const lulus = summaryFiltered.reduce((sum, row) => sum + row.lulus, 0);
    const tidakLulus = summaryFiltered.reduce((sum, row) => sum + row.tidakLulus, 0);
    const resignInClass = summaryFiltered.reduce((sum, row) => sum + row.resignInClass, 0);
    const resignOjt = summaryFiltered.reduce((sum, row) => sum + row.resignOjt, 0);

    return {
      period: `${years} tahun terakhir`,
      total,
      resignInClass,
      resignOjt,
      tidakLulus,
      lulus,
      percent: total ? `${((lulus / total) * 100).toFixed(2).replace(/\./g, ",")}%` : "-",
      resignInClassRate: total ? `${((resignInClass / total) * 100).toFixed(2).replace(/\./g, ",")}%` : "-",
      resignOjtRate: total ? `${((resignOjt / total) * 100).toFixed(2).replace(/\./g, ",")}%` : "-",
      tidakLulusRate: total ? `${((tidakLulus / total) * 100).toFixed(2).replace(/\./g, ",")}%` : "-",
    };
  });

  return (
    <div className="mb-6 overflow-x-auto">
      <table className="w-full min-w-[900px] border-collapse text-sm">
        <thead>
          <tr className="bg-[#078c91] text-white">
            <th className="border border-gray-800 px-3 py-2 text-left">Periode</th>
            <th className="border border-gray-800 px-3 py-2">Jumlah</th>
            <th className="border border-gray-800 px-3 py-2">Resign In Class</th>
            <th className="border border-gray-800 px-3 py-2">Resign OJT</th>
            <th className="border border-gray-800 px-3 py-2">Tidak Lulus</th>
            <th className="border border-gray-800 px-3 py-2">Lulus</th>
            <th className="border border-gray-800 px-3 py-2">%</th>
          </tr>
        </thead>
        <tbody>
          {rows.length > 0 ? rows.map((row) => (
            <tr key={row.period} className="even:bg-gray-50">
              <td className="border border-gray-800 px-3 py-2 font-bold">{row.period}</td>
              <td className="border border-gray-800 px-3 py-2 text-center font-bold">{row.total}</td>
              <td className="border border-gray-800 px-3 py-2 text-center">{row.resignInClassRate}</td>
              <td className="border border-gray-800 px-3 py-2 text-center">{row.resignOjtRate}</td>
              <td className="border border-gray-800 px-3 py-2 text-center text-red-600 font-bold">{row.tidakLulusRate}</td>
              <td className="border border-gray-800 px-3 py-2 text-center text-green-700 font-bold">{row.lulus}</td>
              <td className="border border-gray-800 px-3 py-2 text-center font-bold">{row.percent}</td>
            </tr>
          )) : (
            <tr><td colSpan={7} className="border border-gray-800 px-3 py-4 text-center text-gray-500">Belum ada data training</td></tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

export function RegionalKpi({ data = [], summaryData = [], afdelingData = [], culledData = [], onUpdateAfdeling }) {
  const [isAfdelingModalOpen, setIsAfdelingModalOpen] = useState(false);

  const validBatchNames = new Set(
    (summaryData || [])
      .map(row => normalizeBatchName(row.angkatan ?? row.Angkatan ?? row.program ?? ""))
      .filter(Boolean)
  );

  const validEmployees = data.filter((employee) => {
    const program = getProgramName(employee);
    return program !== "Tidak diketahui" && validBatchNames.has(normalizeBatchName(program));
  });

  const stats = REGIONS.reduce((result, region) => {
    result[region] = { allocation: 0, locations: new Set(), active: 0, resigned: 0, graduates: 0 };
    return result;
  }, {});

  validEmployees.forEach((employee) => {
    const region = normalizeRegion(employee["REGION TERAKHIR"] || employee["REGION AWAL PENEMPATAN"]);
    if (!region) return;

    const regionStats = stats[region];
    regionStats.allocation += 1;
    if (employee["LOKASI TERAKHIR"]) regionStats.locations.add(employee["LOKASI TERAKHIR"]);
    if (isActive(employee)) regionStats.active += 1;
    else regionStats.resigned += 1;
    if (isGraduate(employee)) regionStats.graduates += 1;
  });

  const defaultAfdeling = {
    Riau: 166,
    Kalbar: 99,
    Kubar: 37,
    Corp: 0
  };

  const hasAfdelingData = Array.isArray(afdelingData) && afdelingData.length > 0;
  const afdelingMap = hasAfdelingData
    ? afdelingData.reduce((acc, item) => {
        const reg = normalizeRegion(item.Region || item.region || item.REGION);
        if (reg) {
          acc[reg] = (acc[reg] || 0) + (Number(item.Total ?? item.total ?? 0) || 0);
        }
        return acc;
      }, { Riau: 0, Kalbar: 0, Kubar: 0, Corp: 0 })
    : defaultAfdeling;

  const totalAfdeling = hasAfdelingData
    ? Object.values(afdelingMap).reduce((sum, val) => sum + val, 0)
    : 302;

  const rows = REGIONS.map((region) => {
    const regionStats = stats[region];
    const activeTotal = regionStats.active;
    const resignedTotal = regionStats.resigned;
    const total = activeTotal + resignedTotal;
    const departments = afdelingMap[region] || 0;
    return {
      region,
      allocation: activeTotal + resignedTotal, // Total Aktif (per region) + Total Resign (per region)
      departments,
      existing: total ? Math.round((activeTotal / total) * 100) : 0,
      resigned: total ? Math.round((resignedTotal / total) * 100) : 0,
      graduateFill: departments ? Math.round((activeTotal / departments) * 100) : 0,
    };
  });

  const totalActive = validEmployees.filter(isActive).length;
  const totalResigned = validEmployees.length - totalActive;
  const totalAllocation = totalActive + totalResigned; // Total Aktif (all region) + Total Resign (all region)

  const total = {
    region: "Total",
    allocation: totalAllocation,
    departments: totalAfdeling,
    existing: totalAllocation ? Math.round((totalActive / totalAllocation) * 100) : 0,
    resigned: totalAllocation ? Math.round((totalResigned / totalAllocation) * 100) : 0,
    graduateFill: totalAfdeling ? Math.round((totalActive / totalAfdeling) * 100) : 0,
  };

  const cells = [...rows, total];
  const metrics = [
    { label: "Jumlah Alokasi trainee per region", key: "allocation", format: (value) => value },
    { label: "Total afdeling per region", key: "departments", format: (value) => value },
    { label: "Persentasi existing dan resign", key: "status", format: (value) => `${value.existing}% / ${value.resigned}%` },
    { label: "Persentasi keterisian lulusan", key: "graduateFill", format: (value) => `${value}%` },
  ];

  const getProgramYear = (value) => {
    const match = String(value || "").match(/(?:19|20)\d{2}/);
    return match ? Number(match[0]) : null;
  };

  const buildRegionPeriodStats = (kind) => {
    const periodYears = [1, 2, 3, 4, 5];
    const latestProgramYear = data
      .map((employee) => getProgramYear(employee["ANGKATAN FR ACADEMY"] || employee["ANGKATAN"] || employee.angkatan))
      .filter(Boolean)
      .reduce((max, value) => Math.max(max, value), new Date().getFullYear());

    return periodYears.map((years) => {
      const startYear = latestProgramYear - years;
      const regionTotals = {
        Riau: { active: 0, resign: 0 },
        Kalbar: { active: 0, resign: 0 },
        Kubar: { active: 0, resign: 0 },
        Corp: { active: 0, resign: 0 },
        Total: { active: 0, resign: 0 },
      };

      data.forEach((employee) => {
        const year = getProgramYear(employee["ANGKATAN FR ACADEMY"] || employee["ANGKATAN"] || employee.angkatan);
        if (!year || year < startYear || year > latestProgramYear) return;

        const region = normalizeRegion(employee["REGION TERAKHIR"] || employee["REGION AWAL PENEMPATAN"]);
        if (!region) return;

        if (isActive(employee)) {
          regionTotals[region].active += 1;
          regionTotals.Total.active += 1;
        } else {
          regionTotals[region].resign += 1;
          regionTotals.Total.resign += 1;
        }
      });

      const regionEntries = ["Riau", "Kalbar", "Kubar", "Corp", "Total"].reduce((acc, region) => {
        const total = regionTotals[region].active + regionTotals[region].resign;
        const value = kind === "eksis"
          ? total ? (regionTotals[region].active / total) * 100 : 0
          : total ? (regionTotals[region].resign / total) * 100 : 0;

        acc[region] = Number.isFinite(value) ? Number(value.toFixed(2)) : 0;
        return acc;
      }, {});

      return {
        period: `${years} tahun terakhir`,
        ...regionEntries,
      };
    });
  };

  const eksisRows = buildRegionPeriodStats("eksis");
  const resignRows = buildRegionPeriodStats("resign");

  const renderRegionTable = (title, rows, colorClass) => (
    <div className="mb-6 overflow-x-auto">
      <table className="w-full min-w-[800px] border-collapse text-sm shadow-sm rounded-lg overflow-hidden">
        <thead>
          <tr className="bg-[#078c91] text-white">
            <th className="border border-gray-800 px-3 py-2 text-left" colSpan={6}>{title}</th>
          </tr>
          <tr className="bg-[#078c91] text-white">
            <th className="border border-gray-800 px-3 py-2 text-left">Periode</th>
            <th className="border border-gray-800 px-3 py-2">Riau</th>
            <th className="border border-gray-800 px-3 py-2">Kalbar</th>
            <th className="border border-gray-800 px-3 py-2">Kubar</th>
            <th className="border border-gray-800 px-3 py-2">Corp</th>
            <th className="border border-gray-800 px-3 py-2">Total</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.period} className="even:bg-gray-50">
              <td className="border border-gray-800 px-3 py-2 font-bold">{row.period}</td>
              {['Riau', 'Kalbar', 'Kubar', 'Corp', 'Total'].map((region) => (
                <td key={`${row.period}-${region}`} className={`border border-gray-800 px-3 py-2 text-center font-bold ${colorClass}`}>
                  {row[region]}%
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );

  return (
    <section className="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div className="px-6 py-4 border-b border-gray-100">
     </div>

      <div className="overflow-x-auto border-b border-gray-100">
        <table className="w-full min-w-[760px] border-collapse text-sm">
          <thead>
            <tr className="bg-[#244f6d] text-white">
              <th className="text-left px-5 py-3 font-bold">Indikator</th>
              {cells.map((cell) => <th key={cell.region} className="px-4 py-3 font-bold text-center">{cell.region}</th>)}
              {onUpdateAfdeling && <th className="px-4 py-3 font-bold text-center">Aksi</th>}
            </tr>
          </thead>
          <tbody>
            {metrics.map((metric, index) => (
              <tr key={metric.label} className={index % 2 ? "bg-gray-50" : "bg-white"}>
                <td className="px-5 py-3 font-semibold text-gray-700">{metric.label}</td>
                {cells.map((cell) => {
                  const value = metric.key === "status" ? cell : cell[metric.key];
                  return <td key={`${metric.label}-${cell.region}`} className="px-4 py-3 text-center font-bold text-gray-800">{metric.format(value)}</td>;
                })}
                {onUpdateAfdeling && (
                  <td className="px-4 py-3 text-center">
                    {metric.key === "departments" ? (
                      <button
                        onClick={() => setIsAfdelingModalOpen(true)}
                        className="inline-flex items-center gap-1.5 px-3 py-1 bg-[#2c8f42] hover:bg-[#237335] text-white rounded-lg text-xs font-bold transition-all shadow-sm shadow-green-900/10 cursor-pointer"
                        title="Edit Total Afdeling per Region"
                      >
                        <Edit2 size={13} />
                        Edit
                      </button>
                    ) : (
                      <span className="text-gray-300 font-bold">-</span>
                    )}
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {onUpdateAfdeling && (
        <AfdelingFormModal
          isOpen={isAfdelingModalOpen}
          onClose={() => setIsAfdelingModalOpen(false)}
          onSubmit={onUpdateAfdeling}
          initialData={afdelingMap}
        />
      )}

      <div className="p-6 pb-0">
        <TrainingKpi data={data} summaryData={summaryData} culledData={culledData} />
        {renderRegionTable("Persentasi lulusan Eksis", eksisRows, "text-green-700")}
        {renderRegionTable("Persentasi lulusan Resign", resignRows, "text-red-600")}
      </div>
    </section>
  );
}
