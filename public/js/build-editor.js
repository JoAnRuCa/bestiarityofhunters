/* ============================================================
    BUILD ARCHITECT — CORE ENGINE
   ============================================================ */

let weapons = [], armors = [], charms = [], decorationsData = [], skillsData = [];
let dataLoaded = false;
let skillMaxLevels = {};
let decoCache = { weapon: { 1: [], 2: [], 3: [] }, armor: { 1: [], 2: [], 3: [] } };

const weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];

async function loadBuildData() {
    try {
        const res = await fetch('api/build-data');
        if (!res.ok) throw new Error("Error HTTP: " + res.status);
        const data = await res.json();
        weapons = data.weapons; armors = data.armors; charms = data.charms;
        decorationsData = data.decorations; skillsData = data.skills;

        skillsData.forEach(s => { if (s.name && s.ranks) skillMaxLevels[s.name] = s.ranks.length; });
        decorationsData.forEach(d => {
            for (let lvl = d.slot; lvl <= 3; lvl++) {
                if (decoCache[d.kind] && decoCache[d.kind][lvl]) decoCache[d.kind][lvl].push(d);
            }
        });
        dataLoaded = true;
    } catch (e) { console.error("Data error:", e); }
}

let build = { weapon1: null, weapon2: null, head: null, chest: null, arms: null, waist: null, legs: null, charm: null };
let decorations = { weapon1: [], weapon2: [], head: [], chest: [], arms: [], waist: [], legs: [], charm: [] };
let activeSlot = null, activeDecoIndex = null, modalMode = null, currentList = [];

function getName(item) {
    if (!item) return "— Select Piece —";
    return item.name || item.weaponName || item.charmName || "Unnamed";
}

function extractSkills(item) {
    if (!item) return [];
    if (item.skill && item.level) return [{ name: item.skill.name, level: item.level }];
    if (Array.isArray(item.skills)) return item.skills.map(s => ({ name: s.skill.name, level: s.level }));
    return [];
}

function updateSelected() {
    for (const slot in build) {
        const nameEl = document.getElementById(slot + "_name");
        if (nameEl) nameEl.textContent = getName(build[slot]);
        renderSlots(slot);
    }
    renderSkillTotals();
    syncWeaponTags();
}

function renderSlots(slot) {
    const item = build[slot];
    const container = document.getElementById(slot + "_slots");
    if (!container) return;
    if (!item || !item.slots || item.slots.length === 0) {
        container.innerHTML = ""; container.classList.add("hidden"); return;
    }
    container.classList.remove("hidden");
    container.innerHTML = "";

    item.slots.forEach((slotLevel, index) => {
        const deco = decorations[slot][index];
        const row = document.createElement("div");
        row.className = "flex items-center justify-between p-2 rounded-xl border mb-1.5 " +
            (deco ? 'bg-[#6B8E23]/5 border-[#6B8E23]/30' : 'bg-gray-50 border-dashed border-gray-300');

        const decoName = deco ? deco.name : "Empty Slot (Lv" + slotLevel + ")";
        const textStyle = deco
            ? "text-[#2F2F2F] group-hover:text-[#6B8E23]"
            : "text-[#2F2F2F]/40 italic group-hover:text-[#6B8E23] group-hover:not-italic";

        row.innerHTML = `
            <div class="flex items-center gap-3 cursor-pointer group transition-colors" onclick="event.stopPropagation(); selectDecoration('${slot}', ${index}, ${slotLevel})">
                <div class="w-5 h-5 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[9px] font-black text-[#6B8E23] bg-white">
                    ${slotLevel}
                </div>
                <span class="text-xs font-bold transition-colors tracking-tight ${textStyle}">
                    ${decoName}
                </span>
            </div>
        `;

        if (deco) {
            const btn = document.createElement("button");
            btn.className = "text-gray-400 hover:text-red-500 p-1.5 transition-colors";
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>';
            btn.onclick = (e) => { e.stopPropagation(); clearSlot(slot, index); };
            row.appendChild(btn);
        }
        container.appendChild(row);
    });
}

function clearSlot(slot, index) {
    if (index === undefined || index === null) { build[slot] = null; decorations[slot] = []; }
    else { decorations[slot][index] = null; }
    updateSelected();
}

function renderSkillTotals() {
    const totals = {};
    for (const slot in build) {
        const item = build[slot]; if (!item) continue;
        extractSkills(item).forEach(s => { totals[s.name] = (totals[s.name] || 0) + s.level; });
        if (decorations[slot]) {
            decorations[slot].forEach(d => { if (d && d.skills) d.skills.forEach(ds => { totals[ds.skill.name] = (totals[ds.skill.name] || 0) + ds.level; }); });
        }
    }
    const box = document.getElementById("skillTotals");
    let html = "";
    Object.keys(totals).sort().forEach(name => {
        const lvl = totals[name], max = skillMaxLevels[name] || 5, capped = Math.min(lvl, max);
        const skill = skillsData.find(s => s.name === name);
        const desc = (skill?.ranks?.[capped - 1]) ? skill.ranks[capped - 1].description : "...";
        html += `
            <div class="mb-5 border-b border-[#6B8E23]/10 pb-4">
                <div class="flex justify-between items-end mb-1">
                    <span class="font-black uppercase text-[11px] text-[#2F2F2F]">${name}</span>
                    <span class="text-[#6B8E23] font-black text-xs">Lv ${capped}/${max}</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: ${(capped / max) * 100}%"></div>
                </div>
                <p class="text-[10px] leading-tight text-[#2F2F2F] font-bold uppercase opacity-80">${desc}</p>
            </div>`;
    });
    box.innerHTML = html || '<p class="italic text-xs opacity-50 text-center py-10 font-bold uppercase">No Skills Detected</p>';
}

function openSelector(slot) {
    if (!dataLoaded) return;
    activeSlot = slot; modalMode = "piece";
    let list = slot.includes("weapon") ? weapons : (slot === "charm" ? charms : armors.filter(a => a.kind === slot));
    currentList = list; renderList(list); openModal();
}

function selectDecoration(slot, index, lvl) {
    activeSlot = slot; activeDecoIndex = index; modalMode = "decoration";
    currentList = decoCache[slot.includes("weapon") ? "weapon" : "armor"][lvl];
    renderList(currentList); openModal();
}

function renderList(list) {
    const container = document.getElementById("modalList");
    container.innerHTML = "";
    list.forEach(item => {
        const div = document.createElement("div");
        div.className = "p-4 mb-2 bg-white border border-[#6B8E23]/10 rounded-2xl cursor-pointer transition-all group hover:border-[#6B8E23]/40";
        const skillsHtml = extractSkills(item).map(s => `<span class="text-[9px] font-bold text-[#6B8E23] bg-[#6B8E23]/10 px-2 py-0.5 rounded-full mr-1 inline-block uppercase">◈ ${s.name}</span>`).join("");
        div.innerHTML = `
            <div class="text-[#2F2F2F] font-black text-sm uppercase transition-colors group-hover:text-[#6B8E23]">${getName(item)}</div>
            <div class="mt-2 flex flex-wrap gap-1 opacity-70 transition-opacity group-hover:opacity-100">${skillsHtml}</div>
        `;
        div.onclick = () => {
            if (modalMode === "piece") { build[activeSlot] = item; decorations[activeSlot] = new Array(item.slots?.length || 0).fill(null); }
            else { decorations[activeSlot][activeDecoIndex] = item; }
            updateSelected(); closeModal();
        };
        container.appendChild(div);
    });
}

document.getElementById("searchInput").oninput = function () {
    const t = this.value.toLowerCase();
    renderList(currentList.filter(i => getName(i).toLowerCase().includes(t)));
};

function openModal() { document.getElementById("modal").classList.remove("hidden"); }
function closeModal() { document.getElementById("modal").classList.add("hidden"); }

/* LOGICA DE CIERRE EXTERNO */
document.getElementById('modal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});

// Tecla Escape para cerrar
document.addEventListener('keydown', (e) => { if (e.key === "Escape") closeModal(); });

function syncWeaponTags() {
    const cont = document.getElementById('tagContainer'); if (!cont) return;
    const mapping = { 'great-sword': 'Great Sword', 'long-sword': 'Long Sword', 'sword-and-shield': 'Sword and Shield', 'dual-blades': 'Dual Blades', 'hunting-horn': 'Hunting Horn', 'switch-axe': 'Switch Axe', 'charge-blade': 'Charge Blade', 'insect-glaive': 'Insect Glaive', 'light-bowgun': 'Light Bowgun', 'heavy-bowgun': 'Heavy Bowgun', 'bow': 'Bow', 'hammer': 'Hammer', 'lance': 'Lance', 'gunlance': 'Gunlance' };
    const equipped = [];
    if (build.weapon1?.type) equipped.push(mapping[build.weapon1.type]);
    if (build.weapon2?.type) equipped.push(mapping[build.weapon2.type]);
    cont.querySelectorAll('input[name="tags[]"]').forEach(cb => {
        const name = cb.getAttribute('data-name');
        if (weaponNames.includes(name)) {
            cb.checked = equipped.includes(name);
            const label = cb.nextElementSibling;
            if (cb.checked) label?.classList.add('text-[#6B8E23]', 'font-bold');
            else label?.classList.remove('text-[#6B8E23]', 'font-bold');
        }
    });
}


document.getElementById('forgeForm').onsubmit = function (e) {
    e.preventDefault();

    let hasErrors = false;
    const requiredSlots = ['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'];

    // 1. Validar Piezas
    requiredSlots.forEach(function (slot) {
        const container = document.getElementById('slot_container_' + slot);
        if (!build[slot]) {
            container.classList.remove('border-[#6B8E23]/10');
            container.classList.add('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
            hasErrors = true;
        } else {
            container.classList.remove('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
            container.classList.add('border-[#6B8E23]/10');
        }
    });

    // 2. Validar Playstyle
    const playstyleField = document.getElementById('playstyleField');
    const playstyleCont = document.getElementById('playstyle_container');
    if (!playstyleField.value.trim()) {
        playstyleCont.classList.add('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
        hasErrors = true;
    } else {
        playstyleCont.classList.remove('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
    }

    if (hasErrors) {
        alert("The forge requires a complete set. Please fill in the missing pieces and your strategy.");
        return;
    }

    // 3. Envío (Lógica existente)
    document.getElementById('buildDataInput').value = JSON.stringify(build);
    document.getElementById('decoDataInput').value = JSON.stringify(decorations);

    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                window.location.href = data.redirect_url || (window.location.origin + '/builds/' + data.slug);
            } else {
                alert("Forge error: " + (data.error || "Unknown error occurred."));
            }
        })
        .catch(function (err) {
            alert("The forge is offline.");
        });
};


function clearVisualErrors() {
    const requiredSlots = ['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'];
    requiredSlots.forEach(function (slot) {
        if (build[slot]) {
            const container = document.getElementById('slot_container_' + slot);
            if (container) {
                container.classList.remove('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
                container.classList.add('border-[#6B8E23]/10');
            }
        }
    });

    const playstyleField = document.getElementById('playstyleField');
    if (playstyleField && playstyleField.value.trim()) {
        document.getElementById('playstyle_container').classList.remove('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
    }
}



loadBuildData();