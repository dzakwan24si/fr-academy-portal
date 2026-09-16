import { useState, useEffect } from "react";
import { ChevronDown, Clock, MapPin, Menu } from "lucide-react";
import { useLocation } from "react-router-dom";

export function Topbar({ onOpenSidebar }) {
  const [time, setTime] = useState(new Date());
  const location = useLocation();

  useEffect(() => {
    const timer = setInterval(() => setTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  const formatTime = (date) => {
    return date.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit"
    });
  };

  const formatDate = (date) => {
    return date.toLocaleDateString("id-ID", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric"
    });
  };

  const getBreadcrumb = (pathname) => {
    switch (pathname) {
      case "/": return "Dashboard Utama";
      case "/eksis": return "Data Karyawan / Staf Eksis";
      case "/terminate": return "Data Karyawan / Staf Terminate";
      case "/culled": return "Data Karyawan / Staf Culled";
      case "/settings": return "Pengaturan Sistem";
      default: return "Halaman FR Academy";
    }
  };

  return (
    <header className="h-16 bg-white rounded-2xl md:rounded-[2rem] shadow-sm flex items-center justify-between px-4 sm:px-6 shrink-0">
      {/* Left Side: Logo & Breadcrumbs */}
      <div className="flex items-center gap-2 sm:gap-6">
        {/* Mobile Hamburger */}
        {onOpenSidebar && (
          <button 
            onClick={onOpenSidebar}
            className="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-full"
          >
            <Menu size={20} />
          </button>
        )}

        <div className="flex items-center">
          <img src="/logofr.png" alt="FR Logo" className="h-8 w-auto mr-2 sm:mr-3" />
          <span className="font-bold text-gray-800 text-lg tracking-tight">FR Academy</span>
        </div>
        
        <div className="hidden md:flex items-center gap-2 px-4 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
          <MapPin size={14} className="text-[#2c8f42]" />
          <span className="text-xs font-bold text-gray-500 uppercase tracking-wide">
            {getBreadcrumb(location.pathname)}
          </span>
        </div>
      </div>

      {/* Right Side: Clock & Profile */}
      <div className="flex flex-1 items-center justify-end gap-6">
        
        {/* Live Clock */}
        <div className="hidden md:flex flex-col items-end justify-center px-4 border-r border-gray-100">
          <div className="flex items-center gap-1.5 text-gray-800 font-bold text-sm">
            <Clock size={14} className="text-[#2c8f42]" />
            {formatTime(time)}
          </div>
          <div className="text-[10px] text-gray-500 font-medium">
            {formatDate(time)}
          </div>
        </div>

        {/* Profile */}
        <div className="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
          <div className="h-9 w-9 bg-[#eaf4ec] text-[#2c8f42] rounded-full flex items-center justify-center font-bold text-sm">
            A
          </div>
          <div className="flex flex-col items-start hidden sm:flex">
            <span className="text-sm font-semibold text-gray-800 leading-tight">Admin FR</span>
            <span className="text-[10px] text-gray-500 leading-tight">Superadmin</span>
          </div>
          <ChevronDown size={14} className="text-gray-400" />
        </div>

      </div>
    </header>
  );
}
