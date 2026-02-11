/* ============================================================
   BUILD ARCHITECT — CORE ENGINE
   ============================================================ */

// Data Stores
let weapons = [];
let armors = [];
let charms = [];
let decorationsData = [];
let skillsData = [];
let dataLoaded = false;

// Dictionaries for fast lookup
let skillMaxLevels = {};
let decoCache = {
    weapon: { 1: [], 2: [], 3: [] },
    armor: { 1: [], 2: [], 3: [] }
};

/**
 * Initial data fetch from the API
 */
async function loadBuildData() {
    try {
        const res = await fetch('api/build-data');
        const data = await res.json();

        weapons = data.weapons;
        armors = data.armors;
        charms = data.charms;
        decorationsData = data.decorations;
        skillsData = data.skills;

        // Map max levels for progress bars
        skillsData.forEach(s => {
            if (s.name && s.ranks) {
                skillMaxLevels[s.name] = s.ranks.length;
            }
        });

        // Cache decorations by slot level and type (kind)
        decorationsData.forEach(d => {
            for (let lvl = d.slot; lvl <= 3; lvl++) {
                if (decoCache[d.kind] && decoCache[d.kind][lvl]) {
                    decoCache[d.kind][lvl].push(d);
                }
            }
        });

        dataLoaded = true;
        console.log("Forge data loaded successfully.");
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
let modalMode = null; // 'piece' or 'decoration'
let currentList = [];

/* --- Utilities --- */

function getName(item) {
    if (!item) return "— Select Piece —";
    return item.name ?? item.weaponName ?? item.charmName ?? "Unnamed";
}

function extractSkills(item) {
    if (!item) return [];
    // Handle single skill objects (like charms)
    if (item.skill && item.level) return [{ name: item.skill.name, level: item.level }];
    // Handle armor skill arrays
    if (Array.isArray(item.skills)) {
        return item.skills.map(s => ({ name: s.skill.name, level: s.level }));
    }
    return [];
}

/* --- UI Rendering --- */

/**
 * Main update function to sync the UI with the 'build' state
 */
function updateSelected() {
    for (const slot in build) {
        const nameElement = document.getElementById(slot + "_name");
        if (nameElement) {
            nameElement.textContent = getName(build[slot]);
        }
        renderSlots(slot);
    }
    renderSkillTotals();
}

/**
 * Renders the decoration slots for a specific equipment piece
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

    item.slots.forEach((slotLevel, index) => {
        const deco = decorations[slot][index];
        const row = document.createElement("div");

        // Use 'deco-row' class for CSS-based hover (background & border)
        row.className = `deco-row flex items-center justify-between p-2 rounded-xl border border-dashed mb-1.5 cursor-pointer transition-all duration-200
            ${deco ? 'bg-[#6B8E23]/5 border-[#6B8E23]/30' : 'bg-gray-50 border-gray-200'}`;

        row.onclick = (e) => {
            e.stopPropagation();
            selectDecoration(slot, index, slotLevel);
        };

        const decoName = deco ? deco.name : `Empty Slot (Lv${slotLevel})`;
        // Use 'deco-text' class for CSS-based hover (color & opacity)
        const textStyle = deco ? "text-[#2F2F2F]" : "text-[#2F2F2F]/40 italic";

        row.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-5 h-5 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[9px] font-black text-[#6B8E23] bg-white shadow-sm">
                    ${slotLevel}
                </div>
                <span class="deco-text text-xs font-bold ${textStyle} transition-colors">
                    ${decoName}
                </span>
            </div>
        `;

        if (deco) {
            const deleteBtn = document.createElement("button");
            deleteBtn.className = "delete-btn text-gray-400 p-1.5 rounded-md transition-all";
            deleteBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>`;
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                clearSlot(slot, index);
            };
            row.appendChild(deleteBtn);
        }

        container.appendChild(row);
    });
}

function clearSlot(slot, index = null) {
    if (index === null) {
        build[slot] = null;
        decorations[slot] = [];
    } else {
        decorations[slot][index] = null;
    }
    updateSelected();
}

/**
 * Calculates and renders the cumulative skills of the build
 */
function renderSkillTotals() {
    const totals = {};

    // 1. Gather skills from pieces and decorations
    for (const slot in build) {
        const item = build[slot];
        if (!item) continue;

        extractSkills(item).forEach(s => {
            totals[s.name] = (totals[s.name] || 0) + s.level;
        });

        if (decorations[slot]) {
            decorations[slot].forEach(deco => {
                if (deco && deco.skills) {
                    deco.skills.forEach(ds => {
                        totals[ds.skill.name] = (totals[ds.skill.name] || 0) + ds.level;
                    });
                }
            });
        }
    }

    const box = document.getElementById("skillTotals");
    let html = "";

    // 2. Generate HTML with progress bars
    for (const [name, lvl] of Object.entries(totals)) {
        const max = skillMaxLevels[name] || 5;
        const cappedLvl = Math.min(lvl, max);
        const percent = (cappedLvl / max) * 100;

        html += `
            <div class="mb-4">
                <div class="flex justify-between items-end mb-1">
                    <span class="font-black uppercase text-[11px] tracking-wider">${name}</span>
                    <span class="text-[#6B8E23] font-black text-xs">Lv ${cappedLvl}/${max}</span>
                </div>
                <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: ${percent}%"></div>
                </div>
            </div>`;
    }

    box.innerHTML = html || `<p class="italic text-sm opacity-50 text-center py-10 font-bold uppercase">No Skills Detected</p>`;
}

/* --- Modal Logic --- */

function openSelector(slot) {
    if (!dataLoaded) return;
    activeSlot = slot;
    modalMode = "piece";
    document.getElementById("modalTitle").textContent = "Select " + slot;

    let list = [];
    if (slot.includes("weapon")) list = weapons;
    else if (slot === "charm") list = charms;
    else list = armors.filter(a => a.kind === slot);

    currentList = list;
    renderList(list);
    openModal();
}

function selectDecoration(slot, index, slotLevel) {
    activeSlot = slot;
    activeDecoIndex = index;
    modalMode = "decoration";
    document.getElementById("modalTitle").textContent = "Select Jewel (Lv" + slotLevel + ")";

    const type = slot.includes("weapon") ? "weapon" : "armor";
    currentList = decoCache[type][slotLevel];
    renderList(currentList);
    openModal();
}

function renderList(list) {
    const container = document.getElementById("modalList");
    container.innerHTML = "";

    list.forEach(item => {
        const div = document.createElement("div");
        div.className = "p-3 mb-2 bg-white border border-[#6B8E23]/10 rounded-xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 cursor-pointer transition-all shadow-sm";

        const skillsHtml = extractSkills(item).map(s =>
            `<span class="text-[9px] font-bold text-[#C67C48] bg-[#C67C48]/5 px-2 py-0.5 rounded-full mr-1 inline-block">◈ ${s.name}</span>`
        ).join("");

        div.innerHTML = `
            <div class="text-[#2F2F2F] font-bold text-sm">${getName(item)}</div>
            <div class="mt-1 flex flex-wrap">${skillsHtml}</div>
        `;

        div.onclick = () => {
            if (modalMode === "piece") {
                build[activeSlot] = item;
                // Initialize deco array based on item's slots
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

// Live Search
document.getElementById("searchInput").addEventListener("input", function () {
    const term = this.value.toLowerCase().trim();
    const filtered = currentList.filter(item => getName(item).toLowerCase().includes(term));
    renderList(filtered);
});

/* --- UI Controls --- */

function openModal() {
    document.getElementById("modal").classList.remove("hidden");
    document.getElementById("searchInput").value = "";
    document.getElementById("searchInput").focus();
}

function closeModal() {
    document.getElementById("modal").classList.add("hidden");
}

async function saveBuild() {
    try {
        const res = await fetch('api/save-build', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ build, decorations })
        });
        if (res.ok) alert("Build stored in the forge!");
    } catch (e) {
        alert("The forge is cold. Error saving build.");
    }
}

// Boot
loadBuildData();