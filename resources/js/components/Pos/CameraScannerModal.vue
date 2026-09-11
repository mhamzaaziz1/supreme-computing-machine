<script setup>
import { ref, onMounted, onUnmounted, defineEmits } from 'vue';

const emit = defineEmits(['close', 'scan']);
const isScanning = ref(false);
const scanHistory = ref([]);
let html5QrcodeScanner = null;

onMounted(() => {
    // In a real implementation, we would initialize html5-qrcode here
    // import { Html5QrcodeScanner } from "html5-qrcode";
    /*
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: {width: 250, height: 250} },
        false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    */
    isScanning.value = true;
});

onUnmounted(() => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(error => {
            console.error("Failed to clear html5QrcodeScanner. ", error);
        });
    }
});

const onScanSuccess = (decodedText) => {
    scanHistory.value.unshift({
        text: decodedText,
        time: new Date().toLocaleTimeString()
    });
    emit('scan', decodedText);
};

// Mock function for demo purposes
const triggerMockScan = () => {
    const mockCode = "123456789012";
    onScanSuccess(mockCode);
};
</script>

<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-slate-700">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl leading-6 font-bold text-white flex items-center" id="modal-title">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Live Barcode Scanner
                    </h3>
                    <button @click="emit('close')" class="text-white hover:text-gray-200 focus:outline-none bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 bg-gray-50 dark:bg-slate-900/50">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        
                        <!-- Camera Area -->
                        <div class="md:col-span-3 flex flex-col">
                            <div class="relative w-full h-[350px] bg-black rounded-2xl overflow-hidden shadow-inner border border-gray-200 dark:border-slate-700 flex items-center justify-center">
                                <!-- Scanner Container (Empty for now, replaced by library) -->
                                <div id="reader" class="w-full h-full absolute inset-0"></div>
                                
                                <!-- Placeholder overlay for demonstration -->
                                <div v-if="isScanning" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
                                    <div class="w-48 h-48 border-2 border-indigo-500 rounded-lg relative">
                                        <!-- Scan line animation -->
                                        <div class="absolute left-0 top-0 w-full h-0.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-[scan_2s_ease-in-out_infinite]"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex justify-center">
                                <div class="bg-white dark:bg-slate-800 px-6 py-3 rounded-full shadow-md border border-gray-100 dark:border-slate-700 flex items-center text-gray-700 dark:text-gray-300 font-medium">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Ready to scan items...
                                </div>
                                <button @click="triggerMockScan" class="ml-4 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hidden">Mock Scan</button>
                            </div>
                        </div>

                        <!-- History Area -->
                        <div class="md:col-span-2">
                            <div class="bg-white dark:bg-slate-800 rounded-2xl h-full flex flex-col shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center font-bold text-gray-700 dark:text-gray-200">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Session History
                                </div>
                                
                                <div class="flex-1 overflow-y-auto p-3 custom-scrollbar">
                                    <div v-if="scanHistory.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 italic">
                                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                                        No items scanned yet
                                    </div>
                                    
                                    <transition-group name="list" tag="ul" class="space-y-2">
                                        <li v-for="(scan, index) in scanHistory" :key="index" class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-3 flex items-center border border-gray-100 dark:border-slate-600 shadow-sm">
                                            <div class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-2 rounded-lg mr-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200">{{ scan.text }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ scan.time }}</div>
                                            </div>
                                        </li>
                                    </transition-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes scan {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
