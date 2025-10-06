import"./totalInnovatorCategories-c2862f7b.js";import{C as k,d as S}from"./chartjs-plugin-datalabels.esm-766c20d8.js";function P(i){const a=document.getElementById("totalInnovatorWithGenderChart").getContext("2d"),r=Object.keys(i),c=r.map(l=>i[l].laki_laki||0);console.log(c);const d=r.map(l=>i[l].perempuan||0),o=r.map(l=>i[l].outsourcing||0);new k(a,{type:"bar",plugins:[S],data:{labels:r,datasets:[{label:"Laki-laki",data:c,backgroundColor:"#006dd9",maxBarThickness:60},{label:"Perempuan",data:d,backgroundColor:"#db2d92",maxBarThickness:60},{label:"Outsource",data:o,backgroundColor:"#d8c600",maxBarThickness:60}]},options:{responsive:!1,plugins:{legend:{position:"top"},datalabels:{anchor:"end",align:"top"}},scales:{x:{title:{display:!0,text:"Tahun"}},y:{title:{display:!0,text:"Jumlah Innovator"},beginAtZero:!0}}}}),L(i)}function L(i){const a={};let r=0,c=0,d=0;Object.entries(i).forEach(([t,s])=>{const e=s.laki_laki||0,n=s.perempuan||0,u=s.outsourcing||0,h=e+n+u;a[t]=h,r+=e,c+=n,d+=u});const o=Object.keys(a).map(t=>parseInt(t)).sort((t,s)=>s-t),l={};for(let t=0;t<o.length;t++){const s=o[t],e=o[t+1],n=a[s]-a[e],u=(n/a[e]*100).toFixed(1);l[s]={absolute:n,percentage:u}}let m=o[0],g=o[0];o.forEach(t=>{a[t]>a[m]&&(m=t),a[t]<a[g]&&(g=t)});const y=(Object.values(a).reduce((t,s)=>t+s,0)/o.length).toFixed(0),p=r+c+d,v=(r/p*100).toFixed(1),x=(c/p*100).toFixed(1),f=(d/p*100).toFixed(1),w=`
        <div class="mt-4 p-4 bg-gray-100 rounded summary-card">
            <h3 class="text-lg font-semibold mb-3 text-center">Ringkasan Statistik Innovator</h3>

            <div class="container-fluid d-flex flex-row justify-content-between align-items-baseline flex-wrap">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2">Statistik Total:</h4>
                        <ul class="list-disc pl-5">
                            <li>Total keseluruhan: ${p.toLocaleString()} innovator</li>
                            <li>Rata-rata per tahun: ${parseInt(y).toLocaleString()} innovator</li>
                            <li>Tahun tertinggi: ${m} (${a[m].toLocaleString()} innovator)</li>
                            <li>Tahun terendah: ${g} (${a[g].toLocaleString()} innovator)</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium mb-2">Distribusi Gender:</h4>
                        <ul class="list-disc pl-5">
                            <li>Laki-laki: ${r.toLocaleString()} (${v}%)</li>
                            <li>Perempuan: ${c.toLocaleString()} (${x}%)</li>
                            <li>Perempuan: ${d.toLocaleString()} (${f}%)</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="font-medium mb-2">Pertumbuhan Tahunan:</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2">Tahun</th>
                                    <th class="px-4 py-2">Jumlah</th>
                                    <th class="px-4 py-2">Pertumbuhan</th>
                                    <th class="px-4 py-2">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${o.map((t,s)=>`
                                    <tr class="${s%2===0?"bg-white":"bg-gray-200"}">
                                        <td class="px-4 py-2">${t}</td>
                                        <td class="px-4 py-2">${a[t].toLocaleString()}</td>
                                        <td class="px-4 py-2">${s+1==o.length?"-":(l[t].absolute>=0?"+":"")+l[t].absolute.toLocaleString()}</td>
                                        <td class="px-4 py-2">${s+1==o.length?"-":l[t].percentage+"%"}</td>
                                    </tr>
                                `).join("")}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                <h4 class="font-medium mb-2">Pertumbuhan Inovator per Event:</h4>
                ${(()=>{const t=Array.isArray(window.growthPerEventData)?[...window.growthPerEventData]:[];return t.sort((e,n)=>String(e.event)===String(n.event)?(e.year??0)-(n.year??0):String(e.event).localeCompare(String(n.event))),t.length?`
                        <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                            <tr class="bg-gray-2 00">
                                <th class="px-4 py-2">Event</th>
                                <th class="px-4 py-2">Jumlah (Tahun)</th>
                                <th class="px-4 py-2">Pertumbuhan</th>
                                <th class="px-4 py-2">Persentase</th>
                            </tr>
                            </thead>
                            <tbody>${t.map((e,n)=>{const u=n%2===0?"bg-white":"bg-gray-200",h=e.growth_abs===null||e.growth_abs===void 0?"-":`${Number(e.growth_abs)>=0?"+":""}${Number(e.growth_abs).toLocaleString()}`,$=e.growth_pct===null||e.growth_pct===void 0?"-":`${Number(e.growth_pct).toFixed(1)}%`;return`
                        <tr class="${u}">
                            <td class="px-4 py-2">${e.event??"-"}</td>
                            <td class="px-4 py-2">${Number(e.total??0).toLocaleString()} (${e.year??"-"})</td>
                            <td class="px-4 py-2">${h}</td>
                            <td class="px-4 py-2">${$}</td>
                        </tr>`}).join("")}</tbody>
                        </table>
                        </div>`:'<div class="text-muted small px-2">Belum ada data pertumbuhan per event.</div>'})()}
                </div>
            </div>
        </div>
    `,b=document.getElementById("chartSummary");b&&(b.innerHTML=w)}window.renderTotalInnovatorWithGenderChart=P;
