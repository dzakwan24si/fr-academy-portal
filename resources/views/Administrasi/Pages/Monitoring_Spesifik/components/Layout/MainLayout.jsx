import { useState } from "react";
import { Outlet } from "react-router-dom";
import { Sidebar } from "./Sidebar";
import { Topbar } from "./Topbar";

export function MainLayout() {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  return (
    <div className="flex flex-col h-screen bg-[#f4f6f8] p-2 sm:p-4 gap-3 sm:gap-4 overflow-hidden">
      {/* Full Width Topbar Card */}
      <Topbar onOpenSidebar={() => setIsSidebarOpen(true)} />
      
      {/* Bottom Area: Sidebar & Main Content */}
      <div className="flex flex-1 gap-4 overflow-hidden relative">
        
        {/* Mobile Backdrop */}
        {isSidebarOpen && (
          <div 
            className="fixed inset-0 bg-black/40 z-40 md:hidden transition-opacity"
            onClick={() => setIsSidebarOpen(false)}
          />
        )}

        {/* Sidebar Card */}
        <div className={`
          fixed inset-y-0 left-0 z-50 w-64 bg-white md:rounded-3xl shadow-2xl md:shadow-sm shrink-0 overflow-hidden flex flex-col
          transition-transform duration-300 ease-in-out md:relative md:translate-x-0
          ${isSidebarOpen ? 'translate-x-0' : '-translate-x-full'}
        `}>
          <Sidebar onClose={() => setIsSidebarOpen(false)} />
        </div>
        
        {/* Main Content */}
        <main className="flex-1 overflow-x-hidden overflow-y-auto pb-8 pt-1 md:pt-0">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
