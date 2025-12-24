<!-- Camera Modal -->
<div x-data="cameraModal()" x-show="$store.camera.isOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="$store.camera.isOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             aria-hidden="true"
             @click="closeCamera()"></div>

        <!-- Modal panel -->
        <div x-show="$store.camera.isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <i class="fas fa-camera mr-2"></i>Ambil Foto Absensi
                            </h3>
                            <button @click="closeCamera()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <!-- Camera View -->
                        <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="min-height: 300px;">
                            <video x-ref="video" autoplay playsinline muted class="w-full h-auto" style="max-height: 400px;" x-show="stream && !capturedImage"></video>
                            <canvas x-ref="canvas" class="hidden"></canvas>
                            
                            <!-- Captured Image Preview -->
                            <img x-show="capturedImage" :src="capturedImage" class="w-full h-auto" style="max-height: 400px;">
                            
                            <!-- Camera Status / Requesting Permission -->
                            <div x-show="!stream && !capturedImage && !error" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                <div class="text-white text-center p-6 max-w-md">
                                    <div class="mb-6">
                                        <div class="relative inline-block">
                                            <i class="fas fa-video text-6xl mb-3 text-blue-400"></i>
                                            <div class="absolute -top-1 -right-1 animate-ping">
                                                <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="text-xl font-bold mb-3">Izinkan Akses Kamera</h4>
                                    <p class="text-sm text-gray-300 mb-6">Browser akan meminta izin akses kamera. Klik <strong class="text-blue-400">"Allow"</strong> atau <strong class="text-blue-400">"Izinkan"</strong> untuk melanjutkan.</p>
                                    
                                    <!-- Manual Request Button -->
                                    <button @click="requestCameraManually()" 
                                        class="mb-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-all transform hover:scale-105">
                                        <i class="fas fa-video mr-2"></i>Klik untuk Aktifkan Kamera
                                    </button>
                                    
                                    <div class="mt-6 pt-6 border-t border-gray-700">
                                        <p class="text-xs text-gray-400 mb-2">💡 Tips:</p>
                                        <ul class="text-xs text-gray-400 space-y-1">
                                            <li>• Pastikan kamera tidak digunakan aplikasi lain</li>
                                            <li>• Gunakan browser Chrome, Firefox, atau Edge</li>
                                            <li>• Akses via <span class="text-blue-400 font-mono">localhost</span> atau <span class="text-blue-400 font-mono">https://</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div x-show="error" class="mt-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm text-red-800 font-medium mb-2">Gagal Mengakses Kamera</p>
                                    <p class="text-sm text-red-700 whitespace-pre-line" x-text="error"></p>
                                    <div class="mt-3 text-xs text-red-600">
                                        <p class="font-medium mb-1">Cara mengatur agar selalu diizinkan:</p>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li><strong>Chrome/Edge:</strong> Klik ikon kamera di address bar → Kelola → Izinkan</li>
                                            <li><strong>Firefox:</strong> Klik ikon gembok → Hapus izin → Muat ulang → Pilih "Ingat keputusan" → Izinkan</li>
                                            <li><strong>Safari:</strong> Settings → Websites → Camera → Pilih "Allow"</li>
                                        </ul>
                                        <p class="mt-2"><strong>Atau:</strong> Gunakan URL <code class="bg-red-100 px-1 rounded">localhost</code> atau <code class="bg-red-100 px-1 rounded">https://</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <template x-if="!capturedImage && !error">
                    <button @click="capturePhoto()" 
                        :disabled="!stream"
                        :class="stream ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-camera mr-2"></i>
                        <span x-text="stream ? 'Ambil Foto' : 'Menunggu Kamera...'"></span>
                    </button>
                </template>
                <template x-if="capturedImage">
                    <button @click="confirmPhoto()" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-check mr-2"></i>Gunakan Foto Ini
                    </button>
                </template>
                <template x-if="capturedImage">
                    <button @click="retakePhoto()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        <i class="fas fa-redo mr-2"></i>Foto Ulang
                    </button>
                </template>
                <template x-if="error">
                    <button @click="startCamera()" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                </template>
                <button @click="closeCamera()" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function cameraModal() {
    return {
        stream: null,
        capturedImage: null,
        error: null,
        isRequesting: false,

        init() {
            this.$watch('$store.camera.isOpen', (value) => {
                if (value) {
                    // Auto-request on modal open
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.requestCameraManually();
                        }, 300);
                    });
                }
            });
        },

        async requestCameraManually() {
            if (this.isRequesting) return;
            
            this.isRequesting = true;
            this.error = null;
            this.capturedImage = null;
            
            try {
                console.log('🎥 Requesting camera access...');
                
                // Check browser support
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser Anda tidak mendukung akses kamera. Gunakan browser modern seperti Chrome, Firefox, atau Edge.');
                }

                // Simple video constraints
                const constraints = {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                };

                // Request permission - THIS WILL SHOW BROWSER POPUP
                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                console.log('✅ Camera access granted!');
                console.log('Stream:', this.stream);
                
                // Setup video element
                await this.$nextTick();
                
                const video = this.$refs.video;
                if (video) {
                    video.srcObject = this.stream;
                    video.muted = true;
                    
                    // Force play
                    try {
                        await video.play();
                        console.log('✅ Video playing');
                    } catch (playError) {
                        console.error('Play error:', playError);
                    }
                } else {
                    console.error('❌ Video element not found');
                }
                
                this.isRequesting = false;
                
            } catch (err) {
                console.error('❌ Camera error:', err);
                this.isRequesting = false;
                
                // User-friendly error messages
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    this.error = '🚫 Akses kamera ditolak!\n\nCara mengizinkan:\n1. Klik ikon kamera 📷 atau gembok 🔒 di address bar (kiri URL)\n2. Pilih "Izinkan" atau "Allow" untuk akses kamera\n3. Klik tombol "Coba Lagi" di bawah';
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    this.error = '📷 Kamera tidak ditemukan!\n\nPastikan:\n• Kamera terhubung dengan benar\n• Driver kamera terinstal\n• Kamera tidak rusak';
                } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                    this.error = '⚠️ Kamera sedang digunakan!\n\nTutup aplikasi lain yang menggunakan kamera:\n• Zoom, Teams, Skype\n• Browser tabs lain\n• Aplikasi kamera lainnya';
                } else if (err.name === 'OverconstrainedError') {
                    console.log('Trying lower resolution...');
                    setTimeout(() => this.tryLowerResolution(), 500);
                } else if (err.name === 'TypeError') {
                    this.error = '🌐 Akses kamera tidak diizinkan!\n\nGunakan salah satu cara berikut:\n• Akses via http://localhost\n• Akses via https:// (SSL)\n• Bukan menggunakan IP address';
                } else {
                    this.error = err.message || 'Gagal mengakses kamera. Silakan coba lagi.';
                }
            }
        },

        async tryLowerResolution() {
            try {
                console.log('🔄 Trying lower resolution...');
                const constraints = {
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    audio: false
                };
                
                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                await this.$nextTick();
                const video = this.$refs.video;
                if (video) {
                    video.srcObject = this.stream;
                    video.muted = true;
                    await video.play();
                }
                
                this.error = null;
                console.log('✅ Lower resolution works!');
            } catch (err) {
                console.error('❌ Lower resolution failed:', err);
                this.error = 'Tidak dapat mengakses kamera dengan resolusi apapun. Periksa pengaturan kamera Anda.';
            }
        },

        async startCamera() {
            // Alias for manual request
            await this.requestCameraManually();
        },

        async startCameraLowRes() {
            await this.tryLowerResolution();
        },

        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            
            if (!video || !canvas) {
                console.error('Video or canvas not found');
                return;
            }
            
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            this.capturedImage = canvas.toDataURL('image/png');
            console.log('📸 Photo captured!');
            
            // Stop camera stream
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },

        retakePhoto() {
            this.capturedImage = null;
            this.requestCameraManually();
        },

        confirmPhoto() {
            if (this.capturedImage) {
                console.log('📤 Confirming photo...');
                console.log('Image size:', this.capturedImage.length, 'characters');
                
                // Store image data
                Alpine.store('camera').imageData = this.capturedImage;
                
                // Get form based on type
                const formId = Alpine.store('camera').type === 'checkin' ? 'checkin-form' : 'checkout-form';
                const form = document.getElementById(formId);
                
                if (!form) {
                    console.error('❌ Form not found:', formId);
                    this.error = 'Form tidak ditemukan. Silakan refresh halaman.';
                    return;
                }
                
                // Set image data to hidden input
                const imageInput = form.querySelector('input[name="image"]');
                if (!imageInput) {
                    console.error('❌ Image input not found in form');
                    this.error = 'Input foto tidak ditemukan. Silakan refresh halaman.';
                    return;
                }
                
                imageInput.value = this.capturedImage;
                console.log('✅ Image data set to form input');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                
                // Add loading state before submitting
                const submitBtn = form.querySelector('button[type="button"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
                    
                    // Set a timeout to restore button if submission fails
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }, 10000);
                }
                
                // Close modal first
                this.closeCamera();
                
                // Submit form using setTimeout to ensure modal closes first
                console.log('📤 Submitting form...');
                setTimeout(() => {
                    form.submit();
                }, 100);
            } else {
                console.error('❌ No captured image');
                this.error = 'Tidak ada foto yang diambil. Silakan ambil foto terlebih dahulu.';
            }
        },

        closeCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.capturedImage = null;
            this.error = null;
            this.isRequesting = false;
            Alpine.store('camera').isOpen = false;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
