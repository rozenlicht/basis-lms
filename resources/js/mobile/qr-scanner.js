import QrScanner from 'qr-scanner';

const BASE_URL = import.meta.env.VITE_APP_URL ?? window.APP_URL ?? window.location.origin;

function openScanner(button) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[99] flex items-center justify-center bg-black/70 backdrop-blur-sm';

    const container = document.createElement('div');
    container.className = 'relative w-[90vw] max-w-sm overflow-hidden rounded-3xl bg-white shadow-2xl';

    const video = document.createElement('video');
    video.className = 'w-full';

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'absolute right-3 top-3 rounded-full bg-black/70 px-3 py-1 text-sm font-semibold text-white';
    closeBtn.textContent = 'Close';

    const status = document.createElement('p');
    status.className = 'px-4 pb-4 pt-2 text-center text-sm text-slate-500';
    status.textContent = 'Align the QR code within the frame.';

    container.appendChild(video);
    container.appendChild(closeBtn);
    container.appendChild(status);
    modal.appendChild(container);
    document.body.appendChild(modal);

    const scanner = new QrScanner(video, (result) => {
        const url = result.data || result;
        if (!url) {
            status.textContent = 'Unable to read QR code. Try again.';
            return;
        }

        try {
            const parsed = new URL(url);
            const base = `${parsed.protocol}//${parsed.host}`;
            if (base !== BASE_URL) {
                status.textContent = 'QR code points to another system.';
                return;
            }

            scanner.stop();
            document.body.removeChild(modal);
            window.location.href = parsed.toString();
        } catch (error) {
            console.error('QR scanner error:', error);
            status.textContent = 'Invalid QR code.';
        }
    }, {
        returnDetailedScanResult: true,
    });

    scanner.start().catch((error) => {
        console.error('Scanner failed to start:', error);
        status.textContent = 'Camera access denied.';
    });

    const cleanup = () => {
        scanner.stop();
        scanner.destroy();
        if (document.body.contains(modal)) {
            document.body.removeChild(modal);
        }
    };

    closeBtn.addEventListener('click', cleanup, { once: true });
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            cleanup();
        }
    });
}

function bindScannerButtons() {
    document.querySelectorAll('[data-mobile-qr]').forEach((button) => {
        if (button.dataset.qrBound === 'true') {
            return;
        }

        button.addEventListener('click', () => openScanner(button));
        button.dataset.qrBound = 'true';
    });
}

if (document.readyState !== 'loading') {
    bindScannerButtons();
} else {
    document.addEventListener('DOMContentLoaded', bindScannerButtons);
}

if (window.Livewire) {
    Livewire.hook('message.processed', () => {
        bindScannerButtons();
    });
    Livewire.on('mobile-qr-scan', () => {
        const button = document.querySelector('[data-mobile-qr]');
        if (button) {
            openScanner(button);
        }
    });
} else {
    document.addEventListener('livewire:init', () => {
        bindScannerButtons();
        Livewire.hook('message.processed', () => bindScannerButtons());
        Livewire.on('mobile-qr-scan', () => {
            const button = document.querySelector('[data-mobile-qr]');
            if (button) {
                openScanner(button);
            }
        });
    });
}
