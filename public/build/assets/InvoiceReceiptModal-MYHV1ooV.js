import{c as g}from"./createLucideIcon-C56Nry2A.js";import{g as I,c as s,a as t,e as b,u as l,t as a,F as h,r as R,f as p,i,L as z,o as d}from"./app-Xp3vVa8Q.js";import{R as F}from"./receipt-LMvKLe4s.js";import{X as P}from"./x-C24IxFD_.js";const T=[["rect",{width:"20",height:"12",x:"2",y:"6",rx:"2",key:"9lu3g6"}],["circle",{cx:"12",cy:"12",r:"2",key:"1c9p78"}],["path",{d:"M6 12h.01M18 12h.01",key:"113zkx"}]],Pt=g("banknote",T);const $=[["path",{d:"M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2",key:"143wyd"}],["path",{d:"M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6",key:"1itne7"}],["rect",{x:"6",y:"14",width:"12",height:"8",rx:"1",key:"1ue0tg"}]],E=g("printer",$);const O=[["rect",{width:"5",height:"5",x:"3",y:"3",rx:"1",key:"1tu5fj"}],["rect",{width:"5",height:"5",x:"16",y:"3",rx:"1",key:"1v8r4q"}],["rect",{width:"5",height:"5",x:"3",y:"16",rx:"1",key:"1x03jg"}],["path",{d:"M21 16h-3a2 2 0 0 0-2 2v3",key:"177gqh"}],["path",{d:"M21 21v.01",key:"ents32"}],["path",{d:"M12 7v3a2 2 0 0 1-2 2H7",key:"8crl2c"}],["path",{d:"M3 12h.01",key:"nlz23k"}],["path",{d:"M12 3h.01",key:"n36tog"}],["path",{d:"M12 16v.01",key:"133mhm"}],["path",{d:"M16 12h1",key:"1slzba"}],["path",{d:"M21 12v.01",key:"1lwtk9"}],["path",{d:"M12 21v-1",key:"1880an"}]],Tt=g("qr-code",O),S={key:0,class:"fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm print:p-0 print:bg-white print:static print:block print:inset-auto print:w-full print:h-auto"},B={class:"relative w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh] print:max-h-none print:shadow-none print:border-none print:w-full print:max-w-[80mm] print:mx-auto print:bg-white print:text-black print:overflow-visible"},H={class:"px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800 flex items-center justify-between shrink-0 print:hidden"},L={class:"flex items-center gap-2"},D={id:"printable-receipt",class:"p-6 overflow-y-auto space-y-4 font-mono text-gray-800 dark:text-gray-200 print:text-black print:p-3 print:overflow-visible print:bg-white"},q={class:"text-center border-b border-dashed border-gray-300 dark:border-gray-600 pb-4 print:border-black"},K={class:"text-xl font-extrabold uppercase tracking-wide text-gray-900 dark:text-gray-100 print:text-black print:text-lg"},A={class:"text-xs text-gray-500 dark:text-gray-400 mt-1 print:text-black"},U={class:"text-xs text-gray-500 dark:text-gray-400 print:text-black"},W={class:"text-xs space-y-1 border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black"},Y={class:"flex justify-between"},Q={class:"font-bold font-mono print:text-black"},V={class:"flex justify-between"},G={class:"print:text-black"},X={class:"flex justify-between"},J={class:"print:text-black"},Z={class:"flex justify-between"},tt={class:"print:text-black"},et={class:"flex justify-between items-center pt-1"},rt={class:"font-bold uppercase inline-flex items-center gap-1"},at={key:0,class:"text-red-600 dark:text-red-400 print:text-black"},ot={key:1,class:"text-emerald-600 dark:text-emerald-400 print:text-black"},nt={class:"border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black"},st={class:"w-full text-xs text-left"},dt={class:"divide-y divide-gray-100 dark:divide-gray-700/50 print:divide-black"},it={class:"py-2 pr-2 font-medium print:text-black"},lt={class:"py-2 text-center print:text-black"},pt={class:"py-2 text-right print:text-black"},ct={class:"py-2 text-right font-bold print:text-black"},xt={class:"space-y-1.5 text-xs border-b border-dashed border-gray-300 dark:border-gray-600 pb-3 print:border-black"},bt={class:"flex justify-between"},gt={class:"print:text-black"},yt={key:0,class:"flex justify-between"},mt={class:"print:text-black"},ht={key:1,class:"flex justify-between text-red-600 dark:text-red-400 print:text-black"},kt={class:"print:text-black"},ut={class:"flex justify-between items-baseline pt-2 border-t border-gray-200 dark:border-gray-700 print:border-black"},ft={class:"text-right"},vt={class:"text-lg font-extrabold text-emerald-700 dark:text-emerald-400 print:text-black"},wt={class:"block text-[11px] font-bold text-gray-500 dark:text-gray-400 print:text-black"},_t={class:"flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700 print:border-black"},Nt={class:"font-bold print:text-black"},Mt={class:"flex justify-between"},Ct={class:"font-bold text-emerald-600 dark:text-emerald-400 print:text-black"},jt={class:"p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800 flex items-center justify-end gap-3 shrink-0 print:hidden"},k=4100,$t={__name:"InvoiceReceiptModal",props:{show:{type:Boolean,default:!1},order:{type:Object,default:null}},emits:["close"],setup(r,{emit:u}){const c=r,y=u,{t:f}=I(),v=z(),x=i(()=>v.props.settings||{}),w=i(()=>x.value?.store_name||"CHOUERNCHYWORN KONG"),_=i(()=>x.value?.store_address||"Phnom Penh, Cambodia"),N=i(()=>x.value?.store_phone||"+855 12 345 678"),M=i(()=>c.order?Math.round(Number(c.order.grand_total)*k):0),m=n=>new Intl.NumberFormat("km-KH").format(n)+" ៛",C=n=>n?new Date(n).toLocaleString("en-US",{month:"short",day:"numeric",year:"numeric",hour:"numeric",minute:"2-digit"}):new Date().toLocaleString(),j=()=>{const n=document.getElementById("printable-receipt");if(!n){window.print();return}let e=document.getElementById("pos-print-iframe");e||(e=document.createElement("iframe"),e.id="pos-print-iframe",e.style.position="fixed",e.style.right="0",e.style.bottom="0",e.style.width="0",e.style.height="0",e.style.border="0",document.body.appendChild(e));const o=e.contentWindow.document;o.open(),o.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice - ${c.order?.invoice_no||"Receipt"}</title>
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
            ${n.innerHTML}
        </body>
        </html>
    `),o.close(),setTimeout(()=>{e.contentWindow.focus(),e.contentWindow.print()},250)};return(n,e)=>r.show&&r.order?(d(),s("div",S,[t("div",B,[t("div",H,[t("div",L,[b(l(F),{class:"w-5 h-5 text-emerald-600 dark:text-emerald-400"}),e[2]||(e[2]=t("h3",{class:"font-bold text-gray-900 dark:text-gray-100 text-base"},"Invoice Receipt",-1))]),t("button",{onClick:e[0]||(e[0]=o=>y("close")),class:"p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"},[b(l(P),{class:"w-5 h-5"})])]),t("div",D,[t("div",q,[t("h2",K,a(w.value),1),t("p",A,a(_.value),1),t("p",U,"Tel: "+a(N.value),1),e[3]||(e[3]=t("div",{class:"mt-3 py-1 bg-gray-100 dark:bg-gray-700/50 rounded text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 print:bg-transparent print:border print:border-black print:text-black"}," OFFICIAL RECEIPT / វិក្កយបត្រ ",-1))]),t("div",W,[t("div",Y,[e[4]||(e[4]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Invoice No:",-1)),t("span",Q,a(r.order.invoice_no),1)]),t("div",V,[e[5]||(e[5]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Date & Time:",-1)),t("span",G,a(C(r.order.order_date)),1)]),t("div",X,[e[6]||(e[6]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Cashier:",-1)),t("span",J,a(r.order.user?.name||"Cashier"),1)]),t("div",Z,[e[7]||(e[7]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Customer:",-1)),t("span",tt,a(r.order.customer?.name||"Walk-in"),1)]),t("div",et,[e[8]||(e[8]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Payment Method:",-1)),t("span",rt,[r.order.payment_method==="khqr"||r.order.payment_method==="bank"?(d(),s("span",at," KHQR (Bakong) ")):(d(),s("span",ot," CASH "))])])]),t("div",nt,[t("table",st,[e[9]||(e[9]=t("thead",null,[t("tr",{class:"border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 print:text-black print:border-black"},[t("th",{class:"py-1"},"ITEM"),t("th",{class:"py-1 text-center"},"QTY"),t("th",{class:"py-1 text-right"},"PRICE"),t("th",{class:"py-1 text-right"},"TOTAL")])],-1)),t("tbody",dt,[(d(!0),s(h,null,R(r.order.items,o=>(d(),s("tr",{key:o.id},[t("td",it,a(o.product?.name||"Product #"+o.product_id),1),t("td",lt,a(o.quantity),1),t("td",pt,"$"+a(Number(o.price).toFixed(2)),1),t("td",ct,"$"+a(Number(o.subtotal||o.price*o.quantity).toFixed(2)),1)]))),128))])])]),t("div",xt,[t("div",bt,[e[10]||(e[10]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Subtotal:",-1)),t("span",gt,"$"+a(Number(r.order.subtotal).toFixed(2)),1)]),Number(r.order.tax)>0?(d(),s("div",yt,[e[11]||(e[11]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Tax:",-1)),t("span",mt,"$"+a(Number(r.order.tax).toFixed(2)),1)])):p("",!0),Number(r.order.discount)>0?(d(),s("div",ht,[e[12]||(e[12]=t("span",{class:"print:text-black"},"Discount:",-1)),t("span",kt,"-$"+a(Number(r.order.discount).toFixed(2)),1)])):p("",!0),t("div",ut,[e[13]||(e[13]=t("span",{class:"text-sm font-extrabold uppercase print:text-black"},"Grand Total:",-1)),t("div",ft,[t("span",vt,"$"+a(Number(r.order.grand_total).toFixed(2)),1),t("span",wt,a(m(M.value)),1)])]),r.order.payment_method==="cash"&&r.order.cash_received?(d(),s(h,{key:2},[t("div",_t,[e[14]||(e[14]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Cash Received:",-1)),t("span",Nt,"$"+a(Number(r.order.cash_received).toFixed(2)),1)]),t("div",Mt,[e[15]||(e[15]=t("span",{class:"text-gray-500 dark:text-gray-400 print:text-black"},"Change Due:",-1)),t("span",Ct," $"+a(Number(r.order.change_amount||r.order.cash_received-r.order.grand_total).toFixed(2))+" ("+a(m(Math.round(Math.max(0,r.order.cash_received-r.order.grand_total)*k)))+") ",1)])],64)):p("",!0)]),e[16]||(e[16]=t("div",{class:"text-center pt-2 space-y-1 print:text-black"},[t("p",{class:"text-xs font-bold print:text-black"},"THANK YOU FOR YOUR BUSINESS!"),t("p",{class:"text-[11px] text-gray-500 dark:text-gray-400 print:text-black"},"សូមអរគុណ សម្រាប់ការជាវទំនិញ!"),t("p",{class:"text-[10px] text-gray-400 dark:text-gray-500 print:text-black pt-1"},"Powered by POS System")],-1))]),t("div",jt,[t("button",{type:"button",onClick:e[1]||(e[1]=o=>y("close")),class:"py-2 px-4 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"},a(l(f)("common.cancel")),1),t("button",{type:"button",onClick:j,class:"flex items-center gap-2 py-2 px-5 rounded-lg text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md transition-all"},[b(l(E),{class:"w-4 h-4"}),e[17]||(e[17]=t("span",null,"Print Invoice",-1))])])])])):p("",!0)}};export{Pt as B,E as P,Tt as Q,$t as _};
