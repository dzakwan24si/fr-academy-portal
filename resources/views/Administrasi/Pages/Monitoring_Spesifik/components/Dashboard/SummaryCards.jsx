import { Users, UserX, UserMinus, ArrowUpRight } from "lucide-react";

export function SummaryCards({ data = [] }) {
  const eksisCount = data.filter((employee) => employee.STATUS === "Eksis" || employee.STATUS === "Aktif").length;
  const terminateCount = data.filter((employee) => employee.STATUS === "Terminate" || employee.STATUS === "Resign").length;
  const culledCount = data.filter((employee) => employee.STATUS === "Culled").length;
  const totalCount = data.length;
  const percentage = (count) => totalCount ? `${Math.round((count / totalCount) * 100)}%` : "0%";

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      {/* Total Karyawan */}
      <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div className="flex justify-between items-start mb-4">
          <p className="text-sm text-gray-500 font-medium">Total Employees</p>
          <div className="p-2 border border-gray-100 rounded-full text-gray-400">
            <Users size={16} />
          </div>
        </div>
        <div className="flex items-center gap-3">
          <h3 className="text-4xl font-bold text-gray-800">{totalCount}</h3>
          <span className="flex items-center gap-1 bg-[#eaf4ec] text-[#2c8f42] text-xs font-bold px-2 py-0.5 rounded-full">
            <ArrowUpRight size={12} /> 100%
          </span>
        </div>
        <p className="text-xs text-gray-400 mt-3">Seluruh data yang tersimpan</p>
      </div>

      {/* Eksis */}
      <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div className="flex justify-between items-start mb-4">
          <p className="text-sm text-gray-500 font-medium">Active (Eksis)</p>
          <div className="p-2 border border-gray-100 rounded-full text-gray-400">
            <Users size={16} />
          </div>
        </div>
        <div className="flex items-center gap-3">
          <h3 className="text-4xl font-bold text-gray-800">{eksisCount}</h3>
          <span className="flex items-center gap-1 bg-[#eaf4ec] text-[#2c8f42] text-xs font-bold px-2 py-0.5 rounded-full">
            <ArrowUpRight size={12} /> {percentage(eksisCount)}
          </span>
        </div>
        <p className="text-xs text-gray-400 mt-3">Proporsi dari seluruh karyawan</p>
      </div>

      {/* Terminate */}
      <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div className="flex justify-between items-start mb-4">
          <p className="text-sm text-gray-500 font-medium">Terminated</p>
          <div className="p-2 border border-gray-100 rounded-full text-gray-400">
            <UserX size={16} />
          </div>
        </div>
        <div className="flex items-center gap-3">
          <h3 className="text-4xl font-bold text-gray-800">{terminateCount}</h3>
          <span className="flex items-center gap-1 bg-red-50 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
            <ArrowUpRight size={12} className="rotate-90" /> {percentage(terminateCount)}
          </span>
        </div>
        <p className="text-xs text-gray-400 mt-3">Proporsi dari seluruh karyawan</p>
      </div>

      {/* Culled */}
      <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div className="flex justify-between items-start mb-4">
          <p className="text-sm text-gray-500 font-medium">Culled</p>
          <div className="p-2 border border-gray-100 rounded-full text-gray-400">
            <UserMinus size={16} />
          </div>
        </div>
        <div className="flex items-center gap-3">
          <h3 className="text-4xl font-bold text-gray-800">{culledCount}</h3>
          <span className="flex items-center gap-1 bg-orange-50 text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full">
            <ArrowUpRight size={12} className="rotate-90" /> {percentage(culledCount)}
          </span>
        </div>
        <p className="text-xs text-gray-400 mt-3">Proporsi dari seluruh karyawan</p>
      </div>
    </div>
  );
}
