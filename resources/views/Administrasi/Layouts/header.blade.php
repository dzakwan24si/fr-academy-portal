<!-- 2. TOP HEADER NAVBAR PARTIAL -->
<header class="h-16 bg-white border-b border-[#E5E7EB] flex items-center justify-between px-8 z-10 shrink-0">
    <!-- Search bar placeholder -->
    <div class="relative w-72">
        <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
            <i data-lucide="search" class="w-4 h-4"></i>
        </span>
        <input type="text" placeholder="Search..." class="w-full bg-[#F4F4F5] rounded-full py-1.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 text-[#18181B]">
    </div>

    <!-- Profile and Settings Actions -->
    <div class="flex items-center gap-4">
        <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
            <i data-lucide="bell" class="w-5 h-5"></i>
        </button>
        <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
            <i data-lucide="settings" class="w-5 h-5"></i>
        </button>
        <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
            <i data-lucide="help-circle" class="w-5 h-5"></i>
        </button>
        
        <div class="h-6 w-px bg-[#E5E7EB]"></div>

        <!-- Admin Profile Card -->
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-semibold text-[#18181B] leading-none">Admin Name</p>
                <p class="text-xs text-[#71717A]">Administrator</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-[#509c38] text-white flex items-center justify-center text-xs font-bold border border-[#E5E7EB]">
                AN
            </div>
        </div>
    </div>
</header>
