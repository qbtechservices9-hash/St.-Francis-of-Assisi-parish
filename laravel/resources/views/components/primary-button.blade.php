<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-[#6B2C2C] to-[#8C3D2C] hover:from-[#5A2424] hover:to-[#763223] border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:ring-offset-2 transition-all duration-200 cursor-pointer']) }}>
    {{ $slot }}
</button>
