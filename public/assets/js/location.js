const regions = {
    "Bourgogne-Franche-Comté": {
        "58": "Nièvre",
        "21": "Côte-d'Or",
        "71": "Saône-et-Loire",
        "89": "Yonne",
        "25": "Doubs",
        "39": "Jura",
        "70": "Haute-Saône",
        "90": "Territoire de Belfort"
    },
    "Auvergne-Rhône-Alpes": {
        "03": "Allier",
        "63": "Puy-de-Dôme",
        "42": "Loire",
        "69": "Rhône"
    },
    "Île-de-France": {
        "75": "Paris",
        "77": "Seine-et-Marne",
        "78": "Yvelines",
        "91": "Essonne",
        "92": "Hauts-de-Seine",
        "93": "Seine-Saint-Denis",
        "94": "Val-de-Marne",
        "95": "Val-d'Oise"
    }
};

const villes = {
    "58": [
        "Nevers",
        "Cosne-Cours-sur-Loire",
        "Decize",
        "Fourchambault",
        "Varennes-Vauzelles",
        "La Charité-sur-Loire",
        "Imphy",
        "Clamecy",
        "Marzy",
        "Saint-Léger-des-Vignes"
    ],
    "21": ["Dijon", "Beaune", "Chenôve", "Talant"],
    "71": ["Mâcon", "Chalon-sur-Saône", "Autun", "Le Creusot"],
    "89": ["Auxerre", "Sens", "Joigny", "Avallon"]
};

const codesPostaux = {
    "Nevers": "58000",
    "Cosne-Cours-sur-Loire": "58200",
    "Decize": "58300",
    "Fourchambault": "58600",
    "Varennes-Vauzelles": "58640",
    "La Charité-sur-Loire": "58400",
    "Imphy": "58160",
    "Clamecy": "58500",
    "Marzy": "58180"
};

document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region');
    if (regionSelect) {
        Object.keys(regions).forEach(region => {
            const option = new Option(region, region);
            regionSelect.add(option);
        });

        if (typeof currentRegion !== 'undefined') {
            regionSelect.value = currentRegion;
            updateDepartements();
        }
    }
});

function updateDepartements() {
    const regionSelect = document.getElementById('region');
    const departementSelect = document.getElementById('departement');
    const selectedRegion = regionSelect.value;
    
    departementSelect.innerHTML = '<option value="">Sélectionner un département</option>';
    
    if (selectedRegion && regions[selectedRegion]) {
        Object.entries(regions[selectedRegion]).forEach(([code, nom]) => {
            departementSelect.innerHTML += `<option value="${code}">${nom} (${code})</option>`;
        });
    }
    
    document.getElementById('ville').innerHTML = '<option value="">Sélectionner d\'abord un département</option>';
    document.getElementById('code_postal').value = '';
}

function updateVilles() {
    const departementSelect = document.getElementById('departement');
    const villeSelect = document.getElementById('ville');
    const selectedDepartement = departementSelect.value;
    
    villeSelect.innerHTML = '<option value="">Sélectionner une ville</option>';
    
    if (selectedDepartement && villes[selectedDepartement]) {
        villes[selectedDepartement].forEach(ville => {
            villeSelect.innerHTML += `<option value="${ville}">${ville}</option>`;
        });
    }
}

function updateCodePostal() {
    const villeSelect = document.getElementById('ville');
    const codePostalInput = document.getElementById('code_postal');
    const selectedVille = villeSelect.value;
    
    if (codesPostaux[selectedVille]) {
        codePostalInput.value = codesPostaux[selectedVille];
    } else {
        codePostalInput.value = '';
    }
}