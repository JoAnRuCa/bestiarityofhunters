/* ============================================================
    BUILD ARCHITECT — CORE ENGINE
   ============================================================ */

let weapons = [], armors = [], charms = [], decorationsData = [], skillsData = [];
let dataLoaded = false;
let skillMaxLevels = {};
let decoCache = { weapon: { 1: [], 2: [], 3: [] }, armor: { 1: [], 2: [], 3: [] } };

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

async function loadBuildData() {
    try {
        const res = await fetch('api/build-data');
        if (!res.ok) throw new Error("Error HTTP: " + res.status);
        const data = await res.json();

        weapons = data.weapons;
        armors = data.armors;
        charms = data.charms;
        decorationsData = data.decorations;
        skillsData = data.skills;

        skillsData.forEach(s => {
            if (s.name && s.ranks) skillMaxLevels[s.name] = s.ranks.length;
        });

        decorationsData.forEach(d => {
            for (let lvl = d.slot; lvl <= 3; lvl++) {
                if (decoCache[d.kind] && decoCache[d.kind][lvl]) {
                    decoCache[d.kind][lvl].push(d);
                }
            }
        });
        dataLoaded = true;
    } catch (e) {
        console.error("Data error:", e);
    }
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

        if (build[slot]) {
            const errEl = document.getElementById(`error-${slot}`);
            if (errEl) {
                errEl.innerText = "";
                errEl.classList.add('hidden');
            }
        }
        renderSlots(slot);
    }
    renderSkillTotals();
    syncWeaponTags(); // Sincroniza los tags automáticamente al actualizar piezas
}

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
        row.className = "flex items-center justify-between p-2 rounded-xl border mb-1.5 transition-colors " +
            (deco ? 'bg-[#6B8E23]/5 border-[#6B8E23]/30' : 'bg-gray-50 border-dashed border-gray-300');

        const decoName = deco ? deco.name : "Empty Slot (Lv" + slotLevel + ")";
        const textStyle = deco
            ? "text-[#2F2F2F] group-hover:text-[#6B8E23]"
            : "text-[#2F2F2F]/40 italic group-hover:text-[#6B8E23]";

        row.innerHTML = `
            <div class="flex items-center gap-3 cursor-pointer group transition-colors w-full" onclick="event.stopPropagation(); selectDecoration('${slot}', ${index}, ${slotLevel})">
                <div class="w-5 h-5 flex-shrink-0 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[9px] font-black text-[#6B8E23] bg-white transition-colors group-hover:bg-[#6B8E23] group-hover:text-white">
                    ${slotLevel}
                </div>
                <span class="text-xs font-bold transition-colors tracking-tight ${textStyle}">
                    ${decoName}
                </span>
            </div>
        `;

        if (deco) {
            const btn = document.createElement("button");
            btn.className = "text-gray-400 hover:text-red-500 p-1.5 transition-colors flex-shrink-0";
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>';
            btn.onclick = (e) => { e.stopPropagation(); clearSlot(slot, index); };
            row.appendChild(btn);
        }
        container.appendChild(row);
    });
}

function clearSlot(slot, index) {
    if (index === undefined || index === null) {
        build[slot] = null;
        decorations[slot] = [];
    } else {
        decorations[slot][index] = null;
    }
    updateSelected();
}

function renderSkillTotals() {
    const totals = {};
    const weaponSkillNames = new Set();

    for (const slot in build) {
        if (slot === 'weapon2') continue;

        const item = build[slot];
        if (!item) continue;

        const isMainWeapon = (slot === 'weapon1');

        extractSkills(item).forEach(s => {
            const sName = s.name.trim(); // Limpieza de nombre
            totals[sName] = (totals[sName] || 0) + s.level;
            if (isMainWeapon) weaponSkillNames.add(sName);
        });

        if (decorations[slot]) {
            decorations[slot].forEach(d => {
                if (d && d.skills) {
                    d.skills.forEach(ds => {
                        const dName = ds.skill.name.trim(); // Limpieza de nombre
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

        // PRIORIDAD 1: ¿Es de arma? (Las de arma arriba)
        if (isWeaponA !== isWeaponB) {
            return isWeaponB - isWeaponA;
        }

        // PRIORIDAD 2: Nivel (De mayor a menor)
        if (totals[b] !== totals[a]) {
            return totals[b] - totals[a];
        }

        // PRIORIDAD 3: Alfabético
        return a.localeCompare(b);

    }).forEach(name => {
        const lvl = totals[name];
        const max = skillMaxLevels[name] || 5;
        const capped = Math.min(lvl, max);
        const skill = skillsData.find(s => s.name === name);
        const desc = (skill?.ranks?.[capped - 1]) ? (skill.ranks[capped - 1].description || skill.ranks[capped - 1].desc) : "...";

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

document.getElementById("searchInput").oninput = function () {
    // 1. Limpiamos la búsqueda: pasamos a minúsculas, quitamos espacios y guiones
    const t = this.value.toLowerCase().trim().replace(/[\s-]/g, '');

    renderList(currentList.filter(item => {
        // 2. Limpiamos también los datos del item para comparar "limpio contra limpio"
        const itemName = getName(item).toLowerCase().replace(/[\s-]/g, '');
        const itemKind = item.kind ? item.kind.toLowerCase().replace(/[\s-]/g, '') : '';

        // 3. Comprobamos habilidades
        const skillMatch = extractSkills(item).some(s =>
            s.name.toLowerCase().replace(/[\s-]/g, '').includes(t)
        );

        // Devolvemos true si coincide en nombre, categoría (kind) o habilidades
        return itemName.includes(t) || itemKind.includes(t) || skillMatch;
    }));
};

function openModal() { document.getElementById("modal").classList.remove("hidden"); }
function closeModal() { document.getElementById("modal").classList.add("hidden"); document.getElementById("searchInput").value = ""; }

document.getElementById('modal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
document.addEventListener('keydown', (e) => { if (e.key === "Escape") closeModal(); });

function syncWeaponTags() {
    const container = document.getElementById('tagContainer');
    if (!container) return;

    const equippedNames = [];

    // USAMOS .kind EN LUGAR DE .type
    if (build.weapon1 && build.weapon1.kind) {
        const name = weaponTagMap[build.weapon1.kind];
        if (name) equippedNames.push(name);
    }
    if (build.weapon2 && build.weapon2.kind) {
        const name = weaponTagMap[build.weapon2.kind];
        if (name) equippedNames.push(name);
    }

    container.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
        const tagName = checkbox.getAttribute('data-name');

        // Verificamos si es un tag de arma
        if (weaponNames.includes(tagName)) {
            const isEquipped = equippedNames.includes(tagName);
            checkbox.checked = isEquipped;

            // Feedback visual
            const span = checkbox.nextElementSibling;
            if (span) {
                if (isEquipped) {
                    span.classList.add('text-[#6B8E23]', 'font-bold');
                } else {
                    span.classList.remove('text-[#6B8E23]', 'font-bold');
                }
            }
        }
    });
}

document.getElementById('forgeForm').onsubmit = function (e) {
    e.preventDefault();
    document.querySelectorAll('[id^="error-"]').forEach(el => { el.innerText = ""; el.classList.add('hidden'); });

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
            if (res.status === 422) {
                Object.keys(data.errors).forEach(key => {
                    const cleanKey = key.includes('.') ? key.split('.').pop() : key;
                    const errorEl = document.getElementById(`error-${cleanKey}`);
                    if (errorEl) {
                        errorEl.innerText = "• " + data.errors[key][0];
                        errorEl.classList.remove('hidden');
                    }
                });
                const firstError = document.querySelector('[id^="error-"]:not(.hidden)');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else if (res.ok && data.success) {
                window.location.href = data.redirect_url;
            }
        })
        .catch(err => console.error("Forge error:", err));
};

loadBuildData();