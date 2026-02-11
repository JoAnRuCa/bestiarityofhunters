/* ============================================================
    BUILD ARCHITECT — CORE ENGINE (FINAL VERSION)
   ============================================================ */

let weapons = [], armors = [], charms = [], decorationsData = [], skillsData = [];
let dataLoaded = false;
let skillMaxLevels = {};
let decoCache = { weapon: { 1: [], 2: [], 3: [], 4: [] }, armor: { 1: [], 2: [], 3: [], 4: [] } };

const weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];

const weaponTagMap = {
    'great-sword': 'Great Sword',
    'long-sword': 'Long Sword',
    'sword-shield': 'Sword and Shield',
    'dual-blades': 'Dual Blades',
    'hammer': 'Hammer',
    'hunting-horn': 'Hunting Horn',
    'lance': 'Lance',
    'gunlance': 'Gunlance',
    'switch-axe': 'Switch Axe',
    'charge-blade': 'Charge Blade',
    'insect-glaive': 'Insect Glaive',
    'bow': 'Bow',
    'light-bowgun': 'Light Bowgun',
    'heavy-bowgun': 'Heavy Bowgun'
};

// Carga de datos desde la API
async function loadBuildData() {
    try {
        const res = await fetch('api/build-data'); // Ajusta esta ruta según tu api.php
        if (!res.ok) throw new Error("Error HTTP: " + res.status);
        const data = await res.json();

        weapons = data.weapons;
        armors = data.armors;
        charms = data.charms;
        decorationsData = data.decorations;
        skillsData = data.skills;

        // Mapeo de niveles máximos
        skillsData.forEach(s => {
            if (s.name && s.ranks) skillMaxLevels[s.name.trim()] = s.ranks.length;
        });

        // Cache de decoraciones: Una joya de nivel 1 entra en slots 1, 2, 3 y 4
        decorationsData.forEach(d => {
            const kind = d.kind === 'weapon' ? 'weapon' : 'armor';
            for (let slotLvl = d.slot; slotLvl <= 4; slotLvl++) {
                if (decoCache[kind][slotLvl]) {
                    decoCache[kind][slotLvl].push(d);
                }
            }
        });

        dataLoaded = true;
    } catch (e) {
        console.error("Data load error:", e);
    }
}

// Estado de la Build
let build = { weapon1: null, weapon2: null, head: null, chest: null, arms: null, waist: null, legs: null, charm: null };
let decorations = { weapon1: [], weapon2: [], head: [], chest: [], arms: [], waist: [], legs: [], charm: [] };
let activeSlot = null, activeDecoIndex = null, modalMode = null, currentList = [];

/* --- Funciones de Utilidad --- */

function getName(item) {
    if (!item) return "— Select Piece —";
    return item.name || item.weaponName || item.charmName || "Unnamed";
}

function extractSkills(item) {
    if (!item) return [];
    if (item.skill && item.level) return [{ name: item.skill.name, level: item.level }];
    if (Array.isArray(item.skills)) return item.skills.map(s => ({
        name: s.skill?.name || s.name,
        level: s.level
    }));
    return [];
}

function updateSelected() {
    for (const slot in build) {
        const nameEl = document.getElementById(slot + "_name");
        if (nameEl) nameEl.textContent = getName(build[slot]);

        // Limpiar errores visuales al seleccionar algo
        if (build[slot]) {
            const errEl = document.getElementById(`error-${slot}`);
            if (errEl) { errEl.innerText = ""; errEl.classList.add('hidden'); }
        }
        renderSlots(slot);
    }
    renderSkillTotals();
    syncWeaponTags();
}

/* --- Renderizado de UI --- */

function renderSlots(slot) {
    const item = build[slot];
    const container = document.getElementById(slot + "_slots");
    if (!container) return;

    if (!item || !item.slots || item.slots.length === 0) {
        container.innerHTML = "";
        container.classList.add("hidden");
        return;
    }

    container.classList.remove("hidden");
    container.innerHTML = "";

    item.slots.forEach((slotLevel, index) => {
        const deco = decorations[slot][index];
        const row = document.createElement("div");
        row.className = `flex items-center justify-between p-2 rounded-xl border mb-1.5 transition-all ${deco ? 'bg-[#6B8E23]/10 border-[#6B8E23]/30 shadow-sm' : 'bg-white/40 border-dashed border-gray-300'
            }`;

        const decoName = deco ? deco.name : `Empty Slot (Lv${slotLevel})`;
        const textStyle = deco ? "text-[#2F2F2F] font-bold" : "text-gray-400 italic";

        row.innerHTML = `
            <div class="flex items-center gap-3 cursor-pointer group w-full" onclick="event.stopPropagation(); selectDecoration('${slot}', ${index}, ${slotLevel})">
                <div class="w-6 h-6 flex-shrink-0 rounded-md border-2 border-[#6B8E23] flex items-center justify-center text-[10px] font-black text-[#6B8E23] bg-white group-hover:bg-[#6B8E23] group-hover:text-white transition-colors">
                    ${slotLevel}
                </div>
                <span class="text-xs transition-colors group-hover:text-[#6B8E23] ${textStyle}">
                    ${decoName}
                </span>
            </div>
        `;

        if (deco) {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "text-gray-400 hover:text-red-500 p-1 transition-colors";
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>';
            btn.onclick = (e) => { e.stopPropagation(); clearDeco(slot, index); };
            row.appendChild(btn);
        }
        container.appendChild(row);
    });
}

function renderSkillTotals() {
    const totals = {};
    const weaponSkillNames = new Set();

    for (const slot in build) {
        if (slot === 'weapon2') continue; // Lógica estándar: solo cuenta arma activa
        const item = build[slot];
        if (!item) continue;

        const isMainWeapon = (slot === 'weapon1');

        extractSkills(item).forEach(s => {
            const sName = s.name.trim();
            totals[sName] = (totals[sName] || 0) + s.level;
            if (isMainWeapon) weaponSkillNames.add(sName);
        });

        if (decorations[slot]) {
            decorations[slot].forEach(d => {
                if (d && d.skills) {
                    d.skills.forEach(ds => {
                        const dName = ds.skill?.name?.trim() || ds.name?.trim();
                        totals[dName] = (totals[dName] || 0) + ds.level;
                        if (isMainWeapon) weaponSkillNames.add(dName);
                    });
                }
            });
        }
    }

    const box = document.getElementById("skillTotals");
    let html = "";

    Object.keys(totals).sort((a, b) => {
        const isWeaponA = weaponSkillNames.has(a) ? 1 : 0;
        const isWeaponB = weaponSkillNames.has(b) ? 1 : 0;
        if (isWeaponA !== isWeaponB) return isWeaponB - isWeaponA;
        if (totals[b] !== totals[a]) return totals[b] - totals[a];
        return a.localeCompare(b);
    }).forEach(name => {
        const lvl = totals[name];
        const max = skillMaxLevels[name] || 5;
        const capped = Math.min(lvl, max);
        const skill = skillsData.find(s => s.name.trim() === name);
        const desc = (skill?.ranks?.[capped - 1]) ? (skill.ranks[capped - 1].description || skill.ranks[capped - 1].desc) : "Effect active.";

        html += `
            <div class="mb-5 border-b border-[#6B8E23]/10 pb-4">
                <div class="flex justify-between items-end mb-1">
                    <span class="font-black uppercase text-[10px] text-[#2F2F2F] tracking-tight">${name}</span>
                    <span class="text-[#6B8E23] font-black text-[10px]">Lv ${capped}/${max}</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: ${(capped / max) * 100}%"></div>
                </div>
                <p class="text-[10px] leading-tight text-[#2F2F2F]/70 font-bold uppercase">${desc}</p>
            </div>`;
    });

    box.innerHTML = html || '<p class="italic text-xs opacity-50 text-center py-10 font-bold uppercase">No Skills Detected</p>';
}

/* --- Gestión de Inventario / Modal --- */

function openSelector(slot) {
    if (!dataLoaded) return;
    activeSlot = slot; modalMode = "piece";
    let list = slot.includes("weapon") ? weapons : (slot === "charm" ? charms : armors.filter(a => a.kind === slot));
    currentList = list; renderList(list); openModal();
}

function selectDecoration(slot, index, lvl) {
    activeSlot = slot; activeDecoIndex = index; modalMode = "decoration";
    const kind = slot.includes("weapon") ? "weapon" : "armor";
    currentList = decoCache[kind][lvl] || [];
    renderList(currentList); openModal();
}

function renderList(list) {
    const container = document.getElementById("modalList");
    container.innerHTML = "";
    list.forEach(item => {
        const div = document.createElement("div");
        div.className = "p-4 mb-2 bg-white border border-[#6B8E23]/10 rounded-2xl cursor-pointer transition-all hover:border-[#6B8E23] hover:shadow-md group";
        const skillsHtml = extractSkills(item).map(s => `<span class="text-[9px] font-bold text-[#6B8E23] bg-[#6B8E23]/10 px-2 py-0.5 rounded-full mr-1 uppercase">◈ ${s.name}</span>`).join("");

        div.innerHTML = `
            <div class="text-[#2F2F2F] font-black text-sm uppercase group-hover:text-[#6B8E23]">${getName(item)}</div>
            <div class="mt-2 flex flex-wrap gap-1">${skillsHtml}</div>
        `;
        div.onclick = () => {
            if (modalMode === "piece") {
                build[activeSlot] = item;
                decorations[activeSlot] = new Array(item.slots?.length || 0).fill(null);
            } else {
                decorations[activeSlot][activeDecoIndex] = item;
            }
            updateSelected();
            closeModal();
        };
        container.appendChild(div);
    });
}

/* --- Lógica de Borrado y Sincronización --- */

function clearSlot(slot) {
    build[slot] = null;
    decorations[slot] = [];
    updateSelected();
}

function clearDeco(slot, index) {
    decorations[slot][index] = null;
    updateSelected();
}

function syncWeaponTags() {
    const container = document.getElementById('tagContainer');
    if (!container) return;
    const equippedNames = [];
    if (build.weapon1?.kind) equippedNames.push(weaponTagMap[build.weapon1.kind]);
    if (build.weapon2?.kind) equippedNames.push(weaponTagMap[build.weapon2.kind]);

    container.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
        const tagName = checkbox.getAttribute('data-name');
        if (weaponNames.includes(tagName)) {
            checkbox.checked = equippedNames.includes(tagName);
            const span = checkbox.nextElementSibling;
            if (span) {
                if (checkbox.checked) span.classList.add('text-[#6B8E23]', 'font-bold');
                else span.classList.remove('text-[#6B8E23]', 'font-bold');
            }
        }
    });
}

/* --- Eventos de Búsqueda y Modal --- */

document.getElementById("searchInput").oninput = function () {
    // 1. Limpiamos la búsqueda: "Great Sword" -> "greatsword"
    const searchTerm = this.value.toLowerCase().trim().replace(/[\s-]/g, '');

    if (!searchTerm) {
        renderList(currentList);
        return;
    }

    const filtered = currentList.filter(item => {
        // 2. Limpiamos el nombre del ítem: "Buster Sword I" -> "busterswordi"
        const itemName = getName(item).toLowerCase().replace(/[\s-]/g, '');

        // 3. Limpiamos el tipo (kind): "great-sword" -> "greatsword"
        const itemKind = (item.kind || "").toLowerCase().replace(/[\s-]/g, '');

        // 4. Comprobamos habilidades (limpiando cada una)
        const skillMatch = extractSkills(item).some(s => {
            const sName = (s.name || "").toLowerCase().replace(/[\s-]/g, '');
            return sName.includes(searchTerm);
        });

        // Retornamos true si coincide en cualquiera de los 3 campos
        return itemName.includes(searchTerm) ||
            itemKind.includes(searchTerm) ||
            skillMatch;
    });

    renderList(filtered);
};

function openModal() { document.getElementById("modal").classList.remove("hidden"); document.getElementById("searchInput").focus(); }
function closeModal() { document.getElementById("modal").classList.add("hidden"); document.getElementById("searchInput").value = ""; }
document.getElementById('modal').onclick = function (e) { if (e.target === this) closeModal(); };
document.addEventListener('keydown', (e) => { if (e.key === "Escape") closeModal(); });

/* --- Envío del Formulario (AJAX) --- */

document.getElementById('forgeForm').onsubmit = function (e) {
    e.preventDefault();

    // Reset errores
    document.querySelectorAll('[id^="error-"]').forEach(el => { el.innerText = ""; el.classList.add('hidden'); });

    // Preparar inputs ocultos para el controlador PHP
    document.getElementById('buildDataInput').value = JSON.stringify(build);
    document.getElementById('decoDataInput').value = JSON.stringify(decorations);

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
        .then(async res => {
            const data = await res.json();
            if (res.status === 422) { // Errores de validación Laravel
                Object.keys(data.errors).forEach(key => {
                    const cleanKey = key.includes('.') ? key.split('.').pop() : key;
                    const errorEl = document.getElementById(`error-${cleanKey}`);
                    if (errorEl) {
                        errorEl.innerText = "• " + data.errors[key][0];
                        errorEl.classList.remove('hidden');
                    }
                });
                const firstErr = document.querySelector('[id^="error-"]:not(.hidden)');
                if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else if (res.ok && data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert("Forge Error: " + (data.error || "Unknown server error"));
            }
        })
        .catch(err => console.error("Critical Forge Error:", err));
};

// Arrancar motor
loadBuildData();