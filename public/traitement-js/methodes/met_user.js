async function getUserById(id_user) {
    const user = await fetch(`/api/get/user/${id_user}`);
    if (!user.ok) {
        alert("Echec de getUserById "+id_user); 
        return null;
    }

    const data = await user.json();
    return data;
}

async function getAllUsers() {
    const response = await fetch('/api/get/users');
    if (!response.ok) {
        alert('Echec de getAllUsers');
        return [];
    }
    const data = await response.json();
    return Array.isArray(data) ? data : [];
}