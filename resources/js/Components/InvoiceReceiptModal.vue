<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Printer, X, Receipt, CheckCircle2, Banknote, QrCode } from '@lucide/vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    order: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const { t } = useI18n();
const page = usePage();

const settings = computed(() => page.props.settings || {});
const storeName = computed(() => settings.value?.store_name || 'CHOUERNCHYWORN KONG');
const storeAddress = computed(() => settings.value?.store_address || 'Phnom Penh, Cambodia');
const storePhone = computed(() => settings.value?.store_phone || '+855 12 345 678');

const exchangeRate = 4100;

const grandTotalKhr = computed(() => {
    if (!props.order) return 0;
    return Math.round(Number(props.order.grand_total) * exchangeRate);
});

const formatKhr = (val) => {
    return new Intl.NumberFormat('km-KH').format(val) + ' ៛';
};

const formatDate = (dateStr) => {
    if (!dateStr) return new Date().toLocaleString();
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit'
    });
};

const printReceipt = () => {
    const el = document.getElementById('printable-receipt');
    if (!el) {
        window.print();
        return;
    }

    // Create or reuse hidden iframe for isolated invoice printing
    let iframe = document.getElementById('pos-print-iframe');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'pos-print-iframe';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);
    }

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice - ${props.order?.invoice_no || 'Receipt'}</title>
            <style>
                @page {
                    margin: 0;
                    size: auto;
                }
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                    background: #ffffff !important;
                    color: #000000 !important;
                    width: 450px !important;
                    max-width: 450px !important;
                    margin: 0 auto !important;
                    padding: 20px 15px !important;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                .flex { display: flex; }
                .justify-between { justify-content: space-between; }
                .items-center { align-items: center; }
                .items-baseline { align-items: baseline; }
                .font-bold { font-weight: 700; }
                .font-semibold { font-weight: 600; }
                .font-extrabold { font-weight: 800; }
                .font-mono { font-family: monospace; }
                .uppercase { text-transform: uppercase; }
                .text-xs { font-size: 12px; }
                .text-sm { font-size: 14px; }
                .text-base { font-size: 16px; }
                .text-lg { font-size: 18px; }
                .text-xl { font-size: 20px; }
                .text-2xl { font-size: 24px; }
                .text-\\[11px\\] { font-size: 11px; }
                .text-\\[10px\\] { font-size: 10px; }
                .border-b { border-bottom: 1px dashed #000000; }
                .border-t { border-top: 1px dashed #000000; }
                .py-1 { padding-top: 4px; padding-bottom: 4px; }
                .py-2 { padding-top: 6px; padding-bottom: 6px; }
                .py-3 { padding-top: 10px; padding-bottom: 10px; }
                .pb-3 { padding-bottom: 10px; }
                .pb-4 { padding-bottom: 14px; }
                .pt-1 { padding-top: 4px; }
                .pt-2 { padding-top: 8px; }
                .mt-1 { margin-top: 4px; }
                .mt-3 { margin-top: 10px; }
                .pr-2 { padding-right: 8px; }
                .space-y-1 > * + * { margin-top: 4px; }
                .space-y-1\\.5 > * + * { margin-top: 6px; }
                .space-y-4 > * + * { margin-top: 14px; }
                .w-full { width: 100%; }
                .block { display: block; }
                .inline-flex { display: inline-flex; }
                .gap-1 { gap: 4px; }
                table { width: 100%; border-collapse: collapse; margin-top: 4px; }
                th, td { padding: 4px 0; font-size: 12px; }
                tr { border-bottom: 1px dashed #ccc; }
                .print\\:hidden { display: none !important; }
            </style>
        </head>
        <body>
            ${el.innerHTML}
        </body>
        </html>
    `);
    doc.close();

    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }, 250);
};
</script>

<template>
    <div v-if="show && order" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm print:p-0 print:bg-white print:static print:block print:inset-auto print:w-full print:h-auto">
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh] print:max-h-none print:shadow-none print:border-none print:w-full print:max-w-[80mm] print:mx-auto print:bg-white print:text-black print:overflow-visible">
            
            <!-- Modal Header (Screen Only) -->
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800 flex items-center justify-between shrink-0 print:hidden">
                <div class="flex items-center gap-2">
                    <Receipt class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base">Invoice Receipt</h3>
                </div>
                <button @click="emit('close')" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Receipt Content Container (Print Target) -->
            <div id="printable-receipt" class="p-6 overflow-y-auto space-y-4 font-mono text-gray-800 dark:text-gray-200 print:text-black print:p-3 print:overflow-visible print:bg-white">
                
                <!-- Store Header -->
                <div class="text-center border-b border-dashed border-gray-300 dark:border-gray-600 pb-4 print:border-black">
                    <h2 class="text-xl font-extrabold uppercase tracking-wide text-gray-900 dark:text-gray-100 print:text-black print:text-lg">{{ storeName }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 print:text-black">{{ storeAddress }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 print:text-black">Tel: {{ storePhone }}</p>
                    
                    <div class="mt-3 py-1 bg-gray-100 dark:bg-gray-700/50 rounded text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 print:bg-transparent print:border print:border-black print:text-black">
                        OFFICIAL RECEIPT / វិក្កយបត្រ
                    </div>
                </div>

                <!-- Invoice Meta Info -->
                <div class="text-xs space-y-1 border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Invoice No:</span>
                        <span class="font-bold font-mono print:text-black">{{ order.invoice_no }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Date & Time:</span>
                        <span class="print:text-black">{{ formatDate(order.order_date) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Cashier:</span>
                        <span class="print:text-black">{{ order.user?.name || 'Cashier' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Customer:</span>
                        <span class="print:text-black">{{ order.customer?.name || 'Walk-in' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Payment Method:</span>
                        <span class="font-bold uppercase inline-flex items-center gap-1">
                            <span v-if="order.payment_method === 'khqr' || order.payment_method === 'bank'" class="text-red-600 dark:text-red-400 print:text-black">
                                KHQR (Bakong)
                            </span>
                            <span v-else class="text-emerald-600 dark:text-emerald-400 print:text-black">
                                CASH
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Product Items Table -->
                <div class="border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 print:text-black print:border-black">
                                <th class="py-1">ITEM</th>
                                <th class="py-1 text-center">QTY</th>
                                <th class="py-1 text-right">PRICE</th>
                                <th class="py-1 text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 print:divide-black">
                            <tr v-for="item in order.items" :key="item.id">
                                <td class="py-2 pr-2 font-medium print:text-black">
                                    {{ item.product?.name || 'Product #' + item.product_id }}
                                </td>
                                <td class="py-2 text-center print:text-black">{{ item.quantity }}</td>
                                <td class="py-2 text-right print:text-black">${{ Number(item.price).toFixed(2) }}</td>
                                <td class="py-2 text-right font-bold print:text-black">${{ Number(item.subtotal || item.price * item.quantity).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Calculation -->
                <div class="space-y-1.5 text-xs border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Subtotal:</span>
                        <span class="print:text-black">${{ Number(order.subtotal).toFixed(2) }}</span>
                    </div>
                    <div v-if="Number(order.tax) > 0" class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 print:text-black">Tax:</span>
                        <span class="print:text-black">${{ Number(order.tax).toFixed(2) }}</span>
                    </div>
                    <div v-if="Number(order.discount) > 0" class="flex justify-between text-red-600 dark:text-red-400 print:text-black">
                        <span class="print:text-black">Discount:</span>
                        <span class="print:text-black">-${{ Number(order.discount).toFixed(2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-200 dark:border-gray-700 print:border-black">
                        <span class="text-sm font-extrabold uppercase print:text-black">Grand Total:</span>
                        <div class="text-right">
                            <span class="text-lg font-extrabold text-emerald-700 dark:text-emerald-400 print:text-black">${{ Number(order.grand_total).toFixed(2) }}</span>
                            <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 print:text-black">{{ formatKhr(grandTotalKhr) }}</span>
                        </div>
                    </div>

                    <!-- Cash Calculation Details -->
                    <template v-if="order.payment_method === 'cash' && order.cash_received">
                        <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700 print:border-black">
                            <span class="text-gray-500 dark:text-gray-400 print:text-black">Cash Received:</span>
                            <span class="font-bold print:text-black">${{ Number(order.cash_received).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400 print:text-black">Change Due:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 print:text-black">
                                ${{ Number(order.change_amount || (order.cash_received - order.grand_total)).toFixed(2) }} 
                                ({{ formatKhr(Math.round(Math.max(0, order.cash_received - order.grand_total) * exchangeRate)) }})
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Footer Thank You Note -->
                <div class="text-center pt-2 space-y-1 print:text-black">
                    <p class="text-xs font-bold print:text-black">THANK YOU FOR YOUR BUSINESS!</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 print:text-black">សូមអរគុណ សម្រាប់ការជាវទំនិញ!</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 print:text-black pt-1">Powered by POS System</p>
                </div>

            </div>

            <!-- Footer Controls (Screen Only) -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800 flex items-center justify-end gap-3 shrink-0 print:hidden">
                <button type="button" @click="emit('close')"
                    class="py-2 px-4 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ t('common.cancel') }}
                </button>

                <button type="button" @click="printReceipt"
                    class="flex items-center gap-2 py-2 px-5 rounded-lg text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md transition-all">
                    <Printer class="w-4 h-4" />
                    <span>Print Invoice</span>
                </button>
            </div>

        </div>
    </div>
</template>

<style>
@media print {
    @page {
        margin: 0;
        size: auto;
    }
    body {
        background-color: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    /* Hide all non-printable elements */
    header, nav, sidebar, aside, footer, .print\:hidden {
        display: none !important;
    }
    /* Ensure printable-receipt is rendered as thermal receipt block */
    #printable-receipt {
        display: block !important;
        position: relative !important;
        left: 0 !important;
        top: 0 !important;
        width: 80mm !important;
        max-width: 80mm !important;
        margin: 0 auto !important;
        padding: 10px !important;
        background: #ffffff !important;
        color: #000000 !important;
    }
    #printable-receipt * {
        color: #000000 !important;
        background: transparent !important;
    }
}
</style>
