// Función para marcar el tag de arma automáticamente
function syncWeaponTag(weaponSlug) {
    // 1. Desmarcar todos los tags de armas primero (para que solo haya uno)
    document.querySelectorAll('#weapon-tags-hidden input').forEach(cb => cb.checked = false);

    // 2. Buscar el checkbox que coincide con el arma seleccionada
    // weaponSlug debe ser algo como 'great-sword'
    const tagCheckbox = document.querySelector(`#weapon-tags-hidden input[data-weapon-name="${weaponSlug}"]`);

    if (tagCheckbox) {
        tagCheckbox.checked = true;
        console.log(`Tag automático asignado: ${weaponSlug}`);
    }
}

// Ejemplo de uso cuando el usuario cambia el arma en el JSON/Selector:
// document.getElementById('weapon-select').addEventListener('change', (e) => {
//    syncWeaponTag(e.target.value);
// });