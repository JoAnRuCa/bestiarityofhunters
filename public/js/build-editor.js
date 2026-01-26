/* ============================================================
   BUILD EDITOR — FULL LOGIC
   ============================================================ */

const weapons = BUILD_DATA.weapons;
const armors = BUILD_DATA.armors;
const charms = BUILD_DATA.charms;
const decorationsData = BUILD_DATA.decorations;
const skillsData = BUILD_DATA.skills;

/* ------------------------------------------------------------
   1. Prepare skill dictionaries
   ------------------------------------------------------------ */

const skillMaxLevels = {};
const skillDescriptions = {};

skillsData.forEach(s => {
    if (s.name && Array.isArray(s.ranks)) {
        skillMaxLevels[s.name] = s.ranks.length;
        skillDescriptions[s.name] = {};
        s.ranks.forEach(r => {
            skillDescriptions[s.name][r.level] = r.description;
        });
    }
});

/* ------------------------------------------------------------
   2. Build state
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
   3. Helpers
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
   4. Skill calculation
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
   5. Update UI
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
   6. Slot rendering
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
   7. Decoration selection
   ------------------------------------------------------------ */

function selectDecoration(slot, index, slotLevel) {
    activeSlot = slot;
    activeDecoIndex = index;
    activeDecoLevel = slotLevel;

    modalMode = "decoration";
    document.getElementById("modalTitle").textContent = "Select Decoration";

    const type = (slot === "weapon1" || slot === "weapon2") ? "weapon" : "armor";

    currentList = decorationsData.filter(d =>
        d.kind === type && d.slot <= slotLevel
    );

    document.getElementById("searchInput").value = "";
    renderList(currentList);
}

/* ------------------------------------------------------------
   8. Modal
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
   9. Render list (items or decorations)
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
   10. Search filter (name + skill name)
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
   11. OPEN SELECTOR WHEN CLICKING A SLOT
   ------------------------------------------------------------ */

function openSelector(slot) {
    console.log("openSelector ejecutado para:", slot);

    activeSlot = slot;
    modalMode = "piece";

    // Título del modal
    document.getElementById("modalTitle").textContent = "Select " + slot;

    // Determinar lista según el slot
    let list = [];

    if (slot === "weapon1" || slot === "weapon2") {
        list = weapons;
    } else if (slot === "charm") {
        list = charms;
    } else {
        list = armors.filter(a => a.kind === slot);
    }

    // Guardar lista actual
    currentList = list;

    // Resetear búsqueda
    document.getElementById("searchInput").value = "";

    // Renderizar lista y abrir modal
    renderList(list);
}

