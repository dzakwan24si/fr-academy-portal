import { useState } from 'react';
import { Outlet, Link, useLocation } from 'react-router-dom';
import {
  LayoutDashboard,
  BarChart3,
  CalendarCheck,
  Layers,
  TrendingUp,
  Calendar,
  GraduationCap,
  Users,
  LineChart,
  Contact,
  Sparkles,
  Boxes,
  ChevronDown,
  Menu,
  X
} from 'lucide-react';

const AdminLayout = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [openSections, setOpenSections] = useState({
    dashboard: true,
    program: true,
    monitoring: true,
    talent: true,
    dataAset: true,
  });

  const location = useLocation();

  const toggleSection = (sectionId) => {
    setOpenSections(prev => ({
      ...prev,
      [sectionId]: !prev[sectionId]
    }));
  };

  const menuSections = [
    {
      id: 'dashboard',
      title: 'Dashboard',
      items: [
        { name: 'Dashboard Utama', href: '/administrasi/dashboard-realisasi', icon: BarChart3, isExternal: true },
        { name: 'Dashboard MR', href: '/', icon: LayoutDashboard, isExternal: false },
        { name: 'Dashboard Progress per Tahun', href: '/administrasi/dashboard-monitoring-tahunan', icon: CalendarCheck, isExternal: true },
      ]
    },
    {
      id: 'program',
      title: 'Program',
      items: [
        { name: 'Program', href: '/administrasi/program', icon: Layers, isExternal: true },
        { name: 'Realisasi Program Vs Target', href: '/administrasi/realisasi-vs-batch', icon: TrendingUp, isExternal: true },
        { name: 'Schedule', href: '/administrasi/schedule', icon: Calendar, isExternal: true },
        { name: 'Program / Batch', href: '/administrasi/program-batch', icon: GraduationCap, isExternal: true },
        { name: 'Training Record', href: '/administrasi/training-record', icon: Users, isExternal: true },
      ]
    },
    {
      id: 'monitoring',
      title: 'Monitoring',
      items: [
        { name: 'Monitoring Summarized', href: '/administrasi/monitoring-summarized', icon: BarChart3, isExternal: true },
        { name: 'Monitoring Alumni', href: '/administrasi/monitoring-alumni', icon: GraduationCap, isExternal: true },
        { name: 'Database Asisten', href: '/administrasi/asisten', icon: Contact, isExternal: true },
        { name: 'Monitoring Detailed', href: '/administrasi/monitoring', icon: LineChart, isExternal: true },
      ]
    },
    {
      id: 'talent',
      title: 'Talent',
      items: [
        { name: 'Monitoring Talent', href: '/administrasi/monitoring-talent', icon: Sparkles, isExternal: true },
        { name: 'Talent Pool', href: '/administrasi/talent-pool', icon: Users, isExternal: true },
      ]
    },
    {
      id: 'dataAset',
      title: 'Data Aset',
      items: [
        { name: 'Dashboard Inventaris', href: '/administrasi/inventaris/dashboard', icon: Boxes, isExternal: true },
      ]
    }
  ];

  return (
    <div className="flex h-screen bg-slate-50 overflow-hidden font-sans">
      {/* Mobile sidebar backdrop */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-20 bg-black/50 lg:hidden transition-opacity"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Sidebar Top Management */}
      <aside
        className={`fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col
        ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}
      >
        {/* Header Branding Top Management */}
        <div className="px-5 py-5 border-b border-slate-200 bg-white">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-9 h-9 bg-[#4B6A34] rounded-lg flex items-center justify-center shrink-0 shadow-sm">
              <img src="/Logo First Resource.png" alt="Logo" className="w-6 h-6 object-contain" />
            </div>
            <div>
              <h1 className="font-extrabold text-sm text-slate-800 leading-tight">First Resources</h1>
              <p className="text-xs font-bold text-[#4B6A34]">Top Management</p>
            </div>
          </div>

          {/* User Profile Badge */}
          <div className="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
            <div className="w-7 h-7 rounded-full bg-[#4B6A34] text-white flex items-center justify-center text-xs font-bold shrink-0">
              TM
            </div>
            <div className="min-w-0">
              <p class="text-xs font-bold text-slate-800 truncate leading-tight">Top Executive</p>
              <p class="text-[10px] text-slate-500 font-medium">Management Level</p>
            </div>
          </div>
        </div>

        {/* Navigation Sections */}
        <nav className="flex-1 px-3 py-4 overflow-y-auto space-y-4">
          {menuSections.map((sec) => {
            const isOpen = openSections[sec.id];

            return (
              <div key={sec.id} className="space-y-1">
                {/* Section Header Button */}
                <button
                  onClick={() => toggleSection(sec.id)}
                  className="w-full flex items-center justify-between px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-colors"
                >
                  <span>{sec.title}</span>
                  <ChevronDown
                    size={14}
                    className={`transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}
                  />
                </button>

                {/* Sub-menu Items */}
                {isOpen && (
                  <div className="space-y-0.5 pt-0.5">
                    {sec.items.map((item) => {
                      const isActive = !item.isExternal && (location.pathname === item.href || (item.href === '/' && (location.pathname === '/' || location.pathname === '')));
                      const Icon = item.icon;

                      if (item.isExternal) {
                        const targetHref = item.href.includes('?') ? `${item.href}&role=top_management` : `${item.href}?role=top_management`;
                        return (
                          <a
                            key={item.name}
                            href={targetHref}
                            className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium transition-colors"
                          >
                            <Icon size={16} className="shrink-0 text-slate-400" />
                            <span className="truncate">{item.name}</span>
                          </a>
                        );
                      }

                      return (
                        <Link
                          key={item.name}
                          to={item.href}
                          className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                            ${isActive
                              ? 'bg-[#4B6A34] text-white font-semibold shadow-sm'
                              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'
                            }`}
                        >
                          <Icon size={16} className={`shrink-0 ${isActive ? 'text-white' : 'text-slate-400'}`} />
                          <span className="truncate">{item.name}</span>
                        </Link>
                      );
                    })}
                  </div>
                )}
              </div>
            );
          })}
        </nav>

        {/* Sidebar Footer */}
        <div className="p-3 border-t border-slate-200">
          <div className="text-[10px] text-slate-400 text-center">
            &copy; 2026 FR Academy Top Management
          </div>
        </div>
      </aside>

      {/* Main Content Area */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        {/* Mobile menu button */}
        <button
          className="lg:hidden absolute top-4 right-4 z-40 bg-white p-2 rounded-full shadow-md text-slate-500 hover:text-slate-700 border border-slate-200"
          onClick={() => setSidebarOpen(true)}
        >
          <Menu size={20} />
        </button>

        {/* Main Body */}
        <main className="flex-1 overflow-y-auto p-4 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default AdminLayout;
