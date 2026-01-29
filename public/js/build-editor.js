/* ============================================================
   BUILD EDITOR — OPTIMIZED VERSION (NO CONSOLE LOGS)
   ============================================================ */

/* ------------------------------------------------------------
   0. Load data via AJAX (only once)
   ------------------------------------------------------------ */

let weapons = [];
let armors = [];
let charms = [];
let decorationsData = [];
let skillsData = [];
let dataLoaded = false;

let skillMaxLevels = {};
let skillDescriptions = {};
let decoCache = { weapon: { 1: [], 2: [], 3: [] }, armor: { 1: [], 2: [], 3: [] } };

async function loadBuildData() {
    const res = await fetch('api/build-data');
    const data = await res.json();

    weapons = data.weapons;
    armors = data.armors;
    charms = data.charms;
    decorationsData = data.decorations;
    skillsData = data.skills;

    prepareSkillDictionaries();
    prepareDecoCache();

    dataLoaded = true;
}

/* ------------------------------------------------------------
   1. Prepare skill dictionaries
   ------------------------------------------------------------ */

function prepareSkillDictionaries() {
    skillMaxLevels = {};
    skillDescriptions = {};

    skillsData.forEach(s => {
        if (s.name && Array.isArray(s.ranks)) {
            skillMaxLevels[s.name] = s.ranks.length;
            skillDescriptions[s.name] = {};

            s.ranks.forEach(r => {
                skillDescriptions[s.name][r.level] = r.description;
            });
        }
    });
}

/* ------------------------------------------------------------
   2. Pre-cache decorations by level
   ------------------------------------------------------------ */

function prepareDecoCache() {
    decorationsData.forEach(d => {
        for (let lvl = d.slot; lvl <= 3; lvl++) {
            decoCache[d.kind][lvl].push(d);
        }
    });
}

/* ------------------------------------------------------------
   3. Build state
   ------------------------------------------------------------ */

let build = {
    weapon1: null,
    weapon2: null,
    head: null,
    chest: null,
    arms: null,
    waist: null,
    legs: null,
    charm: null
};

let decorations = {
    weapon1: [],
    weapon2: [],
    head: [],
    chest: [],
    arms: [],
    waist: [],
    legs: [],
    charm: []
};

let activeSlot = null;
let activeDecoIndex = null;
let activeDecoLevel = null;
let modalMode = null;
let currentList = [];

/* ------------------------------------------------------------
   4. Helpers
   ------------------------------------------------------------ */

function getName(item) {
    if (!item) return "—";
    return item.name ?? item.weaponName ?? item.charmName ?? item.title ?? "Unnamed";
}

function extractSkills(item) {
    if (!item) return [];

    if (item.skill && item.level) {
        return [{ name: item.skill.name, level: item.level }];
    }

    if (Array.isArray(item.skills)) {
        return item.skills.map(s => ({
            name: s.skill.name,
            level: s.level
        }));
    }

    return [];
}

/* ------------------------------------------------------------
   5. Skill calculation
   ------------------------------------------------------------ */

function calculateTotalSkills() {
    const totals = {};

    for (const slot in build) {
        if (slot === "weapon2") continue;

        const item = build[slot];
        const skills = extractSkills(item);

        skills.forEach(s => {
            if (!totals[s.name]) totals[s.name] = 0;
            totals[s.name] += s.level;
        });

        if (decorations[slot]) {
            decorations[slot].forEach(deco => {
                if (!deco) return;
                deco.skills.forEach(s => {
                    if (!totals[s.skill.name]) totals[s.skill.name] = 0;
                    totals[s.skill.name] += s.level;
                });
            });
        }
    }

    for (const name in totals) {
        if (skillMaxLevels[name]) {
            totals[name] = Math.min(totals[name], skillMaxLevels[name]);
        }
    }

    return totals;
}

function renderSkillTotals() {
    const totals = calculateTotalSkills();
    const box = document.getElementById("skillTotals");

    if (Object.keys(totals).length === 0) {
        box.textContent = "—";
        return;
    }

    let html = "";
    for (const [name, lvl] of Object.entries(totals)) {
        html += `<strong>${name}: ${lvl}</strong><br>`;
        const desc = skillDescriptions[name]?.[lvl];
        html += `<span style="margin-left:10px;">→ ${desc ?? "No description available"}</span><br><br>`;
    }
    box.innerHTML = html;
}

/* ------------------------------------------------------------
   6. Update UI
   ------------------------------------------------------------ */

function updateSelected() {
    for (const slot in build) {
        const span = document.getElementById(slot);
        if (span) span.textContent = getName(build[slot]);
        renderSlots(slot);
    }
    renderSkillTotals();
}

function clearSlot(slot) {
    build[slot] = null;
    decorations[slot] = [];
    updateSelected();
}

/* ------------------------------------------------------------
   7. Slot rendering
   ------------------------------------------------------------ */

function renderSlots(slot) {
    const item = build[slot];
    const containerId = slot + "_slots";

    let container = document.getElementById(containerId);
    if (!container) {
        container = document.createElement("div");
        container.id = containerId;
        document.getElementById(slot).parentNode.appendChild(container);
    }

    container.innerHTML = "";

    if (!item || !item.slots || item.slots.length === 0) {
        container.innerHTML = "<em>No slots</em>";
        decorations[slot] = [];
        return;
    }

    if (!decorations[slot] || decorations[slot].length !== item.slots.length) {
        decorations[slot] = Array(item.slots.length).fill(null);
    }

    item.slots.forEach((slotLevel, index) => {
        const deco = decorations[slot][index];
        const name = deco ? deco.name : "Empty";

        const div = document.createElement("div");
        div.innerHTML = `Slot ${index + 1} (Lv${slotLevel}): <button onclick="selectDecoration('${slot}', ${index}, ${slotLevel})">${name}</button>`;
        container.appendChild(div);
    });
}

/* ------------------------------------------------------------
   8. Decoration selection
   ------------------------------------------------------------ */

function selectDecoration(slot, index, slotLevel) {
    activeSlot = slot;
    activeDecoIndex = index;
    activeDecoLevel = slotLevel;

    modalMode = "decoration";
    document.getElementById("modalTitle").textContent = "Select Decoration";

    const type = (slot === "weapon1" || slot === "weapon2") ? "weapon" : "armor";

    currentList = decoCache[type][slotLevel];

    document.getElementById("searchInput").value = "";
    renderList(currentList);
}

/* ------------------------------------------------------------
   9. Modal
   ------------------------------------------------------------ */

function openModal() {
    document.getElementById("modal").style.display = "flex";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

document.getElementById("modal").addEventListener("click", function (e) {
    if (e.target === this) closeModal();
});

/* ------------------------------------------------------------
   10. Render list
   ------------------------------------------------------------ */

function renderList(list) {
    const container = document.getElementById("modalList");
    container.innerHTML = "";

    list.forEach(item => {
        const div = document.createElement("div");
        div.textContent = getName(item);
        div.style.cursor = "pointer";
        div.style.padding = "4px";
        div.style.borderBottom = "1px solid #ccc";

        div.addEventListener("click", () => {

            if (modalMode === "piece") {
                build[activeSlot] = item;
                decorations[activeSlot] = [];
                updateSelected();
                closeModal();
                return;
            }

            if (modalMode === "decoration") {
                decorations[activeSlot][activeDecoIndex] = item;
                updateSelected();
                closeModal();
                return;
            }
        });

        container.appendChild(div);
    });

    openModal();
}

/* ------------------------------------------------------------
   11. Search filter
   ------------------------------------------------------------ */

document.getElementById("searchInput").addEventListener("input", function () {
    const term = this.value.toLowerCase();

    const filtered = currentList.filter(item => {
        if (getName(item).toLowerCase().includes(term)) return true;

        if (item.skills && Array.isArray(item.skills)) {
            for (const s of item.skills) {
                const skillName = s.skill?.name?.toLowerCase() ?? "";
                if (skillName.includes(term)) return true;
            }
        }

        return false;
    });

    renderList(filtered);
});

/* ------------------------------------------------------------
   12. OPEN SELECTOR — NOW INSTANT (NO AJAX)
   ------------------------------------------------------------ */

function openSelector(slot) {
    if (!dataLoaded) return;

    activeSlot = slot;
    modalMode = "piece";

    document.getElementById("modalTitle").textContent = "Select " + slot;

    let list = [];

    if (slot === "weapon1" || slot === "weapon2") {
        list = weapons;
    } else if (slot === "charm") {
        list = charms;
    } else {
        list = armors.filter(a => a.kind === slot);
    }

    currentList = list;

    document.getElementById("searchInput").value = "";
    renderList(list);
}

/* ------------------------------------------------------------
   13. SAVE BUILD (AJAX)
   ------------------------------------------------------------ */

async function saveBuild() {
    const res = await fetch('api/save-build', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ build, decorations })
    });

    await res.json();
    alert("Build guardado correctamente");
}

/* ------------------------------------------------------------
   14. INIT
   ------------------------------------------------------------ */

loadBuildData();
