async function getAllCategorie() {
    const categories = await fetch('/api/getAll/categorie');
    if (!categories.ok) {
        alert("Erreur lors de la recuperation des categories");
    }

    const data = await categories.json();
    return data;
}

async function add(data) {
    const url = buildUrl("/api/add/categorie");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Echec de ADD Categorie");
        return false;
    }
    return true;
}

async function delet(id_categorie) {
    const categories = await fetch(`/api/delete/conversations/${id_categorie}`);
    if (!categories.ok) {
        alert("Echec de delete categories "+id_categorie); 
        return false;
    }

    alert("Categories "+id_categorie+" supprimer!"); 
    return true;
}

async function update(data) {
    const url = buildUrl("/api/update/categorie");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Update Categorie non reussi");
        return false;
    }
    return true;
}