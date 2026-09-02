import { NavLink } from "react-router-dom";
import { LayoutDashboard, Users, UserX, UserMinus, GraduationCap, Database, X } from "lucide-react";

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

      <nav className="flex-1 px-4 py-8 md:py-6 overflow-y-auto">
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
