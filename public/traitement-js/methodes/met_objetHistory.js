async function getAllHistory(id_objet) {
    const history = await fetch(`/api/get/objethistory/${id_objet}`);
    if (!history.ok) {
        alert("Echec de getAll history "+id_objet); 
        return null;
    }

    const data = await history.json();
    return data;
}