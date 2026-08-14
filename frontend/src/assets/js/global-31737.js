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
        const printFrame = document.createElement('iframe');
        printFrame.setAttribute('aria-hidden', 'true');
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        const printDocument = printFrame.contentWindow.document;
        printDocument.write('<html><head><title>Lavender Restaurant - Denné menu</title>');
        printDocument.write('<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Nunito:wght@400;700&subset=latin,latin-ext&display=swap" rel="stylesheet">');
        printDocument.write(`<style>
          @page { size: A4 portrait; margin: 8mm; }
          * { box-sizing: border-box; }
          body { font-family: Nunito, sans-serif; margin: 0; padding: 0; color: #1f2937; }
          h1, h2, h3, h4 { font-family: "Playfair Display", serif; margin: 0; }
          p { margin: 0; }
          .print-btn,
          .print-menu-logo { display: none !important; }
          .print-menu { border: 0 !important; box-shadow: none !important; overflow: visible !important; }
          .print-menu > div:first-child { padding: 0 0 8px !important; background: none !important; border-bottom: 1px solid #d8c8e8 !important; }
          .print-menu > div:first-child h2 { font-size: 22px !important; line-height: 1.1 !important; margin-bottom: 2px !important; }
          .print-menu > div:first-child p { font-size: 13px !important; line-height: 1.2 !important; margin-top: 2px !important; }
          #menu-dynamic-content { padding: 8px 0 !important; }
          #menu-dynamic-content > p:first-child { margin-bottom: 8px !important; font-size: 12px !important; }
          .space-y-10 > * + * { margin-top: 10px !important; }
          .menu-day h3 { font-size: 17px !important; line-height: 1.15 !important; padding-bottom: 4px !important; margin-bottom: 6px !important; }
          .menu-day h4 { font-size: 12px !important; line-height: 1.1 !important; margin-bottom: 4px !important; }
          .menu-day .mb-6 { margin-bottom: 8px !important; }
          .space-y-2 > * + * { margin-top: 3px !important; }
          .flex.justify-between.items-start {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            column-gap: 8px !important;
            align-items: start !important;
            line-height: 1.2 !important;
            margin: 0 !important;
          }
          .flex.justify-between.items-start .flex-1 {
            min-width: 0 !important;
            font-size: 12px !important;
            line-height: 1.25 !important;
          }
          .flex.justify-between.items-start .whitespace-nowrap,
          .flex.justify-between.items-start .font-medium {
            display: block !important;
            align-self: start !important;
            font-size: 12px !important;
            line-height: 1.15 !important;
            margin-left: 0 !important;
            white-space: nowrap !important;
          }
          .text-xs { font-size: 10px !important; line-height: 1.2 !important; }
          .bg-purple-50 { background: none !important; }
          .border-t { border-top: 1px solid #d8c8e8 !important; }
          .px-8.py-6.bg-purple-50.border-t.border-purple-100 { padding: 6px 0 0 !important; margin-top: 8px !important; }
          .px-8.py-6.bg-purple-50.border-t.border-purple-100 p { font-size: 10px !important; line-height: 1.2 !important; }
        </style></head><body>`);
        printDocument.write(printContent);
        printDocument.write('</body></html>');
        printDocument.close();

        const cleanupPrintFrame = () => {
          printFrame.remove();
        };

        const runPrint = () => {
          const frameWindow = printFrame.contentWindow;
          if (!frameWindow) {
            cleanupPrintFrame();
            return;
          }
          frameWindow.focus();
          frameWindow.onafterprint = cleanupPrintFrame;
          frameWindow.print();
          window.setTimeout(cleanupPrintFrame, 1000);
        };

        printFrame.onload = runPrint;
        window.setTimeout(runPrint, 50);
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
