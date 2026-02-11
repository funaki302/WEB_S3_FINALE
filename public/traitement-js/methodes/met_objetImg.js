async function getAllImg(id_objet) {
    const img = await fetch(`/api/get/img/${id_objet}`);
    if (!img.ok) {
        alert("Echec de get img "+id_objet); 
        return null;
    }

    const data = await img.json();
    return data;
}

async function uploadImage(id_objet, imageFile) {
    console.log('uploadImage appelé avec:', id_objet, imageFile);
    
    if (!imageFile) {
        console.error('Aucun fichier fourni');
        throw new Error('Aucun fichier fourni');
    }
    
    const formData = new FormData();
    formData.append('id_objet', id_objet);
    formData.append('image', imageFile);
    
    console.log('FormData créé, envoi en cours...');

    const response = await fetch('/api/objet/upload-image', {
        method: 'POST',
        body: formData
    });

    console.log('Response status:', response.status);
    console.log('Response ok:', response.ok);

    if (!response.ok) {
        const errorText = await response.text();
        console.error('Erreur response:', errorText);
        throw new Error('Erreur lors de l\'upload de l\'image: ' + errorText);
    }

    const result = await response.json();
    console.log('Upload result:', result);
    return result;
}