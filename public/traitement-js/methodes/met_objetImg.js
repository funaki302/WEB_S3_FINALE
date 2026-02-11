async function getAllImg(id_objet) {
    const img = await fetch(`/api/get/img/${id_objet}`);
    if (!img.ok) {
        alert("Echec de get img "+id_objet); 
        return null;
    }

    const data = await img.json();
    return data;
}