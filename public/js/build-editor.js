/* ============================================================
    BUILD ARCHITECT — CORE ENGINE (Tailwind 3.0.18 Optimized)
   ============================================================ */

// Data Stores
let weapons = [], armors = [], charms = [], decorationsData = [], skillsData = [];
let dataLoaded = false;

// Dictionaries for fast lookup
let skillMaxLevels = {};
let decoCache = {
    weapon: { 1: [], 2: [], 3: [] },
    armor: { 1: [], 2: [], 3: [] }
};

const weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];

/**
 * Initial data fetch
 */
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

        skillsData.forEach(function (s) {
            if (s.name && s.ranks) {
                skillMaxLevels[s.name] = s.ranks.length;
            }
        });

        decorationsData.forEach(function (d) {
            for (let lvl = d.slot; lvl <= 3; lvl++) {
                if (decoCache[d.kind] && decoCache[d.kind][lvl]) {
                    decoCache[d.kind][lvl].push(d);
                }
            }
        });

        dataLoaded = true;
        console.log("Forge Data Loaded Successfully");
    } catch (e) {
        console.error("Critical error loading build data:", e);
    }
}

/* --- Current Build State --- */
let build = {
    weapon1: null, weapon2: null, head: null, chest: null,
    arms: null, waist: null, legs: null, charm: null
};

let decorations = {
    weapon1: [], weapon2: [], head: [], chest: [],
    arms: [], waist: [], legs: [], charm: []
};

let activeSlot = null;
let activeDecoIndex = null;
let modalMode = null;
let currentList = [];

/* --- Utilities --- */

function getName(item) {
    if (!item) return "— Select Piece —";
    return item.name || item.weaponName || item.charmName || "Unnamed";
}

function extractSkills(item) {
    if (!item) return [];
    if (item.skill && item.level) return [{ name: item.skill.name, level: item.level }];
    if (Array.isArray(item.skills)) {
        return item.skills.map(function (s) { return { name: s.skill.name, level: s.level }; });
    }
    return [];
}

/* --- UI Rendering --- */

function updateSelected() {
    for (const slot in build) {
        const nameElement = document.getElementById(slot + "_name");
        if (nameElement) nameElement.textContent = getName(build[slot]);
        renderSlots(slot);
    }
    renderSkillTotals();
    syncWeaponTags();
}

/**
 * Renderizado de slots: El HOVER ahora es específico del nombre
 */
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

    item.slots.forEach(function (slotLevel, index) {
        const deco = decorations[slot][index];
        const row = document.createElement("div");

        // El contenedor ahora es estático (sin hover general)
        row.className = "flex items-center justify-between p-2 rounded-xl border mb-1.5 " +
            (deco ? 'bg-[#6B8E23]/5 border-[#6B8E23]/30' : 'bg-gray-50 border-dashed border-gray-300');

        const decoName = deco ? deco.name : "Empty Slot (Lv" + slotLevel + ")";

        // El hover se aplica SOLO al div que envuelve el icono y el texto
        const textWrapperClass = "flex items-center gap-3 cursor-pointer transition-all duration-200 hover:translate-x-1 group";
        const textStyle = deco
            ? "text-[#2F2F2F] group-hover:text-black"
            : "text-[#2F2F2F]/40 italic group-hover:text-[#6B8E23] group-hover:not-italic";

        row.innerHTML = `
            <div class="${textWrapperClass}" onclick="event.stopPropagation(); selectDecoration('${slot}', ${index}, ${slotLevel})">
                <div class="w-5 h-5 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[9px] font-black text-[#6B8E23] bg-white shadow-sm">
                    ${slotLevel}
                </div>
                <span class="text-xs font-bold ${textStyle} transition-colors tracking-tight">
                    ${decoName}
                </span>
            </div>
        `;

        if (deco) {
            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-all";
            deleteBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>';
            deleteBtn.onclick = function (e) {
                e.stopPropagation();
                clearSlot(slot, index);
            };
            row.appendChild(deleteBtn);
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
    for (const slot in build) {
        const item = build[slot];
        if (!item) continue;

        extractSkills(item).forEach(function (s) {
            totals[s.name] = (totals[s.name] || 0) + s.level;
        });

        if (decorations[slot]) {
            decorations[slot].forEach(function (deco) {
                if (deco && deco.skills) {
                    deco.skills.forEach(function (ds) {
                        totals[ds.skill.name] = (totals[ds.skill.name] || 0) + ds.level;
                    });
                }
            });
        }
    }

    const box = document.getElementById("skillTotals");
    let html = "";

    Object.keys(totals).sort().forEach(function (name) {
        const lvl = totals[name];
        const max = skillMaxLevels[name] || 5;
        const cappedLvl = Math.min(lvl, max);
        const percent = (cappedLvl / max) * 100;

        const skillInfo = skillsData.find(function (s) { return s.name === name; });
        const desc = (skillInfo && skillInfo.ranks && skillInfo.ranks[cappedLvl - 1])
            ? skillInfo.ranks[cappedLvl - 1].description
            : "No description available.";

        html += `
            <div class="mb-5 border-b border-[#6B8E23]/10 pb-4">
                <div class="flex justify-between items-end mb-1">
                    <span class="font-black uppercase text-[11px] text-[#2F2F2F] tracking-wider">${name}</span>
                    <span class="text-[#6B8E23] font-black text-xs">Lv ${cappedLvl}/${max}</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-[#6B8E23] transition-all duration-700 ease-out" style="width: ${percent}%"></div>
                </div>
                <p class="text-[10px] leading-tight text-[#2F2F2F] font-bold uppercase opacity-80">${desc}</p>
            </div>`;
    });

    box.innerHTML = html || '<p class="italic text-sm opacity-50 text-center py-10 font-bold uppercase">No Skills Detected</p>';
}

/* --- Modal & Selection --- */

function openSelector(slot) {
    if (!dataLoaded) return;
    activeSlot = slot;
    modalMode = "piece";

    let list = [];
    if (slot.includes("weapon")) list = weapons;
    else if (slot === "charm") list = charms;
    else list = armors.filter(function (a) { return a.kind === slot; });

    currentList = list;
    renderList(list);
    openModal();
}

function selectDecoration(slot, index, slotLevel) {
    activeSlot = slot;
    activeDecoIndex = index;
    modalMode = "decoration";

    const type = slot.includes("weapon") ? "weapon" : "armor";
    currentList = decoCache[type][slotLevel];
    renderList(currentList);
    openModal();
}

function renderList(list) {
    const container = document.getElementById("modalList");
    container.innerHTML = "";

    list.forEach(function (item) {
        const div = document.createElement("div");
        // Hover específico para el contenido del modal
        div.className = "p-4 mb-2 bg-white border border-[#6B8E23]/10 rounded-2xl cursor-pointer transition-all duration-200 group hover:border-[#6B8E23] hover:bg-[#6B8E23]/5";

        const skillsHtml = extractSkills(item).map(function (s) {
            return `<span class="text-[9px] font-bold text-[#6B8E23] bg-[#6B8E23]/10 px-2 py-0.5 rounded-full mr-1 inline-block uppercase">◈ ${s.name}</span>`;
        }).join("");

        div.innerHTML = `
            <div class="text-[#2F2F2F] font-black text-sm uppercase tracking-tight group-hover:translate-x-1 transition-transform group-hover:text-black">
                ${getName(item)}
            </div>
            <div class="mt-2 flex flex-wrap gap-1 opacity-70 group-hover:opacity-100 transition-opacity">${skillsHtml}</div>
        `;

        div.onclick = function () {
            if (modalMode === "piece") {
                build[activeSlot] = item;
                decorations[activeSlot] = new Array(item.slots ? item.slots.length : 0).fill(null);
            } else {
                decorations[activeSlot][activeDecoIndex] = item;
            }
            updateSelected();
            closeModal();
        };
        container.appendChild(div);
    });
}

document.getElementById("searchInput").addEventListener("input", function () {
    const term = this.value.toLowerCase().trim();
    renderList(currentList.filter(function (item) {
        return getName(item).toLowerCase().includes(term);
    }));
});

function openModal() {
    document.getElementById("modal").classList.remove("hidden");
    document.getElementById("searchInput").value = "";
    document.getElementById("searchInput").focus();
}

function closeModal() {
    document.getElementById("modal").classList.add("hidden");
}

function syncWeaponTags() {
    const container = document.getElementById('tagContainer');
    if (!container) return;

    const weaponMapping = {
        'great-sword': 'Great Sword', 'long-sword': 'Long Sword', 'sword-and-shield': 'Sword and Shield',
        'dual-blades': 'Dual Blades', 'hunting-horn': 'Hunting Horn', 'switch-axe': 'Switch Axe',
        'charge-blade': 'Charge Blade', 'insect-glaive': 'Insect Glaive', 'light-bowgun': 'Light Bowgun',
        'heavy-bowgun': 'Heavy Bowgun', 'bow': 'Bow', 'hammer': 'Hammer', 'lance': 'Lance', 'gunlance': 'Gunlance'
    };

    const equippedTypes = [];
    if (build.weapon1 && build.weapon1.type) equippedTypes.push(weaponMapping[build.weapon1.type] || build.weapon1.type);
    if (build.weapon2 && build.weapon2.type) equippedTypes.push(weaponMapping[build.weapon2.type] || build.weapon2.type);

    const checkboxes = container.querySelectorAll('input[name="tags[]"]');
    checkboxes.forEach(function (checkbox) {
        const tagName = checkbox.getAttribute('data-name');
        if (weaponNames.includes(tagName)) {
            checkbox.checked = equippedTypes.includes(tagName);
            const labelText = checkbox.nextElementSibling;
            if (checkbox.checked) {
                labelText && labelText.classList.add('text-[#6B8E23]', 'font-bold');
            } else {
                labelText && labelText.classList.remove('text-[#6B8E23]', 'font-bold');
            }
        }
    });
}

document.getElementById('forgeForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const buildName = document.getElementById('buildName').value.trim();
    if (!buildName) {
        alert("Please assign a name to your build.");
        return;
    }

    document.getElementById('buildDataInput').value = JSON.stringify(build);
    document.getElementById('decoDataInput').value = JSON.stringify(decorations);

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url || (window.location.origin + '/builds/' + data.slug);
            } else {
                alert("Forge error: " + (data.error || "Unknown error occurred."));
            }
        })
        .catch(err => {
            console.error("Submission error:", err);
            alert("The forge is offline.");
        });
});

loadBuildData();