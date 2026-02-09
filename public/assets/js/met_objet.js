async function getAllObjet() {
    const objets = await fetch('/api/getAll/objet');
    if (!objets.ok) {
        alert("Erreur lors de la recuperation des objets");
    }

    const data = await objets.json();
    return data;
}

async function add(data) {
    const url = buildUrl("/api/add/objet");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Echec de ADD Objet");
        return false;
    }
    return true;
}

async function delet(id_objet) {
    const objet = await fetch(`/api/delete/objet/${id_objet}`);
    if (!objet.ok) {
        alert("Echec de delete objet "+id_objet); 
        return false;
    }

    alert("Objet "+id_objet+" supprimer!"); 
    return true;
}

async function update(data) {
    const url = buildUrl("/api/update/objet");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Update Objet non reussi");
        return false;
    }
    return true;
}