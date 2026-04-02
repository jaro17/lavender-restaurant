(function() {
  const init = () => {
    // Mobile menu toggle
    const mobileMenuBtns = document.querySelectorAll('.mobile-menu-btn');
    mobileMenuBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const nav = btn.closest('nav');
        const mobileMenu = nav.querySelector('.mobile-menu');
        mobileMenu.classList.toggle('hidden');
      });
    });

    // Print menu functionality
    const printBtns = document.querySelectorAll('.print-btn');
    printBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const printMenu = btn.closest('.print-menu');
        const printContent = printMenu.innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Lavender Restaurant - Denné menu</title>');
        printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Lato, sans-serif; padding: 20px; }');
        printWindow.document.write('h1, h2, h3 { font-family: "Playfair Display", serif; }');
        printWindow.document.write('.print-btn { display: none !important; }');
        printWindow.document.write('</style></head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
      });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
            // Close mobile menu if open
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
              mobileMenu.classList.add('hidden');
            }
          }
        }
      });
    });
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
