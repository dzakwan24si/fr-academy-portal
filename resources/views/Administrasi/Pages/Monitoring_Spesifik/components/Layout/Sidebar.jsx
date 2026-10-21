import { NavLink } from "react-router-dom";
import { LayoutDashboard, Users, UserX, UserMinus, GraduationCap, Database, X, ArrowLeft } from "lucide-react";

export function Sidebar({ onClose }) {
  const mainNavItems = [
    { name: "Dashboard", path: "/", icon: LayoutDashboard, badge: null },
    { name: "Staf Aktif", path: "/eksis", icon: Users, badge: null },
    { name: "Staf Resign", path: "/terminate", icon: UserX, badge: null },
    { name: "Peserta Culled", path: "/culled", icon: UserMinus, badge: null },
  ];

  return (
    <aside className="flex flex-col h-full w-full bg-white relative">
      {/* Mobile Close Button */}
      {onClose && (
        <button 
          onClick={onClose}
          className="md:hidden absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors z-10"
        >
          <X size={20} />
        </button>
      )}

      {/* Tombol Kembali ke Dashboard Utama / Sidebar Utama Portal */}
      <div className="px-4 pt-5 pb-1">
        <a
          href="/administrasi/dashboard-realisasi"
          className="flex items-center justify-between w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 hover:bg-[#eaf4ec] text-gray-700 hover:text-[#2c8f42] border border-gray-200 hover:border-[#cbe5d2] transition-all font-semibold text-xs shadow-2xs group"
          title="Kembali ke Dashboard Utama & Sidebar Utama FR Academy"
        >
          <div className="flex items-center gap-2.5">
            <div className="w-6 h-6 rounded-lg bg-white group-hover:bg-[#2c8f42] group-hover:text-white text-gray-500 flex items-center justify-center transition-all border border-gray-200 group-hover:border-[#2c8f42] shadow-2xs">
              <ArrowLeft size={13} className="transition-transform group-hover:-translate-x-0.5" />
            </div>
            <span>Dashboard Utama</span>
          </div>
          <span className="text-[10px] text-gray-400 group-hover:text-[#2c8f42] font-medium transition-colors">Kembali</span>
        </a>
      </div>

      <nav className="flex-1 px-4 py-4 md:py-4 overflow-y-auto">
        <div className="mb-8">
          <p className="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">
            Menu Utama
          </p>
          <ul className="space-y-1.5">
            {mainNavItems.map((item) => (
              <li key={item.path}>
                <NavLink
                  to={item.path}
                  className={({ isActive }) =>
                    `flex items-center justify-between px-4 py-3 rounded-2xl transition-all font-medium text-sm ${isActive
                      ? "bg-[#2c8f42] text-white shadow-md shadow-[#2c8f42]/20"
                      : "text-gray-500 hover:bg-gray-50 hover:text-gray-900"
                    }`
                  }
                >
                  <div className="flex items-center gap-3">
                    <item.icon size={18} strokeWidth={2.5} />
                    <span>{item.name}</span>
                  </div>
                  {item.badge && (
                    <span className="bg-[#fdebe9] text-[#e35649] text-[10px] font-bold px-2 py-0.5 rounded-full">
                      {item.badge}
                    </span>
                  )}
                </NavLink>
              </li>
            ))}
          </ul>
        </div>

        <div className="mb-6">
          <p className="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">
            Master Data
          </p>
          <ul className="space-y-1.5">
            <li>
              <NavLink
                to="/angkatan"
                className={({ isActive }) =>
                  `flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-medium text-sm ${isActive
                    ? "bg-[#2c8f42] text-white shadow-md shadow-[#2c8f42]/20"
                    : "text-gray-500 hover:bg-gray-50 hover:text-gray-900"
                  }`
                }
              >
                <GraduationCap size={18} strokeWidth={2.5} />
                <span>Data Angkatan</span>
              </NavLink>
            </li>
            <li>
              <NavLink
                to="/database"
                className={({ isActive }) =>
                  `flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-medium text-sm ${isActive
                    ? "bg-[#2c8f42] text-white shadow-md shadow-[#2c8f42]/20"
                    : "text-gray-500 hover:bg-gray-50 hover:text-gray-900"
                  }`
                }
              >
                <Database size={18} strokeWidth={2.5} />
                <span>Data Karyawan</span>
              </NavLink>
            </li>
          </ul>
        </div>
      </nav>

      {/* Bisa ditambahkan tombol Export / Settings nanti di sini jika fitur sudah siap */}
      <div className="p-6">
        <div className="bg-[#eaf4ec] p-4 rounded-2xl border border-[#cbe5d2]">
          <h4 className="text-[#2c8f42] font-bold text-xs mb-1">FR Academy System</h4>
          <p className="text-gray-500 text-[10px]">Versi 1.0 - Monitoring Karyawan</p>
        </div>
      </div>
    </aside>
  );
}
