import"./auto-50b387db.js";import{C as $,c as w}from"./chartjs-plugin-datalabels.esm-6f583e48.js";function S(n){const e=document.getElementById("totalInnovatorWithGenderChart").getContext("2d"),l=Object.keys(n),i=l.map(s=>n[s].laki_laki||0),r=l.map(s=>n[s].perempuan||0),o=l.map(s=>n[s].outsourcing||0);new $(e,{type:"bar",plugins:[w],data:{labels:l,datasets:[{label:"Laki-laki",data:i,backgroundColor:"#006dd9",maxBarThickness:60},{label:"Perempuan",data:r,backgroundColor:"#db2d92",maxBarThickness:60},{label:"Outsource",data:o,backgroundColor:"#d8c600",maxBarThickness:60}]},options:{responsive:!1,plugins:{legend:{position:"top"},datalabels:{anchor:"end",align:"top"}},scales:{x:{title:{display:!0,text:"Tahun"}},y:{title:{display:!0,text:"Jumlah Innovator"},beginAtZero:!0}}}}),L(n)}function L(n){const e={};let l=0,i=0,r=0;Object.entries(n).forEach(([t,a])=>{const c=a.laki_laki||0,u=a.perempuan||0,p=a.outsourcing||0,k=c+u+p;e[t]=k,l+=c,i+=u,r+=p});const o=Object.keys(e).map(t=>parseInt(t)).sort((t,a)=>a-t),s={};for(let t=0;t<o.length;t++){const a=o[t],c=o[t+1],u=e[a]-e[c],p=(u/e[c]*100).toFixed(1);s[a]={absolute:u,percentage:p}}let d=o[0],m=o[0];o.forEach(t=>{e[t]>e[d]&&(d=t),e[t]<e[m]&&(m=t)});const b=(Object.values(e).reduce((t,a)=>t+a,0)/o.length).toFixed(0),g=l+i+r,x=(l/g*100).toFixed(1),y=(i/g*100).toFixed(1),v=(r/g*100).toFixed(1),f=`
        <div class="mt-4 p-4 bg-gray-100 rounded summary-card">
            <h3 class="text-lg font-semibold mb-3 text-center">Ringkasan Statistik Innovator</h3>
            <div class="container-fluid d-flex flex-row justify-content-between align-items-baseline flex-wrap">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2">Statistik Total:</h4>
                        <ul class="list-disc pl-5">
                            <li>Total keseluruhan: ${g.toLocaleString()} innovator</li>
                            <li>Rata-rata per tahun: ${parseInt(b).toLocaleString()} innovator</li>
                            <li>Tahun tertinggi: ${d} (${e[d].toLocaleString()} innovator)</li>
                            <li>Tahun terendah: ${m} (${e[m].toLocaleString()} innovator)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Distribusi Gender:</h4>
                        <ul class="list-disc pl-5">
                            <li>Laki-laki: ${l.toLocaleString()} (${x}%)</li>
                            <li>Perempuan: ${i.toLocaleString()} (${y}%)</li>
                            <li>Outsource: ${r.toLocaleString()} (${v}%)</li>
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
                                ${o.map((t,a)=>`
                                    <tr class="${a%2===0?"bg-white":"bg-gray-200"}">
                                        <td class="px-4 py-2">${t}</td>
                                        <td class="px-4 py-2">${e[t].toLocaleString()}</td>
                                        <td class="px-4 py-2">${a+1==o.length?"-":(s[t].absolute>=0?"+":"")+s[t].absolute.toLocaleString()}</td>
                                        <td class="px-4 py-2">${a+1==o.length?"-":s[t].percentage+"%"}</td>
                                    </tr>
                                `).join("")}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `,h=document.getElementById("chartSummary");h&&(h.innerHTML=f)}window.renderTotalInnovatorWithGenderChart=S;
